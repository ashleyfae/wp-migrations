<?php
/**
 * MigrationGroup.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   GPL2+
 */

namespace Ashleyfae\MigrationsWP;

class MigrationGroup implements \Ashleyfae\MigrationsWP\Contracts\MigrationGroup
{

    public function getSlug(): string
    {
        return 'af_migrations';
    }

    public function getName(): string
    {
        return 'WordPress';
    }
}
