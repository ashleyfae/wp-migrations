<?php
/**
 * Command.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\Migrations\Commands;

use AshleyFae\Migrations\MigrationRegistry;
use AshleyFae\Migrations\MigrationRepository;

abstract class Command
{

    /**
     * @var MigrationRegistry
     */
    protected $migrationRegistry;

    /**
     * @var MigrationRepository
     */
    protected $migrationRepository;

    public function __construct(
        MigrationRegistry $migrationRegistry,
        MigrationRepository $migrationRepository
    ) {
        $this->migrationRegistry   = $migrationRegistry;
        $this->migrationRepository = $migrationRepository;
    }

    abstract public static function command(): string;

    abstract public function __invoke(array $args = [], array $assoc_args = []): void;

}
