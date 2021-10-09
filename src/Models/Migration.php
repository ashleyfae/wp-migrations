<?php
/**
 * Migration.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\Migrations\Models;

use AshleyFae\Migrations\MigrationRepository;

class Migration
{

    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_PENDING = 'pending';

    /**
     * @var string
     */
    public $id = null;

    /**
     * @var string
     */
    public $group = null;

    /**
     * @var string
     */
    public $status = 'pending';

    /**
     * @var string|null
     */
    public $error = null;

    /**
     * @var string|null
     */
    public $last_run = null;

    public function __construct(array $args)
    {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function save(): void
    {
        (new MigrationRepository())->save($this);
    }

}
