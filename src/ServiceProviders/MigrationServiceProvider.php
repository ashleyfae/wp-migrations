<?php
/**
 * MigrationServiceProvider.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\Migrations\ServiceProviders;

use AshleyFae\App\App;
use AshleyFae\App\Helpers\Hooks;
use AshleyFae\App\ServiceProviders\ServiceProvider;
use AshleyFae\Migrations\Commands\Command;
use AshleyFae\Migrations\Commands\Migrate;
use AshleyFae\Migrations\Commands\MigrateRollback;
use AshleyFae\Migrations\MigrationRegistry;
use AshleyFae\Migrations\MigrationRunner;
use AshleyFae\Migrations\Migrations\CreateMigrationsTable;

class MigrationServiceProvider implements ServiceProvider
{

    public function register(): void
    {
        App::getInstance()->singleton(MigrationRunner::class);
        App::getInstance()->singleton(MigrationRegistry::class);
    }

    public function boot(): void
    {
        App::getInstance()->make(MigrationRegistry::class)
            ->addMigration('af_migrations', CreateMigrationsTable::class);
        Hooks::addAction('admin_init', MigrationRunner::class, 'run', 0);

        if (defined('WP_CLI') && WP_CLI) {
            $this->registerCommands();
        }
    }

    private function registerCommands(): void
    {
        $commands = [
            Migrate::class,
            MigrateRollback::class,
        ];

        foreach ($commands as $command) {
            /** @var Command $command */
            $command = App::getInstance()->make($command);

            \WP_CLI::add_command('af '.$command::command(), $command);
        }
    }
}
