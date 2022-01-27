<?php
/**
 * MigrationGroup.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 * @since     1.0
 */

namespace Ashleyfae\MigrationsWP\Contracts;

interface MigrationGroup
{

    /**
     * Unique slug (ID) for this group.
     *
     * @since 1.0
     *
     * @return string
     */
    public function getSlug(): string;

    /**
     * Display name for this group.
     *
     * @since 1.0
     *
     * @return string
     */
    public function getName(): string;

}
