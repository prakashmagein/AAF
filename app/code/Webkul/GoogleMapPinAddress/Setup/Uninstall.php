<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_GoogleMapPinAddress
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\GoogleMapPinAddress\Setup\Patch\Data;

class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{
   /**
    * Eav
    *
    * @var eavSetupFactory
    */
    protected $eavSetupFactory;

    /**
     * Construct
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Uninstall
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function uninstall(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $eavSetup = $this->eavSetupFactory->create();
        $entityTypeId = 1;
        $eavSetup->removeAttribute($entityTypeId, 'latitude');
        $eavSetup->removeAttribute($entityTypeId, 'longitude');
        $setup->endSetup();
    }
}
