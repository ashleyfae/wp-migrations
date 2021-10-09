<?php
/**
 * RunMigration.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\Migrations\Actions;

use AshleyFae\Migrations\Contracts\Migration;
use AshleyFae\Migrations\MigrationRepository;
use AshleyFae\Migrations\ModelNotFoundException;

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

    public function __construct(Migration $migration)
    {
        $this->migration           = $migration;
        $this->migrationRepository = new MigrationRepository();
    }

    /**
     * @throws ModelNotFoundException|\Exception
     */
    public function execute(): void
    {
        global $wpdb;

        $migrationModel = $this->migrationRepository->getById($this->migration::id());

        $wpdb->query("START TRANSACTION");

        try {
            $this->migration->up();
            $migrationModel->status = \AshleyFae\Migrations\Models\Migration::STATUS_SUCCESS;
            $migrationModel->save();
            $wpdb->query("COMMIT");
        } catch (\Exception $e) {
            $wpdb->query("ROLLBACK");
            $migrationModel->status = \AshleyFae\Migrations\Models\Migration::STATUS_FAILED;
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