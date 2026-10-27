<?php

use Illuminate\Console\Application;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Lunar\Models\Product;
use NumaxLab\Lunar\Geslib\Models\Author;
use Testa\Console\Commands\Concerns\ConfiguresLunarSearch;
use Testa\Models\Education\Course;
use Testa\Models\Education\CourseModule;
use Testa\Models\Media\Audio;
use Testa\Models\Media\Video;
use Testa\Models\News\Event;

class ConfigureLunarSearchFixtureCommand extends Command
{
    use ConfiguresLunarSearch;

    protected $signature = 'test:configure-lunar-search {--force}';

    public function handle(): void
    {
        $this->configureLunarSearch();
    }
}

beforeEach(function (): void {
    Application::starting(function ($artisan): void {
        $artisan->resolveCommands([ConfigureLunarSearchFixtureCommand::class]);
    });

    $this->searchConfigDirectory = sys_get_temp_dir().'/testa-search-'.bin2hex(random_bytes(6));
    $this->searchConfigFile = $this->searchConfigDirectory.'/lunar/search.php';

    mkdir($this->searchConfigDirectory.'/lunar', 0777, true);

    file_put_contents($this->searchConfigFile, <<<'PHP'
        <?php

        use Lunar\Models\Brand;
        use Lunar\Models\Collection;
        use Lunar\Models\Product;

        return [
            'models' => [
                Brand::class,
                Collection::class,
                Product::class,
            ],
            'indexers' => [
                Brand::class => \Lunar\Search\BrandIndexer::class,
                Product::class => \Lunar\Search\ProductIndexer::class,
            ],
        ];
        PHP);

    $this->app->useConfigPath($this->searchConfigDirectory);
});

afterEach(function (): void {
    (new Filesystem)->deleteDirectory($this->searchConfigDirectory);
});

it('adds the Testa models and indexers to the Lunar search config', function (): void {
    $this->artisan('test:configure-lunar-search', ['--force' => true])->assertSuccessful();

    $config = require $this->searchConfigFile;

    expect($config['models'])
        ->toContain(Author::class)
        ->toContain(Course::class)
        ->toContain(CourseModule::class)
        ->toContain(Event::class)
        ->toContain(Audio::class)
        ->toContain(Video::class)
        ->and($config['indexers'])
        ->toHaveKey(Author::class)
        ->toHaveKey(Product::class);
});

it('leaves the search config unchanged when the installer runs twice', function (): void {
    $this->artisan('test:configure-lunar-search', ['--force' => true])->assertSuccessful();
    $firstRun = file_get_contents($this->searchConfigFile);

    $this->artisan('test:configure-lunar-search', ['--force' => true])->assertSuccessful();

    expect(file_get_contents($this->searchConfigFile))->toBe($firstRun);

    $modelCounts = array_count_values((require $this->searchConfigFile)['models']);

    expect($modelCounts[Course::class])->toBe(1)
        ->and($modelCounts[Author::class])->toBe(1);
});
