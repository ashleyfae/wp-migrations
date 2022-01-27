<?php
/**
 * MigrationServiceProvider.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Ashleyfae\MigrationsWP\ServiceProviders;

use Ashleyfae\AppWP\App;
use Ashleyfae\AppWP\Helpers\Hooks;
use Ashleyfae\AppWP\ServiceProviders\ServiceProvider;
use Ashleyfae\MigrationsWP\Commands\Command;
use Ashleyfae\MigrationsWP\Commands\Migrate;
use Ashleyfae\MigrationsWP\Commands\MigrateRollback;
use Ashleyfae\MigrationsWP\MigrationRegistry;
use Ashleyfae\MigrationsWP\MigrationRunner;
use Ashleyfae\MigrationsWP\Migrations\CreateMigrationsTable;

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
