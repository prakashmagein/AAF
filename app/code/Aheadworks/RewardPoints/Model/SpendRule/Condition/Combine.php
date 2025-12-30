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

use Aheadworks\RewardPoints\Model\SpendRule\Condition\Rule\Catalog;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Product as ConditionProduct;
use Magento\CatalogRule\Model\Rule\Condition\Product;
use Magento\Rule\Model\Condition\Combine as RuleCombine;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Address as ConditionAddress;
use Magento\Rule\Model\Condition\Context;

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
        $this->setType(self::class);
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions(): array
    {
        $productAttributes = $this->conditionProduct->loadAttributeOptions()->getAttributeOption();
        $attributesProduct = [];
        foreach ($productAttributes as $code => $label) {
            $attributesProduct[] = [
                'value' => 'Aheadworks\RewardPoints\Model\SpendRule\Condition\Product|' . $code,
                'label' => $label,
            ];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => self::class,
                    'label' => __('Conditions combination')
                ],
            ]
        );

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
     * @return Combine
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
