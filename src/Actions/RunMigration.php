<?php
/**
 * RunMigration.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace Ashleyfae\MigrationsWP\Actions;

use Ashleyfae\AppWP\App;
use Ashleyfae\MigrationsWP\Contracts\Migration;
use Ashleyfae\MigrationsWP\MigrationRepository;
use Ashleyfae\MigrationsWP\Exceptions\ModelNotFoundException;

class RunMigration
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
     * @throws \Ashleyfae\MigrationsWP\Exceptions\ModelNotFoundException|\Exception
     */
    public function execute(Migration $migration): void
    {
        $this->migration = $migration;

        global $wpdb;

        $migrationModel = \Ashleyfae\MigrationsWP\Models\Migration::fromMigrationClass($this->migration);

        $wpdb->query("START TRANSACTION");

        try {
            $this->migration->up();
            $migrationModel->status = \Ashleyfae\MigrationsWP\Models\Migration::STATUS_SUCCESS;
            $migrationModel->save();
            $wpdb->query("COMMIT");
        } catch (\Exception $e) {
            $wpdb->query("ROLLBACK");
            $migrationModel->status = \Ashleyfae\MigrationsWP\Models\Migration::STATUS_FAILED;
            $migrationModel->error  = json_encode($e);

            trigger_error(sprintf(
                'Migration failure: %s',
                $e->getMessage()
            ));

            try {
                $migrationModel->save();
            } catch (\Exception $e) {
                trigger_error(sprintf(
                    'Failed to save migration error. Message: %s',
                    $e->getMessage()
                ));
            }


            throw $e;
        }
    }

}
