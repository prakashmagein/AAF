<?php
/**
 * Magedelight
 * Copyright (C) 2022 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @copyright Copyright (c) 2022 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author    Magedelight <info@magedelight.com>
 */
 
namespace Magedelight\SMSProfile\Controller\Account;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\AddressRegistry;
use Magento\Framework\Escaper;
use Magento\Customer\Model\Session;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\Customer\Mapper;

class EditPost extends \Magento\Customer\Controller\Account\EditPost
{
      /**
       * @var Mapper
       */
    private $customerMapper;
      /**
       * @var AddressRegistry
       */
    private $addressRegistry;
   
    /**
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param Validator $formKeyValidator
     * @param Escaper|null $escaper
     * @param AddressRegistry|null $addressRegistry
     * @param Filesystem $filesystem

     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerRepositoryInterface $customerRepository,
        Validator $formKeyValidator,
        CustomerExtractor $customerExtractor,
        ?Escaper $escaper = null,
        AddressRegistry $addressRegistry = null
    ) {

         parent::__construct($context, $customerSession, $customerAccountManagement, $customerRepository, $formKeyValidator, $customerExtractor, $escaper, $addressRegistry);
    }

   /**
    * Change customer email or password action
    *
    * @return Redirect
    * @SuppressWarnings(PHPMD.CyclomaticComplexity)
    */
    public function execute()
    {
       
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $validFormKey = $this->formKeyValidator->validate($this->getRequest());

        if ($validFormKey && $this->getRequest()->isPost()) {
            $currentCustomerDataObject = $this->getCustomerDataObject($this->session->getCustomerId());
            $customerCandidateDataObject = $this->populateNewCustomerDataObject(
                $this->_request,
                $currentCustomerDataObject
            );

            $attributeToDelete = $this->_request->getParam('delete_attribute_value');
            if ($attributeToDelete !== null) {
                $this->deleteCustomerFileAttribute(
                    $customerCandidateDataObject,
                    $attributeToDelete
                );
            }

            try {
                // whether a customer enabled change email option
                $isEmailChanged = $this->processChangeEmailRequest($currentCustomerDataObject);

                // whether a customer enabled change password option
                $isPasswordChanged = $this->changeCustomerPassword($currentCustomerDataObject->getEmail());

                // No need to validate customer address while editing customer profile
                $this->disableAddressValidation($customerCandidateDataObject);

                $this->customerRepository->save($customerCandidateDataObject);
                $this->getEmailNotification()->credentialsChanged(
                    $customerCandidateDataObject,
                    $currentCustomerDataObject->getEmail(),
                    $isPasswordChanged
                );
                $this->dispatchSuccessEvent($customerCandidateDataObject);
                $this->messageManager->addSuccessMessage(__('You saved the account information.'));
                // logout from current session if password or email changed.
                if ($isPasswordChanged || $isEmailChanged) {
                    $this->session->logout();
                    $this->session->start();
                    return $resultRedirect->setPath('customer/account/login');
                }
                return $resultRedirect->setPath('customer/account');
            } catch (InvalidEmailOrPasswordException $e) {
                $this->messageManager->addErrorMessage($this->escaper->escapeHtml($e->getMessage()));
            } catch (UserLockedException $e) {
                $message = __(
                    'The account sign-in was incorrect or your account is disabled temporarily. '
                    . 'Please wait and try again later.'
                );
                $this->session->logout();
                $this->session->start();
                $this->messageManager->addErrorMessage($message);

                return $resultRedirect->setPath('customer/account/login');
            } catch (InputException $e) {
                $this->messageManager->addErrorMessage($this->escaper->escapeHtml($e->getMessage()));
                foreach ($e->getErrors() as $error) {
                    $this->messageManager->addErrorMessage($this->escaper->escapeHtml($error->getMessage()));
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t save the customer.'));
            }

            $this->session->setCustomerFormData($this->getRequest()->getPostValue());
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/edit');

        return $resultRedirect;
    }

     /**
      * Get customer data object
      *
      * @param int $customerId
      *
      * @return CustomerInterface
      */
    private function getCustomerDataObject($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }

     /**
      * Create Data Transfer Object of customer candidate
      *
      * @param RequestInterface $inputData
      * @param CustomerInterface $currentCustomerData
      * @return CustomerInterface
      */
    private function populateNewCustomerDataObject(
        RequestInterface $inputData,
        CustomerInterface $currentCustomerData
    ) {
        $attributeValues = $this->getCustomerMapper()->toFlatArray($currentCustomerData);
        $customerDto = $this->customerExtractor->extract(
            self::FORM_DATA_EXTRACTOR_CODE,
            $inputData,
            $attributeValues
        );
        $customerDto->setId($currentCustomerData->getId());
        if (!$customerDto->getAddresses()) {
            $customerDto->setAddresses($currentCustomerData->getAddresses());
        }
        if (!$inputData->getParam('change_email')) {
            $customerDto->setEmail($currentCustomerData->getEmail());
        }

        return $customerDto;
    }

     /**
      * Get Customer Mapper instance
      *
      * @return Mapper
      *
      * @deprecated 100.1.3
      */
    private function getCustomerMapper()
    {
        if ($this->customerMapper === null) {
            $this->customerMapper = ObjectManager::getInstance()->get(Mapper::class);
        }
        return $this->customerMapper;
    }

    /**
     * Process change email request
     *
     * @param CustomerInterface $currentCustomerDataObject
     * @return bool
     * @throws InvalidEmailOrPasswordException
     * @throws UserLockedException
     */
    private function processChangeEmailRequest(CustomerInterface $currentCustomerDataObject)
    {
        if ($this->getRequest()->getParam('change_email')) {
            // authenticate user for changing email
            try {
                $this->getAuthentication()->authenticate(
                    $currentCustomerDataObject->getId(),
                    $this->getRequest()->getPost('current_password')
                );
                $this->sessionCleaner->clearFor((int) $currentCustomerDataObject->getId());
                return true;
            } catch (InvalidEmailOrPasswordException $e) {
                throw new InvalidEmailOrPasswordException(
                    __("The password doesn't match this account. Verify the password and try again.")
                );
            }
        }
        return false;
    }

    /**
     * Disable Customer Address Validation
     *
     * @param CustomerInterface $customer
     * @throws NoSuchEntityException
     */
    private function disableAddressValidation($customer)
    {
        foreach ($customer->getAddresses() as $address) {
            $addressModel = $this->addressRegistry->retrieve($address->getId());
            $addressModel->setShouldIgnoreValidation(true);
        }
    }
}
