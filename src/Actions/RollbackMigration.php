<?php
/**
 * RollbackMigration.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace Ashleyfae\MigrationsWP\Actions;

use Ashleyfae\MigrationsWP\Contracts\Migration;
use Ashleyfae\MigrationsWP\MigrationRepository;
use Ashleyfae\MigrationsWP\Exceptions\ModelNotFoundException;

class RollbackMigration
{

    /**
     * @var Migration
     */
    protected $migration;

    /**
     * @var MigrationRepository
     */
    protected $migrationRepository;

    public function __construct(MigrationRepository $migrationRepository)
    {
        $this->migrationRepository = $migrationRepository;
    }

    /**
     * @param Migration $migration
     *
     * @throws ModelNotFoundException|\Exception
     */
    public function execute(Migration $migration): void
    {
        $this->migration = $migration;

        global $wpdb;

        $migrationModel = $this->migrationRepository->getById($this->migration::id());

        $wpdb->query("START TRANSACTION");

        try {
            $this->migration->down();
            $migrationModel->status = 'pending';
            $wpdb->query("COMMIT");
        } catch (\Exception $e) {
            $wpdb->query("ROLLBACK");

            trigger_error(sprintf(
                'Migration rollback failure: %s',
                $e->getMessage()
            ));

            throw $e;
        }
    }

}
