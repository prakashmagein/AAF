<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Chart\Axis\Query;

use Amasty\ReportBuilder\Api\Data\AxisInterface;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Axis\Collection;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Axis\CollectionFactory as CollectionFactory;

class GetAxisList implements GetAxisListInterface
{
    /**
     * Array format ['chart_id' => AxisInterface[], ...].
     *
     * @var array
     */
    private $axises = [];

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return AxisInterface[]
     */
    public function execute(int $chartId): array
    {
        if (!isset($this->axises[$chartId])) {
            /** @var Collection $collection */
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter(AxisInterface::CHART_ID, $chartId);
            $this->axises[$chartId] = $collection->getItems();
        }

        return $this->axises[$chartId];
    }
}
