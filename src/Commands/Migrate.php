<?php
/**
 * Migrate.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Ashleyfae\MigrationsWP\Commands;

use Ashleyfae\MigrationsWP\Actions\RunMigration;
use Ashleyfae\MigrationsWP\Contracts\Migration;
use Ashleyfae\MigrationsWP\Exceptions\ModelNotFoundException;
use Ashleyfae\MigrationsWP\MigrationRegistry;
use Ashleyfae\MigrationsWP\MigrationRepository;

class Migrate extends Command
{
    /**
     * @var RunMigration
     */
    protected $runner;

    public function __construct(
        MigrationRegistry $migrationRegistry,
        MigrationRepository $migrationRepository,
        RunMigration $runner
    ) {
        parent::__construct($migrationRegistry, $migrationRepository);
        $this->runner = $runner;
    }

    public static function command(): string
    {
        return 'migrate';
    }

    public function __invoke(array $args = [], array $assoc_args = []): void
    {
        if (! empty($args[0])) {
            $this->runMigration($args[0], $assoc_args['force'] ?? false);
        } else {
            $this->runAllMigrations();
        }
    }

    protected function runMigration(string $migrationId, bool $force): void
    {
        if ($this->hasRunMigration($migrationId) && ! $force) {
            \WP_CLI::error("The {$migrationId} migration has already been completed. Set the --force flag to proceed regardless.");
            return;
        }

        try {
            $this->runner->execute($this->migrationRegistry->getMigration($migrationId));

            \WP_CLI::line('Migration successful.');
        } catch (\Exception $e) {
            \WP_CLI::warning($e->getMessage());
        }
    }

    protected function runAllMigrations(): void
    {
        $completedMigrations = $this->migrationRepository->getCompletedMigrationIds();

        if (! (bool) array_diff($this->migrationRegistry->getKeys(), $completedMigrations)) {
            \WP_CLI::line('No migrations to run.');
            return;
        }

        foreach ($this->migrationRegistry->getMigrations() as $migration) {
            if (in_array($migration::id(), $completedMigrations, true)) {
                continue;
            }

            \WP_CLI::line(sprintf('Migrating: %s', $migration::id()));

            $this->runMigration($migration::id(), false);
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
