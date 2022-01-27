<?php
/**
 * MigrationRegistry.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Ashleyfae\MigrationsWP;

use Ashleyfae\AppWP\App;
use Ashleyfae\MigrationsWP\Contracts\Migration;
use Ashleyfae\MigrationsWP\Helpers\Registry;

class MigrationRegistry extends Registry
{

    /**
     * @param  string|null  $groupId
     *
     * @return Migration[]
     */
    public function getMigrations(string $groupId = null): array
    {
        $sortedMigrations = [];
        foreach ($this->getItems() as $item) {
            if ($groupId && ($item['group'] ?? null) !== $groupId) {
                continue;
            }

            /** @var Migration $migration */
            $migration = App::getInstance()->make($item['class']);

            $sortedMigrations[$migration::timestamp().'_'.$migration::id()] = $migration;
        }

        ksort($sortedMigrations);

        return array_values($sortedMigrations);
    }

    public function addMigration(string $groupId, string $migrationClass): void
    {
        if (! is_subclass_of($migrationClass, Migration::class)) {
            throw new \InvalidArgumentException(sprintf(
                'Migrations must implement the %s interface.',
                Migration::class
            ));
        }

        $migrationId = $migrationClass::id();
        if ($this->offsetExists($migrationId)) {
            throw new \InvalidArgumentException(sprintf(
                'Migration %s is already registered.',
                $migrationId
            ));
        }

        parent::offsetSet($migrationId, [
            'group' => $groupId,
            'class' => $migrationClass,
        ]);
    }

    public function getMigration(string $migrationId): Migration
    {
        $className = $this->offsetGet($migrationId);

        if (! $className) {
            throw new \InvalidArgumentException(sprintf(
                'Class %s does not exist.',
                $className
            ));
        }

        return App::getInstance()->make($className);
    }

}
