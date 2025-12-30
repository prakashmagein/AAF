<?php
namespace Codedecorator\WhatsAppChat\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Codedecorator\WhatsAppChat\Helper\Data;


/**
 * Class InstallData
 * @package Codedecorator\DisableRightClick\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * InstallData constructor.
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper=$helper;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->helper->installModule();
    }
}