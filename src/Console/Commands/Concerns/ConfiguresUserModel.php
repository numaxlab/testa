<?php

namespace Testa\Console\Commands\Concerns;

use function Laravel\Prompts\confirm;

trait ConfiguresUserModel
{
    protected function configureUserModel(): void
    {
        $userPath = app_path('Models/User.php');

        if (! file_exists($userPath)) {
            $this->components->error('User model not found at '.$userPath);

            return;
        }

        $content = file_get_contents($userPath);

        if (str_contains($content, 'LunarGeslibUser')) {
            $this->components->info('User model already configured with Lunar traits.');

            return;
        }

        if (! confirm('Configure User model with Lunar traits and interface?', true)) {
            return;
        }

        $useStatements = <<<'PHP'
            use Lunar\Base\Traits\LunarUser;
            use Lunar\Base\LunarUser as LunarUserInterface;
            use NumaxLab\Lunar\Geslib\Traits\LunarGeslibUser;
            PHP;

        // Add use statements after namespace
        $content = preg_replace(
            '/(namespace App\\\\Models;)/',
            "$1\n\n".$useStatements,
            $content,
        );

        // Add interface implementation
        $content = preg_replace(
            '/(class User extends Authenticatable)(?!\s+implements)/',
            '$1 implements LunarUserInterface',
            $content,
        );

        // If already implements something, add the interface
        $content = preg_replace(
            '/(class User extends Authenticatable implements )([^\{]+)/',
            '$1LunarUserInterface, $2',
            $content,
        );

        // Add traits inside the class
        if (preg_match('/(class User[^{]*\{)(\s*)(use [^;]+;)?/', $content, $matches)) {
            $traits = "use LunarUser;\n    use LunarGeslibUser;";

            if (isset($matches[3]) && ! empty($matches[3])) {
                // There's already a use statement for traits, add after it
                $content = preg_replace(
                    '/(class User[^{]*\{)(\s*)(use [^;]+;)/',
                    "$1$2$3\n    ".$traits,
                    $content,
                );
            } else {
                // No existing traits, add after opening brace
                $content = preg_replace(
                    '/(class User[^{]*\{)/',
                    "$1\n    ".$traits."\n",
                    $content,
                );
            }
        }

        file_put_contents($userPath, $content);

        $this->components->info('User model configured with Lunar traits and interface.');
    }
}
