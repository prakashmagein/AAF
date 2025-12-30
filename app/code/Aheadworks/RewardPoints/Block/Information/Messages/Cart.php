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

namespace Aheadworks\RewardPoints\Block\Information\Messages;

use Aheadworks\RewardPoints\Model\Calculator\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Cart extends AbstractMessages
{
    /**
     * Can show block or not
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Db_Select_Exception
     */
    public function canShow()
    {
        return $this->getEarnPoints() > 0;
    }

    /**
     * Retrieve block message
     *
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Db_Select_Exception
     */
    public function getMessage()
    {
        $earnPoints = $this->getEarnPoints();
        $message = __(
            'Checkout now to earn <strong>%1 %2%3</strong> for your order.',
            $earnPoints,
            $this->getLabelNameRewardPoints(),
            $this->getEarnMoneyByPoints($earnPoints)
        );

        if (!$this->isCustomerLoggedIn()) {
            $message .= ' ' . __(
                'This amount can vary after logging in. <a href="%1">Learn more</a>.',
                $this->getFrontendExplainerPageLink()
            );
        }

        return $message;
    }

    /**
     * Retrieve how much points will be earned
     *
     * @return int
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Db_Select_Exception
     */
    public function getEarnPoints()
    {
        /** @var ResultInterface $calculationResult */
        $calculationResult = $this->earningCalculator->calculationByQuote(
            $this->checkoutSession->getQuote(),
            (int)$this->getCustomerId()
        );
        return $calculationResult->getPoints();
    }
}
