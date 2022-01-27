<?php
/**
 * MigrationRunner.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Ashleyfae\MigrationsWP;

use Ashleyfae\MigrationsWP\Actions\RunMigration;
use Ashleyfae\MigrationsWP\Contracts\Migration;

class MigrationRunner
{

    /**
     * @var MigrationRepository
     */
    private $migrationRepository;

    /**
     * @var MigrationRegistry
     */
    private $migrationRegistry;

    /**
     * @var array
     */
    private $completedMigrations;

    /**
     * @var RunMigration
     */
    private $runMigration;

    public function __construct(
        MigrationRepository $migrationRepository,
        MigrationRegistry $migrationRegistry,
        RunMigration $runMigration
    ) {
        $this->migrationRepository = $migrationRepository;
        $this->migrationRegistry   = $migrationRegistry;
        $this->completedMigrations = $this->migrationRepository->getCompletedMigrationIds();
        $this->runMigration        = $runMigration;
    }

    public function run(): void
    {
        if (! $this->hasMigrationToRun()) {
            return;
        }

        foreach ($this->migrationRegistry->getMigrations() as $migration) {
            if (in_array($migration::id(), $this->completedMigrations, true)) {
                continue;
            }

            try {
                $this->runMigration->execute($migration);
            } catch (\Exception $e) {
                break;
            }
        }
    }

    public function hasMigrationToRun(): bool
    {
        return (bool) array_diff($this->migrationRegistry->getKeys(), $this->completedMigrations);
    }

}
