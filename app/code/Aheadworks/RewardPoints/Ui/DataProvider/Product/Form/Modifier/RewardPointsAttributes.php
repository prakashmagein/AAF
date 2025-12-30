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

namespace Aheadworks\RewardPoints\Ui\DataProvider\Product\Form\Modifier;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Aheadworks\RewardPoints\Api\Data\ProductAttributeInterface;

/**
 * Class RewardPointsAttributes
 */
class RewardPointsAttributes extends AbstractModifier
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    /**
     * Changing data in attributes on the product editing form
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data): array
    {
        return $data;
    }

    /**
     * Modifying the display of attributes on the product editing form
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta): array
    {
        $this
            ->modifyRewardPointsShareCoveredEnabledAttribute($meta)
            ->modifyRewardPointsShareCoveredPercentAttribute($meta);

        return $meta;
    }

    /**
     * Modify covered enabled attribute
     *
     * @param array $meta
     * @return $this
     */
    private function modifyRewardPointsShareCoveredEnabledAttribute(array &$meta): RewardPointsAttributes
    {
        $attribute  = $this->arrayManager
            ->findPath(ProductAttributeInterface::AW_RP_SHARE_COVERED_ENABLED, $meta, null, 'children');

        if (!$attribute) {
            return $this;
        }

        $config = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'switcherConfig' => [
                            'enabled' => true,
                            'rules' => [
                                [
                                    'value' => '0',
                                    'actions' => [
                                        [
                                            'target' => 'product_form.product_form.reward-points-configuration.'
                                                . 'container_aw_rp_share_covered_percent.aw_rp_share_covered_percent',
                                            'callback' => 'hide'
                                        ],
                                    ]
                                ],
                                [
                                    'value' => '1',
                                    'actions' => [
                                        [
                                            'target' => 'product_form.product_form.reward-points-configuration.'
                                                . 'container_aw_rp_share_covered_percent.aw_rp_share_covered_percent',
                                            'callback' => 'show'
                                        ]
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $meta = $this->arrayManager->merge($attribute, $meta, $config);

        return $this;
    }

    /**
     * Modify covered percent attribute
     *
     * @param array $meta
     * @return RewardPointsAttributes
     */
    private function modifyRewardPointsShareCoveredPercentAttribute(array &$meta): RewardPointsAttributes
    {
        $attribute  = $this->arrayManager
            ->findPath(ProductAttributeInterface::AW_RP_SHARE_COVERED_PERCENT, $meta, null, 'children');
        if (!$attribute) {
            return $this;
        }

        $config = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-small',
                        'addbefore' => '%',
                        'validation' => [
                            'required-entry' => true,
                            'validate-integer' => true,
                            'validate-number-range' => '1-100'
                        ],
                    ],
                ],
            ],
        ];

        $meta = $this->arrayManager->merge($attribute, $meta, $config);

        return $this;
    }
}
