<?php
/**
 * Magedelight
 * Copyright (C) 2022 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_SMSProfile
 * @copyright Copyright (c) 2022 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\SMSProfile\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Generate extends Action
{
    /**
     * @var AttributeFactory
     */
    private $customerAttributeFactory;

    /**
     * @var WebsiteFactory
     */
    private $websiteFactory;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    public function __construct(
        Context $context,
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        JsonFactory $resultJsonFactory,
        WriterInterface $configWriter
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configWriter = $configWriter;
        parent::__construct($context);
    }

    /**
     * Collect relations data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $result = [
            'success' => false,
            'errorMessage' => '',
        ];
        try {
            $this->moduleDataSetup->getConnection()->startSetup();
            /** @var CustomerSetup $customerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
            $customerSetup->addAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'customer_notify_mobile',
                [
                    'type' => 'varchar',
                    'label' => 'Mobile Number',
                    'input' => 'text',
                    'source' => '',
                    'required' => false,
                    'visible' => true,
                    'position' => 500,
                    'system' => false,
                    'backend' => ''
                ]
            );

            $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'customer_pan_number')->addData([
                'used_in_forms' => ['adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address']
            ]);
            $attribute->save();
            $this->moduleDataSetup->getConnection()->endSetup();
            $result['success'] = true;
            $this->configWriter->save('magedelightsmsprofile/general/attribute', 'customer_notify_mobile', $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);
            $this->messageManager->addSuccessMessage("Atribute Created and Saved successfully.");
        } catch (\Exception $e) {
            $result['errorMessage'] = $e->getMessage();
            $this->messageManager->addErrorMessage("Someting went wrong.");
        }
        return $resultJson->setData($result);
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magedelight_SMSProfile::smsconfiguration');
    }
}
