<?php

namespace Testa\Console\Commands\Concerns;

use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\confirm;

trait InstallsNpmPackages
{
    protected function installNpmPackages(): void
    {
        if ($this->option('skip-npm')) {
            $this->components->info('Skipping npm package installation.');

            return;
        }

        if (! confirm('Install required npm packages (@numaxlab/atomic, @tailwindcss/typography)?', true)) {
            return;
        }

        $packages = '@numaxlab/atomic@^1.0.0 @tailwindcss/typography';

        $command = $this->detectPackageManager($packages);

        $this->components->info("Running: {$command}");

        $result = Process::path(base_path())->run($command);

        if ($result->successful()) {
            $this->components->info('NPM packages installed successfully.');
        } else {
            $this->components->error('Failed to install npm packages: '.$result->errorOutput());
        }
    }

    protected function detectPackageManager(string $packages): string
    {
        if (file_exists(base_path('pnpm-lock.yaml'))) {
            return "pnpm add {$packages}";
        }

        if (file_exists(base_path('yarn.lock'))) {
            return "yarn add {$packages}";
        }

        if (file_exists(base_path('bun.lockb')) || file_exists(base_path('bun.lock'))) {
            return "bun add {$packages}";
        }

        return "npm install {$packages}";
    }
}
