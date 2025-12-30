<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    2.4.0
 * @copyright  Copyright (c) 2024 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RewardPoints\Block\Adminhtml\SpendRule\Edit\Button;

/**
 * Class Delete
 */
class Delete extends AbstractButton
{
    /**
     * Retrieve button-specified settings
     *
     * @return array
     */
    public function getButtonData(): array
    {
        $data = [];
        $ruleId = $this->context->getRequest()->getParam('id');
        if ($ruleId) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => "deleteConfirm('" . __('Are you sure you want to do this?') .
                    "', '" . $this->getUrl('*/*/delete', ['id' => $ruleId]) . "')",
                'sort_order' => 20,
            ];
        }
        return $data;
    }
}
