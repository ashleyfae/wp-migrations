<?php
/**
 * Registry.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\Migrations\Helpers;

use AshleyFae\Migrations\Migrations\CreateMigrationsTable;

class Registry
{

    private $items = [];

    /**
     * @throws \Exception
     */
    public function offsetGet($key)
    {
        if (! $this->offsetExists($key)) {
            throw new \Exception('The item does not exist.');
        }

        return $this->items[$key];
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getKeys(): array
    {
        return array_keys($this->items);
    }

    public function offsetExists($key): bool
    {
        return array_key_exists($key, $this->items);
    }

    public function offsetSet($key, $value): void
    {
        $this->items[$key] = $value;
    }

    public function offsetUnset($key): void
    {
        unset($this->items[$key]);
    }

}
