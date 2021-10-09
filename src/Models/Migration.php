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
    public $group_id = null;

    /**
     * @var string
     */
    public $status = self::STATUS_PENDING;

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

    public static function fromMigrationClass(\AshleyFae\Migrations\Contracts\Migration $migration)
    {
        return new self([
            'id'       => $migration::id(),
            'group_id' => $migration::groupId(),
        ]);
    }

}
