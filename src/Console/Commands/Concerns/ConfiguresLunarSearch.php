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

        $testaModels = [
            'NumaxLab\\Lunar\\Geslib\\Models\\Author::class',
            'Testa\\Models\\Education\\Course::class',
            'Testa\\Models\\Education\\CourseModule::class',
            'Testa\\Models\\News\\Event::class',
            'Testa\\Models\\Media\\Audio::class',
            'Testa\\Models\\Media\\Video::class',
        ];

        $missingModels = array_values(array_filter(
            $testaModels,
            fn (string $model): bool => ! str_contains($content, $model),
        ));

        $productIndexer = 'Lunar\\Models\\Product::class => NumaxLab\\Lunar\\Geslib\\Search\\ProductIndexer::class,';
        $authorIndexer = 'NumaxLab\\Lunar\\Geslib\\Models\\Author::class => NumaxLab\\Lunar\\Geslib\\Search\\AuthorIndexer::class,';

        $missingIndexers = array_values(array_filter(
            [$productIndexer, $authorIndexer],
            fn (string $indexer): bool => ! str_contains($content, $indexer),
        ));

        if ($missingModels === [] && $missingIndexers === []) {
            $this->components->info('Lunar search already configured with Testa models.');

            return;
        }

        if (! $this->option('force') && ! confirm('Configure Lunar search with Testa models and indexers?', true)) {
            return;
        }

        if ($missingModels !== []) {
            $modelLines = array_map(
                static fn (string $model): string => $model.',',
                $missingModels,
            );

            $content = preg_replace_callback(
                "/('models'\s*=>\s*\[)([^\]]*?)(\s*\])/s",
                static fn (array $matches): string => $matches[1].$matches[2]."\n        ".implode("\n        ", $modelLines)."\n    ]",
                $content,
                1,
            );
        }

        if ($missingIndexers !== []) {
            $indexerLines = [];

            if (in_array($productIndexer, $missingIndexers, true)) {
                $indexerLines[] = '// Lunar\\Search\\ProductIndexer replaced with Geslib ProductIndexer';
            }

            $indexerLines = [...$indexerLines, ...$missingIndexers];

            $content = preg_replace_callback(
                "/(['\"]indexers['\"]\s*=>\s*\[)([^\]]*?)(\s*\])/s",
                static fn (array $matches): string => $matches[1].$matches[2]."\n        ".implode("\n        ", $indexerLines)."\n    ]",
                $content,
                1,
            );
        }

        file_put_contents($configPath, $content);

        $this->components->info('Lunar search configured with Testa models and indexers.');
    }
}
