<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\OrderEdit\Plugin\Backend\Magento\Sales\Block\Adminhtml\Order\View;

use Magento\Sales\Block\Adminhtml\Order\View\History as Subject;

class History
{
    /**
     * @param Subject $subject
     * @param $result
     * @return string
     */
    public function afterToHtml(Subject $subject, $result)
    {
        $result .= $subject->getChildHtml('editing_history');
        return $result;
    }
}
