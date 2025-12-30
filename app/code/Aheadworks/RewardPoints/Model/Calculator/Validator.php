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

namespace Aheadworks\RewardPoints\Model\Calculator;

use Magento\Quote\Api\Data\CartInterface;

class Validator
{
    /**
     * Can apply sales rules
     *
     * @param CartInterface $quote
     * @return bool
     * @throws \Zend_Db_Select_Exception
     */
    public function canApplySalesRules(CartInterface $quote): bool
    {
        foreach ($quote->getAllItems() as $item) {
            if ($item->getExtensionAttributes()->getDiscounts() || (int)$item->getDiscountAmount() > 0) return true;
        }

        return false;
    }
}
