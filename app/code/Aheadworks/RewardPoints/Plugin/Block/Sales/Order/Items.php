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

namespace Aheadworks\RewardPoints\Plugin\Block\Sales\Order;

use Magento\Bundle\Model\Product\Type as BundleProduct;
use Aheadworks\RewardPoints\Model\Config;
use Magento\Framework\Exception\LocalizedException;

class Items
{
    /**
     * Add reward points column after discount
     *
     * @param \Magento\Sales\Block\Adminhtml\Order\View\Items $subject
     * @param \Closure $proceed
     * @return array
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetColumns(
        \Magento\Sales\Block\Adminhtml\Order\View\Items $subject,
        \Closure $proceed
    ) {
        $columns = $proceed();
        foreach ($subject->getOrder()->getAllItems() as $orderItem) {
            if ($orderItem->getProductType() == BundleProduct::TYPE_CODE) {
                return $columns;
            }
        }
        $newColumns = [];
        foreach ($columns as $key => $column) {
            $newColumns[$key] = $column;
            if ($key == 'discont') {
                $newColumns['aw-reward-points'] = __(Config::DEFAULT_LABEL_NAME);
            }
        }
        return $newColumns;
    }
}
