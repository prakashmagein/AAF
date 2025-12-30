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

use Magento\SalesRule\Model\Rule\Condition\Product\Found;
use Magento\SalesRule\Model\Rule\Condition\Product\Subselect;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Rule\Catalog;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Cart\Product as ConditionProduct;
use Magento\CatalogRule\Model\Rule\Condition\Product;
use Magento\Rule\Model\Condition\Combine as RuleCombine;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Address as ConditionAddress;
use Magento\Rule\Model\Condition\Context;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Rule\Cart;

/**
 * Class Combine
 */
class Combine extends RuleCombine
{
    /**
     * @var ConditionAddress
     */
    protected $conditionAddress;

    /**
     * @var ConditionProduct
     */
    protected $conditionProduct;

    /**
     * @param Context $context
     * @param ConditionAddress $conditionAddress
     * @param ConditionProduct $conditionProduct
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConditionAddress $conditionAddress,
        ConditionProduct $conditionProduct,
        array $data = []
    ) {
        $this->conditionAddress = $conditionAddress;
        $this->conditionProduct = $conditionProduct;
        parent::__construct($context, $data);
        $this->setType(Combine::class);
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions(): array
    {
        $addressAttributes = $this->conditionAddress->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($addressAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Aheadworks\RewardPoints\Model\SpendRule\Condition\Address|' . $code,
                'label' => $label,
            ];
        }

        $productAttributes = $this->conditionProduct->loadAttributeOptions()->getAttributeOption();
        $attributesProduct = [];
        foreach ($productAttributes as $code => $label) {
            $attributesProduct[] = [
                'value' => 'Aheadworks\RewardPoints\Model\SpendRule\Condition\Cart\Product|' . $code,
                'label' => $label,
            ];
        }

        $conditions = parent::getNewChildSelectOptions();

        if ($this->getPrefix() == Cart::CONDITION_PREFIX) {
            $conditions = array_merge_recursive(
                $conditions,
                [
                    [
                        'value' => Found::class,
                        'label' => __('Product attribute combination'),
                    ],
                    [
                        'value' => Subselect::class,
                        'label' => __('Products subselection')
                    ],
                    ['label' => __('Cart Attribute'), 'value' => $attributes]
                ]
            );
        } else {
            $conditions = array_merge_recursive(
                $conditions,
                [
                    [
                        'value' => Combine::class,
                        'label' => __('Conditions combination')
                    ],
                ]
            );

        }

        if ($this->getPrefix() == Catalog::CONDITION_PREFIX) {
            $conditions = array_merge_recursive(
                $conditions,
                [
                    ['label' => __('Product Attribute'), 'value' => $attributesProduct]
                ]
            );
        }

        return $conditions;
    }

    /**
     * Return conditions
     *
     * @return array|ConditionProduct|Product
     */
    public function getConditions()
    {
        if ($this->getData($this->getPrefix()) === null) {
            $this->setData($this->getPrefix(), []);
        }
        return $this->getData($this->getPrefix());
    }

    /**
     * Caollect validate attributes
     *
     * @param array $productCollection
     * @return \Magento\CatalogRule\Model\Rule\Condition\Combine
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            /** @var Product|Combine $condition */
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
