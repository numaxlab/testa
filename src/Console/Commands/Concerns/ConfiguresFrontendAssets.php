<?php

namespace Testa\Console\Commands\Concerns;

use function Laravel\Prompts\confirm;

trait ConfiguresFrontendAssets
{
    protected function configureFrontendAssets(): void
    {
        if ($this->option('skip-frontend')) {
            $this->components->info('Skipping frontend assets configuration.');

            return;
        }

        $this->configureCss();
        $this->configureJs();
    }

    protected function configureCss(): void
    {
        $cssPath = resource_path('css/app.css');
        $stubPath = $this->getStubPath('app-css.stub');

        if (! file_exists($stubPath)) {
            $this->components->error('CSS stub not found at '.$stubPath);

            return;
        }

        $stubContent = file_get_contents($stubPath);

        if (file_exists($cssPath)) {
            $existingContent = file_get_contents($cssPath);

            if (str_contains($existingContent, '@numaxlab/atomic')) {
                $this->components->info('app.css already contains Testa imports.');

                return;
            }

            if (! $this->option('force') && ! confirm('Replace existing app.css with Testa version?', true)) {
                return;
            }
        }

        // Ensure directory exists
        if (! is_dir(dirname($cssPath))) {
            mkdir(dirname($cssPath), 0755, true);
        }

        file_put_contents($cssPath, $stubContent);

        $this->components->info('app.css configured with Testa imports.');
    }

    protected function getStubPath(string $stub): string
    {
        return dirname(__DIR__, 4).'/stubs/'.$stub;
    }

    protected function configureJs(): void
    {
        $jsPath = resource_path('js/app.js');
        $stubPath = $this->getStubPath('app-js.stub');

        if (! file_exists($stubPath)) {
            $this->components->error('JS stub not found at '.$stubPath);

            return;
        }

        $stubContent = file_get_contents($stubPath);

        if (file_exists($jsPath)) {
            $existingContent = file_get_contents($jsPath);

            if (str_contains($existingContent, '@numaxlab/atomic')) {
                $this->components->info('app.js already contains Testa imports.');

                return;
            }

            if (! $this->option('force')) {
                if (confirm('Append Testa imports to existing app.js?', true)) {
                    $stubContent = $existingContent."\n\n// Testa Alpine.js components\n".$stubContent;
                } elseif (! confirm('Replace existing app.js with Testa version?', false)) {
                    return;
                }
            }
        }

        // Ensure directory exists
        if (! is_dir(dirname($jsPath))) {
            mkdir(dirname($jsPath), 0755, true);
        }

        file_put_contents($jsPath, $stubContent);

        $this->components->info('app.js configured with Testa Alpine.js components.');
    }
}
