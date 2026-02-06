<?php

namespace Testa\Console\Commands\Concerns;

use function Laravel\Prompts\confirm;

trait ConfiguresLunarSearch
{
    protected function configureLunarSearch(): void
    {
        $configPath = config_path('lunar/search.php');

        if (! file_exists($configPath)) {
            $this->components->warn('Lunar search config not found. Publishing...');
            $this->call('vendor:publish', ['--tag' => 'lunar.search']);
        }

        if (! file_exists($configPath)) {
            $this->components->error('Could not publish Lunar search config.');

            return;
        }

        $content = file_get_contents($configPath);

        if (str_contains($content, 'Testa\\Models\\Page::class')) {
            $this->components->info('Lunar search already configured with Testa models.');

            return;
        }

        if (! confirm('Configure Lunar search with Testa models and indexers?', true)) {
            return;
        }

        // Add Testa models to the models array
        $testaModels = <<<'PHP'
            \Testa\Models\Page::class,
                    \Testa\Models\Course::class,
                    \Testa\Models\Article::class,
            PHP;

        // Find the models array and add Testa models
        $content = preg_replace(
            "/('models'\s*=>\s*\[)([^\]]*?)(\s*\])/s",
            "$1$2\n        ".$testaModels."\n    $3",
            $content,
        );

        // Add/replace indexers
        $indexerComment = "// Lunar\\Search\\ProductIndexer replaced with Geslib ProductIndexer";
        $productIndexer = 'Lunar\\Models\\Product::class => NumaxLab\\Lunar\\Geslib\\Search\\ProductIndexer::class,';
        $authorIndexer = 'NumaxLab\\Lunar\\Geslib\\Models\\Author::class => NumaxLab\\Lunar\\Geslib\\Search\\AuthorIndexer::class,';

        // Check if indexers array exists
        if (preg_match("/['\"]indexers['\"]\s*=>\s*\[/", $content)) {
            // Add to existing indexers array
            if (! str_contains($content, 'ProductIndexer::class')) {
                $content = preg_replace(
                    "/(['\"]indexers['\"]\s*=>\s*\[)([^\]]*?)(\s*\])/s",
                    "$1$2\n        ".$indexerComment."\n        ".$productIndexer."\n        ".$authorIndexer."\n    $3",
                    $content,
                );
            }
        }

        file_put_contents($configPath, $content);

        $this->components->info('Lunar search configured with Testa models and indexers.');
    }
}
