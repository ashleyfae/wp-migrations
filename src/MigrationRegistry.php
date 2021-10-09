<?php
/**
 * MigrationRegistry.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\Migrations;

use AshleyFae\Migrations\Contracts\Migration;
use AshleyFae\Migrations\Helpers\Registry;

class MigrationRegistry extends Registry
{

    public function getMigrations(string $groupId = null): array
    {
        $sortedMigrations = [];
        foreach ($this->getItems() as $item) {
            if ($groupId && ($item['group'] ?? null) !== $groupId) {
                continue;
            }

            /** @var Migration $migrationClass */
            $migrationClass = $item['class'];

            $sortedMigrations[$migrationClass::timestamp().'_'.$migrationClass::id()] = $migrationClass;
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

}
