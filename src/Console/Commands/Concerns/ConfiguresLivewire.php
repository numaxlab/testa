<?php

namespace Testa\Console\Commands\Concerns;

use function Laravel\Prompts\confirm;

trait ConfiguresLivewire
{
    protected function configureLivewire(): void
    {
        $configPath = config_path('livewire.php');

        if (! file_exists($configPath)) {
            $this->components->warn('Livewire config not found. Publishing...');
            $this->call('vendor:publish', ['--tag' => 'livewire:config']);
        }

        if (! file_exists($configPath)) {
            $this->components->error('Could not publish Livewire config.');

            return;
        }

        $content = file_get_contents($configPath);

        if (str_contains($content, 'testa::components.layouts.app')) {
            $this->components->info('Livewire already configured with Testa layout.');

            return;
        }

        if (! confirm('Configure Livewire layout to use Testa?', true)) {
            return;
        }

        // Replace the layout value
        $content = preg_replace(
            "/(['\"]layout['\"]\s*=>\s*)['\"][^'\"]*['\"]/",
            "$1'testa::components.layouts.app'",
            $content,
        );

        file_put_contents($configPath, $content);

        $this->components->info('Livewire configured with Testa layout.');
    }
}
