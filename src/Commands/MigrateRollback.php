<?php
/**
 * MigrateRollback.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\Migrations\Commands;

use AshleyFae\Migrations\Actions\RollbackMigration;
use AshleyFae\Migrations\Contracts\Migration;
use AshleyFae\Migrations\Exceptions\ModelNotFoundException;

class MigrateRollback extends Command
{

    public static function command(): string
    {
        return 'migrate:rollback';
    }

    public function __invoke(array $args = [], array $assoc_args = []): void
    {
        if (! isset($args[0])) {
            \WP_CLI::error("Specify a migration to roll back.");
            return;
        }

        $migrationId = $args[0];

        if (! $this->hasRunMigration($migrationId)) {
            \WP_CLI::error("The {$migrationId} migration never completed successfully.");
            return;
        }

        try {
            /** @var Migration $migration */
            $className = $this->migrationRegistry->offsetGet($migrationId);
            $migration = new $className;
            $runner = new RollbackMigration($migration);
            $runner->execute();

            \WP_CLI::success('Rollback successful.');
        } catch (\Exception $e) {
            \WP_CLI::error($e->getMessage());
        }
    }

    protected function hasRunMigration(string $migrationId): bool
    {
        try {
            $migration = $this->migrationRepository->getById($migrationId);

            return $migration->status === \AshleyFae\Migrations\Models\Migration::STATUS_SUCCESS;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

}
