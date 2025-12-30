<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\ResourceModel\Indexer\Stock\Strategy;

use Amasty\ReportBuilder\Model\Product\CompositeTypes;
use Amasty\ReportBuilder\Model\ResourceModel\Indexer\Stock\Select\BuilderInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;

class SimpleStrategy implements StrategyInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var CompositeTypes
     */
    private $compositeTypes;

    /**
     * @var BuilderInterface[]
     */
    private $selectBuilders;

    public function __construct(
        ResourceConnection $resourceConnection,
        CompositeTypes $compositeTypes,
        array $selectBuilders
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->compositeTypes = $compositeTypes;
        $this->selectBuilders = $selectBuilders;
    }

    public function filter(Select $select): void
    {
        $select->where('type_id NOT IN (?)', $this->compositeTypes->get());
    }

    public function getSelectBuilders(): array
    {
        return $this->selectBuilders;
    }
}
