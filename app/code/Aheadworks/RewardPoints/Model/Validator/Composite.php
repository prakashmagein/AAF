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
namespace Aheadworks\RewardPoints\Model\Validator;

use Magento\Framework\Validator\AbstractValidator;

/**
 * Class Composite
 *
 * @package Aheadworks\RewardPoints\Model\Validator
 */
class Composite extends AbstractValidator
{
    /**
     * @var AbstractValidator[]
     */
    protected $validatorList = [];

    /**
     * @param AbstractValidator[] $validatorList
     */
    public function __construct(
        array $validatorList = []
    ) {
        $this->validatorList = $validatorList;
    }

    /**
     * @inheritdoc
     */
    public function isValid($abstractModel)
    {
        $this->_clearMessages();

        foreach ($this->validatorList as $validator) {
            if (!$validator->isValid($abstractModel)) {
                $this->_addMessages($validator->getMessages());
            }
        }

        return empty($this->getMessages());
    }
}
