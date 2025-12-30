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

namespace Aheadworks\RewardPoints\Setup\Patch\Data;

use Aheadworks\RewardPoints\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Zend_Exception;

/**
 * Class InstallShareCoveredProductAttributes
 */
class InstallShareCoveredProductAttributes implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * Group name for attributes
     *
     * @var string
     */
    private $groupName = 'Reward Points: Configuration';

    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Add Reward Points product attributes
     *
     * @return $this
     * @throws LocalizedException
     * @throws Zend_Exception
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::AW_RP_SHARE_COVERED_ENABLED,
            [
                'group' => $this->groupName,
                'label' => 'Override default configuration',
                'type' => 'int',
                'input' => 'boolean',
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'source' => Boolean::class,
                'default' => Boolean::VALUE_NO,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'sort_order' => '10'
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            ProductAttributeInterface::AW_RP_SHARE_COVERED_PERCENT,
            [
                'group' => $this->groupName,
                'label' => 'Points Usage Limit',
                'type' => 'int',
                'input' => 'text',
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'sort_order' => '20'
            ]
        );

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    /**
     * Remove Reward Points product attributes
     */
    public function revert(): void
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(Product::ENTITY, ProductAttributeInterface::AW_RP_SHARE_COVERED_ENABLED);
        $eavSetup->removeAttribute(Product::ENTITY, ProductAttributeInterface::AW_RP_SHARE_COVERED_PERCENT);

        foreach ($eavSetup->getAllAttributeSetIds(Product::ENTITY) as $setId) {
            $eavSetup->removeAttributeGroup(Product::ENTITY, $setId, $this->groupName);
        }
    }

    /**
     * Patch aliases
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Patch dependencies
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
