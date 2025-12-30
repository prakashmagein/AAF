<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ProductShipping
 * @copyright  Copyright (c) 2022 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */


namespace Lof\ProductShipping\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\GroupFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Product;

class InstallData implements InstallDataInterface
{
    /**
     * @var GroupFactory
     */
    protected $groupFactory;

    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     *
     * @param GroupFactory $groupFactory
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(GroupFactory $groupFactory, CategorySetupFactory $categorySetupFactory)
    {
        $this->groupFactory = $groupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Lof\MarketPlace\Setup\InstallData::install()
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var CustomerSetup $customerSetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $setup->startSetup();

        $categorySetup->addAttribute(
            Product::ENTITY,
            'lof_shipping_charge',
            [
                'type' => 'varchar',
                'label' => 'Shipping Charges',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'group' => 'Product Details',
                'used_in_product_listing' => true,
                'visible_on_front' => true,
                'visible' => true,
                'user_defined' => true,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => true,
                'backend' => '',
                'frontend' => '',
                'apply_to'     => 'simple,configurable,bundle',
                'frontend_class'=>'validate-zero-or-greater',
                'label' =>  'Shipping Charges',
                'note' => 'Not applicable on downloadable and virtual product.'
            ]
        );

        $setup->endSetup();
    }
}
