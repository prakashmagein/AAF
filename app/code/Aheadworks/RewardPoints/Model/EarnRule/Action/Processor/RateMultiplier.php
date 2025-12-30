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
namespace Aheadworks\RewardPoints\Model\EarnRule\Action\Processor;

use Aheadworks\RewardPoints\Model\Action\AttributeProcessor;
use Aheadworks\RewardPoints\Model\EarnRule\Action\ProcessorInterface as ActionProcessorInterface;

/**
 * Class RateMultiplier
 * @package Aheadworks\RewardPoints\Model\EarnRule\Action\Processor
 */
class RateMultiplier implements ActionProcessorInterface
{
    /**
     * @var AttributeProcessor
     */
    private $attributeProcessor;

    /**
     * @param AttributeProcessor $attributeProcessor
     */
    public function __construct(
        AttributeProcessor $attributeProcessor
    ) {
        $this->attributeProcessor = $attributeProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function process($value, $qty, $attributes)
    {
        $multiplier = $this->attributeProcessor->getAttributeValueByCode('multiplier', $attributes);
        if (is_numeric($multiplier)) {
            $value *= $multiplier;
        }

        return $value;
    }
}
