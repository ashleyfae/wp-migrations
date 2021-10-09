<?php
/**
 * CreateMigrationsTable.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\Migrations\Migrations;

use AshleyFae\Migrations\Contracts\Migration;
use AshleyFae\Migrations\DatabaseMigrationException;
use AshleyFae\Migrations\MigrationRepository;
use AshleyFae\Migrations\Traits\CreatesTable;

class CreateMigrationsTable implements Migration
{
    use CreatesTable;

    public static function id(): string
    {
        return 'create_migrations_table';
    }

    public static function title(): string
    {
        return 'Creates af_migrations table.';
    }

    public static function timestamp(): int
    {
        return strtotime('1970-01-01 00:00');
    }

    /**
     * @throws DatabaseMigrationException
     */
    public function up(): void
    {
        $sql = "
        id varchar(180) NOT NULL,
        group varchar(180) DEFAULT NULL,
        status varchar(16) NOT NULL DEFAULT 'pending',
        error text DEFAULT NULL,
        last_run datetime NOT NULL,
        PRIMARY KEY (id),
        INDEX status (status)
        INDEX group (group)
        ";

        $this->createTable(MigrationRepository::tableName(), $sql);
    }

    /**
     * @throws DatabaseMigrationException
     */
    public function down(): void
    {
        $this->dropTable(MigrationRepository::tableName());
    }
}
