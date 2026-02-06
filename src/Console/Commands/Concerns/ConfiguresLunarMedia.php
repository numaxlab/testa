<?php

namespace Testa\Console\Commands\Concerns;

use function Laravel\Prompts\confirm;

trait ConfiguresLunarMedia
{
    protected function configureLunarMedia(): void
    {
        $configPath = config_path('lunar/media.php');

        if (! file_exists($configPath)) {
            $this->components->warn('Lunar media config not found. Publishing...');
            $this->call('vendor:publish', ['--tag' => 'lunar.media']);
        }

        if (! file_exists($configPath)) {
            $this->components->error('Could not publish Lunar media config.');

            return;
        }

        $content = file_get_contents($configPath);

        if (str_contains($content, 'Testa\\Media\\StandardMediaDefinitions')) {
            $this->components->info('Lunar media already configured with Testa definitions.');

            return;
        }

        if (! confirm('Configure Lunar media with Testa definitions?', true)) {
            return;
        }

        // Add use statements at the top of the file
        $useStatements = <<<'PHP'
            use NumaxLab\Lunar\Geslib\Media\ProductMediaDefinitions;
            use Testa\Media\StandardMediaDefinitions;
            PHP;

        // Add after opening PHP tag
        $content = preg_replace(
            '/(<\?php\s*)/',
            "$1\n".$useStatements,
            $content,
        );

        // Replace or add product media definition
        if (preg_match("/['\"]product['\"]\s*=>\s*[^,]+,/", $content)) {
            // Replace existing product definition
            $content = preg_replace(
                "/(['\"]product['\"]\s*=>\s*)[^,]+,/",
                "'product' => ProductMediaDefinitions::class,",
                $content,
            );
        }

        // Add additional media definitions
        $additionalDefinitions = <<<'PHP'
            'author' => StandardMediaDefinitions::class,
                    'education-topic' => StandardMediaDefinitions::class,
                    'course' => StandardMediaDefinitions::class,
            PHP;

        // Find the definitions array and add Testa definitions if not present
        if (! str_contains($content, "'author'")) {
            $content = preg_replace(
                "/(['\"]definitions['\"]\s*=>\s*\[)([^\]]*?)(\s*\])/s",
                "$1$2\n        ".$additionalDefinitions."\n    $3",
                $content,
            );
        }

        file_put_contents($configPath, $content);

        $this->components->info('Lunar media configured with Testa definitions.');
    }
}
