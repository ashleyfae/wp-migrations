<?php
/**
 * MigrationRepository.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\Migrations;

use AshleyFae\Migrations\Exceptions\ModelNotFoundException;
use AshleyFae\Migrations\Models\Migration;

/**
 * @property string $tableName
 */
class MigrationRepository
{
    public function __get($property)
    {
        if ($property === 'tableName') {
            return self::tableName();
        }

        return property_exists($this, $property) ? $this->{$property} : null;
    }

    public static function tableName(): string
    {
        global $wpdb;

        return $wpdb->prefix.'af_migrations';
    }

    /**
     * @throws ModelNotFoundException
     */
    public function getById(string $id): Migration
    {
        $migration = DB::get_row(
            DB::prepare("SELECT * FROM {$this->tableName} WHERE id = %s", $id)
        );

        if (empty($migration)) {
            throw new ModelNotFoundException();
        }

        return new Migration($migration);
    }

    public function getCompletedMigrationIds(string $group = null): array
    {
        try {
            $groupWhere = $group ? DB::prepare("AND group = %s", $group) : '';

            return DB::get_col(DB::prepare(
                "SELECT id FROM {$this->tableName} WHERE status = %s {$groupWhere}",
                Migration::STATUS_SUCCESS
            ));
        } catch (\Exception $e) {
            return [];
        }
    }

    public function save(Migration $migration): void
    {
        $query = "
        INSERT INTO {$this->tableName} (id, group, status, error, last_run)
        values(%s, %s, %s, %s, NOW())
        ON DUPLICATE KEY UPDATE
        group = %s,
        status = %s,
        error = %s,
        last_run = NOW()
        ";

        DB::query(
            DB::prepare(
                $query,
                $migration->id,
                $migration->group,
                $migration->status,
                $migration->error,
                $migration->group,
                $migration->status,
                $migration->error
            )
        );
    }

}
