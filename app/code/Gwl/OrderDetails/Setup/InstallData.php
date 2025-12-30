<?php
namespace Gwl\OrderDetails\Setup;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Customer\Setup\CustomerSetupFactory;

class UpgradeData implements UpgradeDataInterface
{

    private $customerSetupFactory;

    public function __construct(
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context){

       $setup->startSetup();

$customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
$attributesInfo = [
            'district' => [
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'District',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'system' => false,
                'group' => 'General',
                'global' => true,
                'visible_on_front' => true,
            ],
            'house_description' => [
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'House Description',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'system' => false,
                'group' => 'General',
                'global' => true,
                'visible_on_front' => true,
            ],
        ];

        foreach ($attributesInfo as $attributeCode => $attributeParams) {
            $customerSetup->addAttribute('customer_address', $attributeCode, $attributeParams);

            $vatIdAttribute = $customerSetup->getEavConfig()->getAttribute('customer_address', $attributeCode);

             $vatIdAttribute->setData(
            'used_in_forms',
            ['adminhtml_customer',
            'adminhtml_checkout',
            'adminhtml_customer_address',
            'customer_account_edit',
            'customer_address_edit',
            'customer_address',
            'customer_register_address'
            ]
              );
             $vatIdAttribute->save();
        }
    }
}