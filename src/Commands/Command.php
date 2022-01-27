<?php
/**
 * Command.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Ashleyfae\MigrationsWP\Commands;

use Ashleyfae\MigrationsWP\MigrationRegistry;
use Ashleyfae\MigrationsWP\MigrationRepository;

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
