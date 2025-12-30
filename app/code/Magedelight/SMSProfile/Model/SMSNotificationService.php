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
namespace Magedelight\SMSProfile\Model;

use Twilio\Rest\ClientFactory;
use Magedelight\SMSProfile\Model\SMSLogFactory;
use Magedelight\SMSProfile\Helper\Data as HelperData;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Customer\Model\CustomerFactory;

class SMSNotificationService
{
    /** @var SMSLogFactory */
    private $smslog;

    /** @var HelperData */
    private $dataHelper;

    /** @var EncryptorInterface */
    private $encryptor;

    /** @var Curl */
    private $curl;

    /**
     * toNumber
     * @var $toNumber
     */

    private $toNumber;

    /**
     * messageContent
     * @var $messageContent
     */

    private $messageContent;

     /**
      * orderContent
      * @var $orderContent
      */

    private $orderContent;
    
    /**
     * transactionType
     * @var $transactionType
     */

    private $transactionType;

    /**
     * apiVersion
     * @var $apiVersion
     */

    private $apiVersion;

    /**
     * @var JsonHelper
     */
    private $jsonHelper;

    /**
     * @var HelperData
     */
    private $datahelper;

    /**
     * @var ClientFactory
     */
    private $twilioClientFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManager;

    private $currentStoreId;
    protected $customerFactory;

    /**
     * Constructor
     * @param SMSLogFactory $smslog
     * @param HelperData $dataHelper
     * @param EncryptorInterface $encryptor
     * @param ClientFactory $twilioClientFactory
     */

    public function __construct(
        SMSLogFactory $smslog,
        HelperData $dataHelper,
        EncryptorInterface $encryptor,
        Curl $curl,
        JsonHelper $jsonHelper,
        ClientFactory $twilioClientFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CustomerFactory $customerFactory
    ) {
        $this->smslog = $smslog;
        $this->datahelper = $dataHelper;
        $this->encryptor = $encryptor;
        $this->curl = $curl;
        $this->jsonHelper = $jsonHelper;
        $this->twilioClientFactory = $twilioClientFactory;
        $this->_storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    private function getTwilioToken($storeId = null)
    {
        $token = $this->datahelper->getSmsProfileTwilioAccountToken($storeId);
        return $this->encryptor->decrypt($token);
    }

    private function getTwilioSid($storeId = null)
    {
        return $this->datahelper->getSmsProfileTwilioAccountSId($storeId);
    }

    private function getDefaultCountryCode($storeId = null)
    {
        return $this->datahelper->getSmsProfileDefaultCountry($storeId);
    }

    public function getTwilioClient($storeId = null)
    {
        return $this->twilioClientFactory->create([
            'username' => $this->getTwilioSid($storeId),
            'password' => $this->getTwilioToken($storeId)
        ]);
    }

    private function getBulkSmsUserName($storeId = null)
    {
        return $this->datahelper->getBulkSmsUserName($storeId);
    }

    private function getBulkSmsPassword($storeId = null)
    {
        $password = $this->datahelper->getBulkSmsPassword($storeId);
        return $this->encryptor->decrypt($password);
    }

    public function setToNumber($toNumber)
    {
        $this->toNumber = $toNumber;
    }

    public function getToNumber($storeId = null)
    {
        if ($this->datahelper->getSmsProfileApiGateWay($storeId) == "Other") {
            if ($this->datahelper->getSmsProfileApiCountryRequired($storeId) && !$this->datahelper->isCustomerCountryEnabled($storeId)) {
                if (strpos($this->toNumber, '+') === false) {
                    $this->toNumber =  $this->getDefaultCountryCode($storeId).$this->toNumber;
                }
            } else {
                $this->toNumber =  $this->toNumber;
            }
        }
        if (strpos($this->toNumber, '+') === false && $this->datahelper->getSmsProfileApiGateWay($storeId) != "Other") {
               $this->toNumber =  $this->getDefaultCountryCode($storeId).$this->toNumber;
        }
        if ($this->datahelper->getSmsProfileApiGateWay($storeId) != "Other" && $this->datahelper->isCustomerCountryEnabled($storeId)) {
            $this->toNumber =  $this->toNumber;
        }
        return str_replace('Undefined', '', $this->toNumber);
    }

    public function setMessageContent($message)
    {
        $this->messageContent = $message;
    }

    public function getMessageContent()
    {
        return $this->messageContent;
    }

    public function setOrderContent($orderContent)
    {
        $this->orderContent = $orderContent;
    }

    public function getOrderContent()
    {
        return $this->orderContent;
    }

    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
    }

    public function getTransactionType()
    {
        return $this->transactionType;
    }

    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;
    }

    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * @param $currentStoreId
     */
    public function setCurrentStoreId($currentStoreId)
    {
        $this->currentStoreId = $currentStoreId;
    }

    /**
     * @return mixed
     */
    public function getCurrentStoreId()
    {
        return $this->currentStoreId;
    }

    private function getAdditionalData($storeId = null)
    {
        $id ='';
        if (is_string($this->getMessageContent()) && $this->getMessageContent() != null) {
            preg_match_all('!\d+!', $this->getMessageContent(), $matches);
            if (isset($matches[0][0])) {
                if (strlen($matches[0][0]) >= 9) {
                    $id = $matches[0][0];
                } elseif (isset($matches[0][1])) {
                    $id = $matches[0][1];
                }
            }
        }
        $additionalData = [
            'apiVersion' => $this->getApiVersion($storeId),
            'transactionType' =>   $this->getTransactionType(),
            'toNumber' => $this->getToNumber($storeId),
            'orderNo' => $id,
        ];

        return $additionalData;
    }

    public function sendSmsWithTwilio($storeId = null)
    {
        $sms  = $this->smslog->create();
        try {
            $client = $this->getTwilioClient($storeId);
            $result = $client->messages->create(
                $this->getToNumber($storeId),
                [
                        'from' => $this->datahelper->getSmsProfileTwilioPhoneNumber($storeId),
                        'body' => $this->getMessageContent()
                    ]
            ); /* get result json */
            $sms->addLog($result, $this->getAdditionalData($storeId), null);
        } catch (\Exception $e) {
            $sms->addLog($result = 'fail', $this->getAdditionalData($storeId), $e->getMessage());
        }
        return $this;
    }

    private function getBulkSmsData($storeId = null)
    {
        $data =  [
            'to' => $this->getToNumber($storeId),
            "body" => $this->getMessageContent(),
        ];
        return $data;
    }

    public function sendSmsWithBulkSmsService($storeId = null)
    {
        $url = $this->datahelper->getBulkSmsUrl($storeId);
        $data =  $this->getBulkSmsData($storeId);

        $sms  = $this->smslog->create();
        try {
            $this->curl->setCredentials($this->getBulkSmsUserName($storeId), $this->getBulkSmsPassword($storeId));
            $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
            $this->curl->setOption(CURLOPT_HEADER, true);
            $this->curl->setOption(CURLOPT_ENCODING, 'UTF-8');
            $this->curl->post($url, $data);
            $response = $this->curl->getBody();
            if (strpos($response, '[') !== false) {
                $response=  strstr($response, '[');
            } elseif (strpos($response, '{') !== false) {
                $response=  strstr($response, '{');
            }
            $result = $this->jsonHelper->jsonDecode($response); /* get result array  */
            if (isset($result['title'])) {
                  $sms->addLog($result, $this->getAdditionalData($storeId), $result['title']);
            } else {
                $sms->addLog(
                    $result,
                    $this->getAdditionalData($storeId),
                    is_array($result) ? null :  __('Unable to initialize cURL request')
                );
            }
        } catch (\Exception $e) {
            $sms->addLog($result = 'fail', $this->getAdditionalData($storeId), $e->getMessage());
        }
        return $this;
    }

    public function sendSmsViaOtherServices($storeId = null)
    {
        $url = $this->datahelper->getSmsProfileApiUrl($storeId);
        if ($this->datahelper->getSmsProfileApiUrlForNotification()) {
            $url = $this->datahelper->getSmsProfileApiUrlForNotification();
        }
        $user  = $this->getApiUser($storeId);
        $password = $this->getApiUserPassword($storeId);
        $data = $this->getApiData($storeId);
        $sms  = $this->smslog->create();
        $getstring ='';
        if ((strpos($url, 'mshastra') !== false)) {
            foreach ($data as $key => $value) {
                if ($key === $this->datahelper->getSmsProfileApiSmsBody($storeId)) {
                     $getstring .= $key."=".urlencode($value)."&";
                } else {
                    $getstring .= $key."=".$value."&";
                }
            }
            $getstring = substr($getstring, 0, -1);

            $_url = $url.'?'.$getstring;
        } elseif (strpos($url, 'msg91') !== false) {
            $data['mobiles'] = $data['mobile'];
            unset($data['mobile']);
        }

        try {
            if ($this->datahelper->getAPIRequestInGet($storeId)) {
                $this->curl->get($_url);
            } else {
                if ((strpos($url, 'voodoosms') !== false)) {
                    $headers = ["Content-Type" => "application/x-www-form-urlencoded",
                        "authorization" => $data['api_key'],
                        "cache-control"=>'no-cache'];
                    $this->curl->setHeaders($headers);
                    unset($data['api_key']);
                } elseif (strpos($url, 'msg91') !== false) {
                    $tempData = [];
                    $storeId = $this->getStoreId();
                    if (strcmp($this->getTransactionType(), "customer_neworder") == 0) {
                        $tempData = $this->datahelper->getCustomerNewOrderNotificationTemplate('customer_neworder', $storeId);
                    } elseif (strcmp($this->getTransactionType(), "customer_invoice") == 0) {
                        $tempData = $this->datahelper->getCustomerInvoiceNotificationTemplate('customer_invoice', $storeId);
                    } elseif (strcmp($this->getTransactionType(), "customer_creditmemo") == 0) {
                        $tempData = $this->datahelper->getCustomerCreditmemoNotificationTemplate('customer_creditmemo', $storeId);
                    } elseif (strcmp($this->getTransactionType(), "customer_shipment") == 0) {
                        $tempData = $this->datahelper->getCustomerShipmentNotificationTemplate('customer_shipment', $storeId);
                    } elseif (strcmp($this->getTransactionType(), "customer_order_cancel") == 0) {
                        $tempData = $this->datahelper->getCustomerOrderCancelNotificationTemplate('customer_order_cancel', $storeId);
                    } elseif (strcmp($this->getTransactionType(), "customer_contact") == 0) {
                        $tempData = $this->datahelper->getCustomerContactNotificationTemplate('customer_contact', $storeId);
                    } elseif (strcmp($this->getTransactionType(), "admin_new_order") == 0) {
                        $tempData = $this->datahelper->getAdminNewOrderNotificationTemplate('admin_new_order', $storeId);
                    } elseif (strcmp($this->getTransactionType(), "admin_new_customer") == 0) {
                        $tempData = $this->datahelper->getAdminNewCustomerNotificationTemplate('admin_new_customer', $storeId);
                    } elseif (strcmp($this->getTransactionType(), "admin_customer_contact") == 0) {
                        $tempData = $this->datahelper->getAdminCustomerContactNotificationTemplate('admin_customer_contact', $storeId);
                    } elseif (strcmp($this->getTransactionType(), "customer_shipment_tracking") == 0) {
                        $tempData = $this->datahelper->getCustomerShipmentTrackingNotificationTemplate('customer_shipment_tracking', $storeId);
                    }
                    if (array_key_exists('template_id', $tempData)) {
                        $data['template_id'] = $tempData['template_id'];
                    }
                    if (array_key_exists('sender', $tempData)) {
                        $data['sender'] = $tempData['sender'];
                    }
                    $orderDetails = $this->getOrderContent();
                    if ($orderDetails) {
                        $data['var1'] = $orderDetails['firstname'].' '.$orderDetails['lastname'];
                        $data['var2'] = $orderDetails['order_id'];
                        $data['var3'] = $orderDetails['total'];
                        $data['var4'] = $orderDetails['orderitem'];
                        $data['var5'] = $orderDetails['store'];
                        $data['unicode'] = 1;
                    }
                    unset($data['message']);
                } else {
                    $this->curl->setCredentials($user, $password);
                }

                if (strpos($url, 'msg91') !== false) {
                    $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
                    $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
                    $this->curl->setOption(CURLOPT_HEADER, true);
                    $this->curl->setOption(CURLOPT_ENCODING, 'UTF-8');
                    $this->curl->post($url, $this->jsonHelper->jsonEncode($data));
                } else {
                    if ((strpos($url, 'messagebird') !== false)) {
                        $headr[] = 'Authorization: '.$this->getAuthorizationHeader();
                        $this->curl->setOption(CURLOPT_HTTPHEADER, $headr);
                        if (isset($data['recipients']) && strpos($data['recipients'], '+') === false) {
                            $data['recipients'] = $this->getCountryCode($data['recipients']) . $data['recipients'];
                        }
                    }
                    $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
                    $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
                    $this->curl->setOption(CURLOPT_HEADER, true);
                    $this->curl->setOption(CURLOPT_ENCODING, 'UTF-8');
                    $this->curl->post($url, $data);
                }
            }
            $response = $this->curl->getBody();
            if (strpos($response, '{') !== false) {
                $response=  strstr($response, '{');
                if ((strpos($url, 'voodoosms') !== false)) {
                    $response = $this->jsonHelper->jsonDecode($response);

                    if (isset($response['error'])) {
                        $result['error_m'] = $response['error']['msg'];
                    } else {
                        $result['status'] = $response['messages'][0]['status'];
                        $result['body'] = $response['body'];
                        $result['sid'] = $response['messages'][0]['id'];
                    }
                } else {
                    $result = $this->jsonHelper->jsonDecode($response); //get result array
                    if (strpos($url, 'msg91') !== false) {
                        $result['status'] = $result['type'];
                        /*$result['body'] = $data[$this->datahelper->getSmsProfileApiSmsBody($storeId)];*/
                        $result['sid'] = $result['message'];
                    }
                }
            } elseif (strpos($url, 'mshastra') !== false) {
                 $_result = explode(",", $response);
                 $result['status'] = $_result[2];
                 $result['body'] = $data[$this->datahelper->getSmsProfileApiSmsBody($storeId)];
                 $result['sid'] = $_result[0];
            } else {
                if (strpos($url, 'msg91') !== false) {
                    $_result = explode(" ", $response); //get result array
                    
                    $result['status'] = $_result[2];
                    $sid_array = explode("\n", $_result[2]);
                    if (!is_null($sid_array)) {
                        $result['status'] =   $sid_array[0];
                    }
                    $result['body'] = $data[$this->datahelper->getSmsProfileApiSmsBody($storeId)];
                    $result['sid'] = (isset($_result[32])) ? '' : $_result[31];
                    

                    if (isset($_result[31])) {
                        $status_array = explode("\n", $result['sid']);
                        if (!is_null($status_array)) {
                             $result['sid'] =  end($status_array);
                        }
                    }
                    if (isset($_result[32])) {
                         $result['error_m'] = $_result[32].' '.$_result[33].' '.$_result[34].' '.$_result[35].' '.$_result[36];
                        $result['status'] = __('failed');
                    }
                }
            }
            if (isset($result['error_m'])) {
                /*case of msg91 */
                $sms->addLog($result, $this->getAdditionalData($storeId), $result['error_m']);
            } elseif (isset($result['ErrorMessage']) && $result['ErrorMessage'] != "Success") {
                 /** case of smsindihub*/
                $sms->addLog($result, $this->getAdditionalData($storeId), $result['ErrorMessage']);
            } elseif (isset($result['errors'][0]['description'])) {
                /*case of message bird */
                $sms->addLog($result, $this->getAdditionalData($storeId), $result['errors'][0]['description']);
            } else {
                $sms->addLog(
                    $result,
                    $this->getAdditionalData($storeId),
                    is_array($result) ? null :  __('Unable to initialize cURL request')
                );
            }
        } catch (\Exception $e) {
            $sms->addLog($result = 'fail', $this->getAdditionalData($storeId), $e->getMessage());
        }
        return $this;
    }

    public function getAuthorizationHeader()
    {
        $headerCredential = $this->datahelper->getAuthorizationHeader();
        return $headerCredential;
    }

    private function getCountryCode($phone)
    {
        if ($this->datahelper->isCustomerCountryEnabled()) {
            return $this->getCustomerCountryCode($phone);
        } else {
            return $this->getDefaultCountryCode();
        }
    }

    private function getCustomerCountryCode($phone)
    {
        $collection = $this->customerFactory->create()->getCollection()
               ->addAttributeToSelect("countryreg")
               ->addAttributeToFilter('customer_mobile', $phone)
               ->load();
        $firstItem = $collection->getFirstItem();

        if ($firstItem->getCountryreg() !== null) {
            return $firstItem->getCountryreg();
        } else {
            return $this->getDefaultCountryCode();
        }
    }

    public function getApiUser($storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->getCurrentStoreId();
        }
        $apiCredential = $this->datahelper->getSmsProfileApiCredential($storeId);
        if (isset($apiCredential['username'])) {
            return $apiCredential['username'];
        }
        return '';
    }

    public function getApiUserPassword($storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->getCurrentStoreId();
        }
        $apiCredential = $this->datahelper->getSmsProfileApiCredential($storeId);
        if (!isset($apiCredential['password'])) {
            return '';
        }
        return $apiCredential['password'];
    }

    public function getApiData($storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->getCurrentStoreId();
        }
        $data = $this->datahelper->getSmsProfileApiParams($storeId);
        $data[$this->datahelper->getSmsProfileApiTo($storeId)] = $this->getToNumber($storeId);
        $data[$this->datahelper->getSmsProfileApiSmsBody($storeId)] = $this->getMessageContent();
        return $data;
    }
}
