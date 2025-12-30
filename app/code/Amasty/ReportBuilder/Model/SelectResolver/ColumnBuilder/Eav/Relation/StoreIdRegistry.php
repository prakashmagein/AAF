<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder\Eav\Relation;

class StoreIdRegistry
{
    /**
     * @var int|null
     */
    private $storeId;

    /**
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return $this->storeId ?? null;
    }

    /**
     * @param int $storeId
     */
    public function setStoreId(int $storeId): void
    {
        $this->storeId = $storeId;
    }

    /**
     * Reset registry
     */
    public function reset(): void
    {
        $this->storeId = null;
    }
}
