<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn;

use Amasty\ReportBuilder\Api\Data\SelectColumnInterface;
use ArrayAccess;

interface ColumnStorageInterface extends ArrayAccess
{
    /**
     * @param string $columnId
     * @param SelectColumnInterface $selectColumn
     */
    public function addColumn(string $columnId, SelectColumnInterface $selectColumn): void;

    /**
     * @param string $columnId
     *
     * @return SelectColumnInterface|null
     */
    public function getColumnById(string $columnId): ?SelectColumnInterface;

    /**
     * @return SelectColumnInterface[]
     */
    public function getAllColumns(): array;

    /**
     * @param SelectColumnInterface[] $columns
     */
    public function setColumns(array $columns): void;

    /**
     * @param string $alias
     *
     * @return SelectColumnInterface|null
     */
    public function getColumnByAlias(string $alias): ?SelectColumnInterface;
}
