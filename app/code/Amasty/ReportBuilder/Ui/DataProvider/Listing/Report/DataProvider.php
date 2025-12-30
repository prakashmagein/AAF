<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Ui\DataProvider\Listing\Report;

use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Magento\Store\Model\Store;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getField() == ReportInterface::STORE_IDS && $filter->getValue() !== Store::DEFAULT_STORE_ID) {
            $filter->setConditionType('in');
            $filter->setValue(sprintf('%s,%s', $filter->getValue(), Store::DEFAULT_STORE_ID));
        }

        return parent::addFilter($filter);
    }
}
