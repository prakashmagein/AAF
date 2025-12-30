<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\ResourceModel\Indexer\Eav;

class DecimalType extends AbstractType
{
    protected function _construct()
    {
        $this->_init('amasty_report_builder_eav_index_decimal', 'entity_id');
    }

    protected function getSourceTable(): string
    {
        return $this->getTable('catalog_product_entity_decimal');
    }

    protected function _prepareRelationIndex($parentIds = null): void
    {
        // phpcs:ignore
        return;
    }
}
