<?php
/**
 * MigrateRollback.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace Ashleyfae\MigrationsWP\Commands;

use Ashleyfae\MigrationsWP\Actions\RollbackMigration;
use Ashleyfae\MigrationsWP\Contracts\Migration;
use Ashleyfae\MigrationsWP\Exceptions\ModelNotFoundException;
use Ashleyfae\MigrationsWP\MigrationRegistry;
use Ashleyfae\MigrationsWP\MigrationRepository;

class MigrateRollback extends Command
{
    /**
     * @var RollbackMigration
     */
    protected $rollbackRunner;

    public function __construct(
        MigrationRegistry $migrationRegistry,
        MigrationRepository $migrationRepository,
        RollbackMigration $rollbackMigration
    ) {
        parent::__construct($migrationRegistry, $migrationRepository);

        $this->rollbackRunner = $rollbackMigration;
    }

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
            $this->rollbackRunner->execute(
                $this->migrationRegistry->getMigration($migrationId)
            );

            \WP_CLI::success('Rollback successful.');
        } catch (\Exception $e) {
            \WP_CLI::error($e->getMessage());
        }
    }

    protected function hasRunMigration(string $migrationId): bool
    {
        try {
            $migration = $this->migrationRepository->getById($migrationId);

            return $migration->status === \Ashleyfae\MigrationsWP\Models\Migration::STATUS_SUCCESS;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

}
