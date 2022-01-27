<?php
/**
 * CreateMigrationsTable.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Ashleyfae\MigrationsWP\Migrations;

use Ashleyfae\MigrationsWP\Contracts\Migration;
use Ashleyfae\MigrationsWP\Exceptions\DatabaseMigrationException;
use Ashleyfae\MigrationsWP\MigrationGroup;
use Ashleyfae\MigrationsWP\MigrationRepository;
use Ashleyfae\MigrationsWP\Traits\CreatesTable;

class CreateMigrationsTable implements Migration
{
    use CreatesTable;

    public static function id(): string
    {
        return 'create_migrations_table';
    }

    public static function group(): \Ashleyfae\MigrationsWP\Contracts\MigrationGroup
    {
        return new MigrationGroup();
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
        group_id varchar(180) DEFAULT NULL,
        status varchar(16) NOT NULL DEFAULT 'pending',
        total_steps bigint(20) unsigned DEFAULT NULL,
        next_step bigint(20) unsigned NOT NULL DEFAULT 1,
        error text DEFAULT NULL,
        last_run datetime NOT NULL,
        PRIMARY KEY (id),
        INDEX group_status (group_id, status)
        ";

        $this->createTable(MigrationRepository::tableName(), $sql);
    }

    /**
     * @throws \Ashleyfae\MigrationsWP\Exceptions\DatabaseMigrationException
     */
    public function down(): void
    {
        $this->dropTable(MigrationRepository::tableName());
    }
}
