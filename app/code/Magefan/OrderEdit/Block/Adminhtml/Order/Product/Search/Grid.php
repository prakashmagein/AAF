<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Block\Adminhtml\Order\Product\Search;

class Grid extends \Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid
{
    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl() : string
    {
        return $this->getUrl(
            'sales/order_edit/loadBlock',
            ['block' => 'search_grid', '_current' => true, 'collapse' => null]
        );
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if (class_exists('\Magento\Backend\ViewModel\LimitTotalNumberOfProductsInGrid')) {
            $this->setViewModel(\Magento\Framework\App\ObjectManager::getInstance()
                ->create(\Magento\Backend\ViewModel\LimitTotalNumberOfProductsInGrid::class));
        }

        return parent::toHtml();
    }
}
