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

namespace Aheadworks\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;
use Aheadworks\RewardPoints\Model\SpendRule\CartSpendRule;
use Magento\Framework\Event\Observer;

/**
 * Class SalesOrderPlaceAfter
 */
class SalesOrderPlaceAfter implements ObserverInterface
{
    /**
     * SalesOrderPlaceAfter constructor.
     *
     * @param CartSpendRule $cartSpendRule
     */
    public function __construct(private CartSpendRule $cartSpendRule)
    {
    }

    /**
     * Order place after actions
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $this->cartSpendRule->removeCurrentRuleIds();
    }
}
