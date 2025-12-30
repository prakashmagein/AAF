<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn;

use Amasty\ReportBuilder\Api\Data\SelectColumnInterface;

class ColumnStorage implements ColumnStorageInterface
{
    /**
     * @var SelectColumnInterface[]
     */
    private $columns = [];

    public function addColumn(string $columnId, SelectColumnInterface $selectColumn): void
    {
        $this->columns[$columnId] = $selectColumn;
    }

    public function getColumnById(string $columnId): ?SelectColumnInterface
    {
        return $this->columns[$columnId] ?? null;
    }

    public function getAllColumns(): array
    {
        return $this->columns;
    }

    public function setColumns(array $columns): void
    {
        foreach ($columns as $columnId => $columnConfig) {
            $this->addColumn($columnId, $columnConfig);
        }
    }

    public function remove(string $columnId): void
    {
        unset($this->columns[$columnId]);
    }

    public function getColumnByAlias(string $alias): ?SelectColumnInterface
    {
        foreach ($this->columns as $column) {
            if ($column->getAlias() === $alias) {
                return $column;
            }
        }

        return null;
    }

    public function clear(): void
    {
        $this->columns = [];
    }

    /**
     * The value to set.
     *
     * @param string $offset
     * @param string $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->columns[] = $value;
        } else {
            $this->columns[$offset] = $value;
        }
    }

    /**
     * The return value will be casted to boolean if non-boolean was returned.
     *
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->columns[$offset]);
    }

    /**
     * The offset to unset.
     *
     * @param string $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->columns[$offset]);
    }

    /**
     * The offset to retrieve.
     *
     * @param string $offset
     *
     * @return SelectColumnInterface|null
     */
    public function offsetGet($offset): ?SelectColumnInterface
    {
        return $this->columns[$offset] ?? null;
    }

    /**
     * Return Select Columns data in array format.
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->columns as $key => $value) {
            $data[$key] = $value->__toArray();
        }

        return $data;
    }
}
