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

namespace Aheadworks\RewardPoints\Model\SpendRule\Condition\Cart;

use Magento\Rule\Model\Condition\Product\AbstractProduct;

/**
 * Class Product
 */
class Product extends AbstractProduct
{
    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $productAttributes = $this->_productResource->loadAllAttributes()->getAttributesByCode();

        $attributes = [];
        foreach ($productAttributes as $attribute) {
            /* @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
            if (!$attribute->isAllowedForRuleCondition()
                || !$attribute->getDataUsingMethod($this->_isUsedForRuleProperty)
            ) {
                continue;
            }
            $frontLabel = $attribute->getFrontendLabel();
            $attributes[$attribute->getAttributeCode()] = $frontLabel;
        }

        $this->_addSpecialAttributes($attributes);

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Set attribute value
     *
     * @param string $value
     * @return void
     */
    public function setAttribute(string $value): void
    {
        if (strpos($value, '::') !== false) {
            list($scope, $attribute) = explode('::', $value);
            $this->setData('attribute_scope', $scope);
            $this->setData('attribute', $attribute);
        } else {
            $this->setData('attribute', $value);
        }
    }
}
