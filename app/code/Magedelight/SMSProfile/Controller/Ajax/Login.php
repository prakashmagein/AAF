<?php
/**
 * Magedelight
 * Copyright (C) 2023 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @copyright Copyright (c) 2023 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author    Magedelight <info@magedelight.com>
 */
 
namespace Magedelight\SMSProfile\Controller\Ajax;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Customer\Model\Customer;
use Magedelight\SMSProfile\Helper\Data as HelperData;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Login extends \Magento\Customer\Controller\Ajax\Login
{

    /**
     * @var CollectionFactory
     */
    private $customerCollection;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var HelperData
     */
    private $datahelper;

    /**
     * @var StoreManagerInterface
     */
    private $store;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /** @var TimezoneInterface */
    private $timezone;

    /** @var CollectionFactory  */
    private $attemptcollection;

    /** @var CollectionFactory  */
    private $otpcollection;

    public function __construct(
        Context $context,
        Customer $customer,
        HelperData $datahelper,
        CollectionFactory $customerCollection,
        StoreManagerInterface $store,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Json\Helper\Data $helper,
        AccountManagementInterface $customerAccountManagement,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        TimezoneInterface $timezone,
        \Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtp\CollectionFactory $otpcollection,
        \Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtpAttempt\CollectionFactory $attemptcollection,
        CookieManagerInterface $cookieManager = null,
        CookieMetadataFactory $cookieMetadataFactory = null
    ) {
        $this->customerCollection = $customerCollection;
        $this->customer = $customer;
        $this->datahelper = $datahelper;
        $this->store = $store;
        $this->timezone = $timezone;
        $this->otpcollection = $otpcollection;
        $this->attemptcollection = $attemptcollection;
        $this->cookieManager = $cookieManager ?: ObjectManager::getInstance()->get(
            CookieManagerInterface::class
        );
        $this->cookieMetadataFactory = $cookieMetadataFactory ?: ObjectManager::getInstance()->get(
            CookieMetadataFactory::class
        );
        parent::__construct($context, $customerSession, $helper, $customerAccountManagement, $resultJsonFactory, $resultRawFactory);
    }

    /**
     * Login registered users and initiate a session.
     *
     * Expects a POST. ex for JSON {"username":"user@magento.com", "password":"userpassword"}
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {

        if (!$this->datahelper->getModuleStatus()) {
            return parent::execute();
        }
        
        $credentials = null;
        $httpBadRequestCode = 400;

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        try {
            $credentials = $this->helper->jsonDecode($this->getRequest()->getContent());
        } catch (\Exception $e) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }
        if (!$credentials || $this->getRequest()->getMethod() !== 'POST' || !$this->getRequest()->isXmlHttpRequest()) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        $response = [
            'errors' => false,
            'message' => __('Login successful.')
        ];

        if (isset($credentials['password']) && !empty($credentials['password']) && isset($credentials['username']) && !empty($credentials['username'])) {
            $credentials['mobile']=null;
        }
        
        if (isset($credentials['mobile']) && !empty($credentials['mobile']) && $credentials['verifyotp'] != 1) {
                $response = [
                    'errors' => true,
                    'message' => __('Please verify OTP.')
                ];
        } else {
            try {
                if (isset($credentials['mobile']) && $credentials['mobile']!="") {
                    if (is_numeric($credentials['mobile'])) {
                        $customerCollections = $this->getCustomerByPhone($credentials['mobile']);
                        foreach ($customerCollections as $_customer) {
                            $credentials['username'] = $_customer->getEmail();
                        }
                    } else {
                        $credentials['username'] = $credentials['mobile'];
                    }

                    //verify OTP
                    $isOtpVerified=0;
                    $toNumber = $credentials['mobile'];
                    if ($this->datahelper->isCustomerCountryEnabled() && isset($credentials['countryreg']) && is_numeric($credentials['mobile'])) {
                        $toNumber = $credentials['countryreg'].$credentials['mobile'];
                    }

                    $otp = $credentials['otp'];
                    $minutes = $this->datahelper->getSmsProfileOTPExpiry();
                    $now = $this->timezone->date(null, null, false)->format('Y-m-d H:i:s');
                    $now2 = $this->timezone->date(null, null, false)->modify('-' . $minutes . 'minute')->format('Y-m-d H:i:s');

                    $smsProfileOtp = $this->otpcollection->create();
                    $smsProfileOtp->addFieldToFilter('customer_mobile', $toNumber);
                    $smsProfileOtp->addFieldToFilter('created_at', ['from' => $now2, 'to' => $now]);
                    $smsProfileOtp->addFieldToFilter('created_at', ['gteq' => $now2, 'lteq' => $now]);
                    $smsProfileOtp->getLastItem();

                    if ($smsProfileOtp->getSize()) {
                        $data = $smsProfileOtp->getLastItem();
                        if ($data->getOtpCode() == $otp) {
                            $data->delete();
                            $isOtpVerified=1;
                            //Reset customer Attempt Data:
                            $smsProfileattempt = $this->attemptcollection->create();
                            $smsProfileattempt->addFieldToFilter('customer_mobile', $toNumber);
                            $adata = $smsProfileattempt->getLastItem();
                            $adata->setAttempCount(0);
                            $adata->setResendCountTime(null);
                            $adata->save();
                        } else {
                            $isOtpVerified=0;
                        }
                    }
                }

                if (is_numeric($credentials['username'])) {
                    $customerCollections = $this->getCustomerByPhone($credentials['username']);
                    foreach ($customerCollections as $customer) {
                        $credentials['username']  = $customer->getEmail();
                    }
                }

                if (($this->datahelper->getSmsProfileOtpOnLogin() == 'login_otp' || $this->datahelper->getSmsProfileOtpOnLogin() == 'login_both') && (!empty($credentials['username']) && (!empty($credentials['otp'])) && $isOtpVerified == 1)) {
                    $this->customer->setWebsiteId($this->store->getStore()->getWebsiteId());
                    $customer = $this->customer->loadByEmail($credentials['username']);
                    $this->customerSession->setCustomerAsLoggedIn($customer);
                    $this->customerSession->regenerateId();
                } else {
                    $customer = $this->customerAccountManagement->authenticate(
                        $credentials['username'],
                        $credentials['password']
                    );
                    $this->customerSession->setCustomerDataAsLoggedIn($customer);
                    $this->customerSession->regenerateId();
                }
                $redirectRoute = $this->getAccountRedirect()->getRedirectCookie();
                if ($this->cookieManager->getCookie('mage-cache-sessid')) {
                    $metadata = $this->cookieMetadataFactory->createCookieMetadata();
                    $metadata->setPath('/');
                    $this->cookieManager->deleteCookie('mage-cache-sessid', $metadata);
                }
                if (!$this->getScopeConfig()->getValue('customer/startup/redirect_dashboard') && $redirectRoute) {
                    $response['redirectUrl'] = $this->_redirect->success($redirectRoute);
                    $this->getAccountRedirect()->clearRedirectCookie();
                }
            } catch (EmailNotConfirmedException $e) {
                $response = [
                    'errors' => true,
                    'message' => $e->getMessage()
                ];
            } catch (InvalidEmailOrPasswordException $e) {
                $response = [
                    'errors' => true,
                    'message' => $e->getMessage()
                ];
            } catch (LocalizedException $e) {
                $response = [
                    'errors' => true,
                    'message' => $e->getMessage()
                ];
            } catch (\Exception $e) {
                $response = [
                    'errors' => true,
                    'message' => __($e->getMessage())
                ];
            }
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }

    public function getCustomerByPhone($phone)
    {
        $customerCollection = $this->customerCollection->create();
        $customerCollection->addAttributeToSelect('*')
                           ->addAttributeToFilter('customer_mobile', $phone)
                           ->load();
        return $customerCollection;
    }
}
