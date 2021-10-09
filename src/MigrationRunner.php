<?php
/**
 * MigrationRunner.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\Migrations;

use AshleyFae\Migrations\Contracts\Migration;

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

    public function __construct(
        MigrationRepository $migrationRepository,
        MigrationRegistry $migrationRegistry
    ) {
        $this->migrationRepository = $migrationRepository;
        $this->migrationRegistry   = $migrationRegistry;
        $this->completedMigrations = $this->migrationRepository->getCompletedMigrationIds();
    }

    public function run(): void
    {
        if (! $this->hasMigrationToRun()) {
            return;
        }

        foreach ($this->migrationRegistry->getMigrations() as $migrationClass) {
            $migrationId = $migrationClass::id();

            if (in_array($migrationId, $this->completedMigrations, true)) {
                continue;
            }

            /** @var Migration $migration */
            $migration = new $migrationClass;
            $runner    = new RunMigration($migration);

            try {
                $runner->execute();
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
