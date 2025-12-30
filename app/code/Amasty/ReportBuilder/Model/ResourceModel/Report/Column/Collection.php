<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\ResourceModel\Report\Column;

use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Model\Report\Column as Model;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Column as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @method \Amasty\ReportBuilder\Model\Report\Column[] getItems()
 * @method \Amasty\ReportBuilder\Model\Report\Column getItemById($idValue)
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'amasty_report_builder_column_collection';

    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }

    public function filterByReport(int $reportId): void
    {
        $this->addFieldToFilter(ReportColumnInterface::REPORT_ID, $reportId);
    }

    /**
     * @return string[]
     */
    public function getIds(): array
    {
        return array_keys($this->getItems());
    }
}
