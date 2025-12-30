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
namespace Aheadworks\RewardPoints\Ui\Component\Form\Customer\RewardPointsSection;

use Magento\Framework\View\Element\ComponentVisibilityInterface;

/**
 * Class Tab
 *
 * @package Aheadworks\RewardPoints\Ui\Component\Form\Customer\RewardPointsSection
 */
class Tab extends AclResourceFieldset implements ComponentVisibilityInterface
{
    /**
     * @inheridoc
     */
    public function isComponentVisible(): bool
    {
        $customerId = $this->context->getRequestParam('id');
        return (bool)$customerId
            && parent::isComponentVisible();
    }
}
