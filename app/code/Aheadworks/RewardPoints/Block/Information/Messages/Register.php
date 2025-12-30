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

use Magento\Framework\Exception\LocalizedException;

class Register extends AbstractMessages
{
    /**
     * Can show block or not
     *
     * @return bool
     */
    public function canShow()
    {
        return $this->config->getFrontendIsDisplayInvitationToRegister() && $this->getEarnPoints()
            && !$this->isCustomerLoggedIn();
    }

    /**
     * Retrieve block message
     *
     * @return string
     * @throws LocalizedException
     */
    public function getMessage()
    {
        $earnPoints = $this->getEarnPoints();
        return __(
            'Register now to earn <strong>%1 %2%3</strong>. <a href="%4">Learn more</a>.',
            $earnPoints,
            $this->getLabelNameRewardPoints(),
            $this->getEarnMoneyByPoints($earnPoints),
            $this->getFrontendExplainerPageLink()
        );
    }

    /**
     * Retrieve how much points will be earned
     *
     * @return int
     */
    public function getEarnPoints()
    {
        return $this->config->getAwardedPointsForRegistration();
    }
}
