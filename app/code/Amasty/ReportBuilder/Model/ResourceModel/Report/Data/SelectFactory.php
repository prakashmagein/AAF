<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\ResourceModel\Report\Data;

use Amasty\ReportBuilder\Model\ResourceModel\Report\Data as DataResource;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select\SelectRenderer;

class SelectFactory
{
    /**
     * @var SelectRenderer
     */
    private $selectRenderer;

    /**
     * @var AdapterInterface
     */
    private $adapter;

    public function __construct(
        DataResource $resource,
        SelectRenderer $selectRenderer
    ) {
        $this->adapter = $resource->getConnection();
        $this->selectRenderer = $selectRenderer;
    }

    public function create(): Select
    {
        return new Select($this->adapter, $this->selectRenderer);
    }
}
