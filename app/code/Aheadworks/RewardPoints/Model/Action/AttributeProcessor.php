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
namespace Aheadworks\RewardPoints\Model\Action;

use Magento\Framework\Api\AttributeInterface;

/**
 * Class AttributeProcessor
 * @package Aheadworks\RewardPoints\Model\Action
 */
class AttributeProcessor
{
    /**
     * Get attribute value by code
     *
     * @param string $code
     * @param AttributeInterface[] $attributes
     * @return mixed|null
     * @throws \Exception
     */
    public function getAttributeValueByCode($code, $attributes)
    {
        $value = null;
        /** @var AttributeInterface $attribute */
        foreach ($attributes as $attribute) {
            if ($code == $attribute->getAttributeCode()) {
                $value = $attribute->getValue();
                break;
            }
        }

        return $value;
    }
}
