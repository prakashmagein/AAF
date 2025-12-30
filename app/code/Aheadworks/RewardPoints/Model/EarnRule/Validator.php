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
namespace Aheadworks\RewardPoints\Model\EarnRule;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\RewardPoints\Model\StorefrontLabelsEntity\Validator as StorefrontLabelsEntityValidator;
use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;

/**
 * Class Validator
 *
 * @package Aheadworks\RewardPoints\Model\EarnRule
 */
class Validator extends AbstractValidator
{
    /**
     * @var StorefrontLabelsEntityValidator
     */
    private $storefrontLabelsEntityValidator;

    /**
     * @param StorefrontLabelsEntityValidator $storefrontLabelsEntityValidator
     */
    public function __construct(
        StorefrontLabelsEntityValidator $storefrontLabelsEntityValidator
    ) {
        $this->storefrontLabelsEntityValidator = $storefrontLabelsEntityValidator;
    }

    /**
     * Returns true if and only if earn rule entity meets the validation requirements
     *
     * @param EarnRuleInterface $earnRule
     * @return bool
     */
    public function isValid($earnRule)
    {
        $this->_clearMessages();

        if (!$this->storefrontLabelsEntityValidator->isValid($earnRule)) {
            $this->_addMessages($this->storefrontLabelsEntityValidator->getMessages());
            return false;
        }

        return true;
    }
}
