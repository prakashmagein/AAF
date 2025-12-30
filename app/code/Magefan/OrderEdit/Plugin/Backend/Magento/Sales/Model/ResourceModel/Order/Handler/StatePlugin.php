<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\OrderEdit\Plugin\Backend\Magento\Sales\Model\ResourceModel\Order\Handler;

use Magento\Sales\Model\ResourceModel\Order\Handler\State;

class StatePlugin
{
    /**
     * @param State $subject
     * @param callable $proceed
     * @param $object
     * @return State
     */
    public function aroundCheck(State $subject, callable $proceed, $object)
    {
        if ($object->getData('mf_grid_inline_edit') /*&& $object->getData('pre_state') === 'canceled' */) {
            return $subject;
        } else {
            return $proceed($object);
        }
    }
}
