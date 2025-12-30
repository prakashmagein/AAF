<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\OrderEdit\Plugin\Backend\Magento\Sales\Block\Adminhtml\Order\View;

use Magento\Sales\Block\Adminhtml\Order\View\Items as Subject;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection;
use Magefan\OrderEdit\Model\Order\UpdateOrderItems;

class Items
{

    /**
     * @param Subject $subject
     * @param $result
     * @return mixed
     */
    public function afterGetItemsCollection(Subject $subject, $result)
    {
        if ($result instanceof Collection) {
            $this->removeItemsWithSkipFlag($result);
        }

        return $result;
    }

    /**
     * @param $items
     * @return void
     */
    private function removeItemsWithSkipFlag($items): void
    {
        foreach ($items as $k => $i) {
            if (UpdateOrderItems::SKIP_PARENT_ITEM_ID == $i->getParentItemId()) {
                $items->removeItemByKey($k);
            }
        }
    }
}
