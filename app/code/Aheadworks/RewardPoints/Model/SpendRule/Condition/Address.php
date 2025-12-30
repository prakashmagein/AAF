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

namespace Aheadworks\RewardPoints\Model\SpendRule\Condition;

use Magento\SalesRule\Model\Rule\Condition\Address as SalesRuleAddress;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Address
 *
 */
class Address extends SalesRuleAddress
{
    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions(): Address
    {
        $attributes = [
            'base_subtotal_with_discount' => __('Subtotal (Excl. Tax)'),
            'base_subtotal_total_incl_tax' => __('Subtotal (Incl. Tax)'),
            'base_subtotal' => __('Subtotal'),
            'total_qty' => __('Total Items Quantity'),
            'weight' => __('Total Weight'),
            'payment_method' => __('Payment Method'),
            'shipping_method' => __('Shipping Method'),
            'postcode' => __('Shipping Postcode'),
            'region' => __('Shipping Region'),
            'region_id' => __('Shipping State/Province'),
            'country_id' => __('Shipping Country'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Get input type
     *
     * @return string
     */
    public function getInputType(): string
    {
        switch ($this->getAttribute()) {
            case 'base_subtotal':
            case 'base_subtotal_excl_promo':
            case 'weight':
            case 'total_qty':
                return 'numeric';

            case 'shipping_method':
            case 'payment_method':
            case 'country_id':
            case 'region_id':
                return 'select';
        }
        return 'string';
    }

    /**
     * Validate model
     *
     * @param AbstractModel $model
     * @return bool
     */
    public function validate(AbstractModel $model): bool
    {
        if (!$model->hasData($this->getAttribute())) {
            $model->load($model->getId());
        }
        $attributeValue = $model->getData($this->getAttribute());
        if (is_double($attributeValue)) {
            $attributeValue = round($attributeValue, 2);
        }

        return $this->validateAttribute($attributeValue);
    }
}
