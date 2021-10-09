<?php
/**
 * Migration.php
 *
 * @package   wp-migration
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\Migrations\Contracts;

interface Migration
{

    /**
     * Returns a unique ID for this migration.
     *
     * @return string
     */
    public static function id(): string;

    /**
     * Returns a display-friendly title for this migragion.
     *
     * @return string
     */
    public static function title(): string;

    /**
     * Unix timestamp for when the migration was created.
     *
     * @return int
     */
    public static function timestamp(): int;

    /**
     * Runs the migration to modify the database.
     */
    public function up(): void;

    /**
     * Reverts the migration.
     */
    public function down(): void;

}
