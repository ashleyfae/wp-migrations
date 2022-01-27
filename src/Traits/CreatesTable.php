<?php
/**
 * CreatesTable.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Ashleyfae\MigrationsWP\Traits;

use Ashleyfae\MigrationsWP\Exceptions\DatabaseMigrationException;
use Ashleyfae\MigrationsWP\Exceptions\DatabaseQueryException;
use Ashleyfae\MigrationsWP\DB;

trait CreatesTable
{

    /**
     * @throws \Ashleyfae\MigrationsWP\Exceptions\DatabaseMigrationException
     */
    protected function createTable(string $table, string $schema): void
    {
        global $wpdb;

        $charset = DB::get_charset_collate();

        try {
            $wpdb->query("CREATE TABLE IF NOT EXISTS {$table} ({$schema}) {$charset}");
        } catch (DatabaseQueryException $e) {
            throw new DatabaseMigrationException(
                "An error occurred while creating the {$table} table.",
                500,
                $e,
                $e->getQueryErrors()
            );
        }
    }

    /**
     * @throws DatabaseMigrationException
     */
    protected function dropTable(string $table): void
    {
        global $wpdb;

        try {
            $wpdb->query("DROP TABLE IF EXISTS {$table}");
        } catch (DatabaseQueryException $e) {
            throw new DatabaseMigrationException("An error occurred while dropping the {$table} table.", 500, $e);
        }
    }


}
