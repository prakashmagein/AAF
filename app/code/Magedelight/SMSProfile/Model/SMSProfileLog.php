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

namespace Magedelight\SMSProfile\Model;

use Magedelight\SMSProfile\Helper\Data as HelperData;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class SMSProfileLog extends AbstractModel
{
    const CACHE_TAG = 'smsprofilelog';

    protected $_cacheTag = 'smsprofilelog';

    protected $_eventPrefix = 'smsprofilelog';

    /** @var HelperData */
    private $datahelper;

    /**  @var TimezoneInterface */
    private $timezone;

    /** @var ScopeConfigInterface */
    protected $_scopeConfig;

    /** @var TransportBuilder */
    protected $transportBuilder;

    /** @var StateInterface  */
    protected $inlineTranslation;

    /**
     * Constructor
     * @param HelperData $dataHelper
     * @param Context $context
     * @param Registry $registry
     * @param DateTime $date
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $timezone
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     */

    public function __construct(
        HelperData $dataHelper,
        Context $context,
        Registry $registry,
        DateTime $date,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        TimezoneInterface $timezone,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->datahelper = $dataHelper;
        $this->date = $date;
        $this->timezone = $timezone;
        $this->_scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init(\Magedelight\SMSProfile\Model\ResourceModel\SMSProfileLog::class);
    }

    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData('entity_id', $id);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData('entity_id');
    }

    /**
     * @param string $sid
     * @return $this
     */
    public function setSid($sid)
    {
        return $this->setData('s_id', $sid);
    }

    /**
     * @return string
     */
    public function getSid()
    {
        return $this->getData('s_id');
    }

    /**
     * @param string $apiService
     * @return $this
     */
    public function setApiService($apiService)
    {
        return $this->setData('api_service', $apiService);
    }

    /**
     * @return string
     */
    public function getApiService()
    {
        return $this->getData('api_service');
    }

    /**
     * @param string $recipientPhone
     * @return $this
     */
    public function setRecipientPhone($recipientPhone)
    {
        return $this->setData('recipient_phone', $recipientPhone);
    }

    /**
     * @return string
     */
    public function getRecipientPhone()
    {
        return $this->getData('recipient_phone');
    }

    /**
     * @param string $transaction_type
     * @return $this
     */
    public function setTransactionType($transactionType)
    {
        return $this->setData('transaction_type', $transactionType);
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return $this->getData('transaction_type');
    }

    /**
     * @param string $messageBody
     * @return $this
     */
    public function setMessageBody($messageBody)
    {
        return $this->setData('message_body', $messageBody);
    }

    /**
     * @return string
     */
    public function getMessageBody()
    {
        return $this->getData('message_body');
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData('status', $status);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * @param int $isError
     * @return $this
     */
    public function setIsError($isError)
    {
        return $this->setData('is_error', $isError);
    }

    /**
     * @return int
     */
    public function getIsError()
    {
        return $this->getData('is_error');
    }

    /**
     * @param string $errorMessage
     * @return $this
     */
    public function setErrorMessage($errorMessage)
    {
        return $this->setData('error_message', $errorMessage);
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->getData('error_message');
    }

    /**
     * @param $result
     * @param $additionalData
     * @param $error
     * @throws \Exception
     */
    public function addSmsProfileLog($result, $additionalData, $error)
    {
        $_now = $this->date->gmtDate();
        $now = $this->timezone->date()->format('Y-m-d H:i:s');
        if ($this->datahelper->getSmsProfileLogStatus()) {
            $this->setSid($this->getResultSid($result));
            $this->setApiService($additionalData['apiVersion']);
            if (strpos($additionalData['toNumber'], '{"binding_type"') !== false) {
                $numbers = str_replace('{"binding_type":"sms","address":"', '', $additionalData['toNumber']);
                $numbers = str_replace('"}', ' ', $numbers);
                $this->setRecipientPhone($numbers);
            } else {
                $_numbers = str_replace(',', ', ', $additionalData['toNumber']);
                $this->setRecipientPhone($_numbers);
            }
            $this->setTransactionType($additionalData['transactionType']);
            $this->setMessageBody($this->getResultBody($result));
            $this->setStatus($this->getResultStatus($result, $additionalData));
            $this->setIsError(0);
            if ($error != null) {
                $this->setIsError(1);
            }
            $this->setErrorMessage($this->getResultError($error));
            $this->setCreatedAt($now);
            $this->save();
        }
        if ($error != null) {
            $this->sendSmsProfileFailureMail($additionalData, $error);
        }
    }

    /**
     * @param $result
     * @return mixed|string
     */
    public function getResultSid($result)
    {
        if ($result === 'fail') {
            return '';
        }
        if (is_array($result)) {
            /*Bulk SMS ID  & other's id*/
            if (isset($result[0]['id'])) {
                return  $result[0]['id'];
            } elseif (isset($result['sid'])) {
                return $result['sid'];
            } elseif (isset($result['MessageData'][0]['MessageParts'][0]['MsgId'])) {
                /* case of sms india hub*/
                return $result['MessageData'][0]['MessageParts'][0]['MsgId'];
            } elseif (isset($result['id'])) {
                /** case of messagebird*/
                return $result['id'];
            } elseif (isset($result['request_id'])) {
                /** case of fast2sms*/
                return $result['request_id'];
            } else {
                return  '';
            }
        }
        /*Twioli SMS ID */
        return  $result->sid;
    }

    /**
     * @param $result
     * @return mixed|string
     */
    public function getResultBody($result)
    {
        if ($result === 'fail') {
            return '';
        }
        if (is_array($result)) {
            /*Bulk SMS & other body */
            if (isset($result[0]['body'])) {
                return $result[0]['body'];
            } elseif (isset($result['body'])) {
                return $result['body'];
            } elseif (isset($result['MessageData'][0]['MessageParts'][0]['Text'])) {
                /* case of sms india hub*/
                return $result['MessageData'][0]['MessageParts'][0]['Text'];
            } else {
                return '';
            }
        }
        /*Twiolio SMS body */
        return  $result->body;
    }

    /**
     * @param $result
     * @param $additionalData
     * @return mixed|string
     */
    public function getResultStatus($result, $additionalData)
    {
        if ($result === 'fail') {
            return 'fail';
        }

        if ($additionalData['transactionType'] === 'Promotional Sms') {
            return '';
        } else {
            if (is_array($result)) {
                /*Bulk SMS & other status */
                if (isset($result[0]['type'])) {
                    /* case of bulksms*/
                    return $result[0]['type'];
                } elseif (isset($result['status'])) {
                    /* case of msg91*/
                    return $result['status'];
                } elseif (isset($result['ErrorMessage'])) {
                    /* case of sms india hub*/
                    if ($result['ErrorMessage'] != 'Success') {
                        return 'failed';
                    }
                    return $result['ErrorMessage'];
                } elseif (isset($result['recipients']['items'][0]['status'])) {  /* case of messagebird*/
                    return $result['recipients']['items'][0]['status'];
                } elseif (isset($result['return'])) {
                    /** case of fast2sms*/
                    return $result['return'];
                } else {
                    return '';
                }
            }
            /*Twiolio SMS status */
            return  $result->status;
        }
    }

    /**
     * @param $error
     * @return string
     */
    public function getResultError($error)
    {
        if ($error === null) {
            return '';
        }
        return $error;
    }

    /**
     * @param $additionalData
     * @param $error
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendSmsProfileFailureMail($additionalData, $error)
    {
        if ($this->datahelper->getFailureNotificationStatus()) {
            $templateId = $this->datahelper->getSMSAlertTemplateId();
            try {
                $this->inlineTranslation->suspend();
                $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
                      ->setTemplateOptions($this->getTemplateOption())
                      ->setTemplateVars($this->getSmsProfileTemplateVariable($additionalData, $error))
                      ->setFromByScope($this->getSmsProfileFromMail($this->datahelper->getSmsProfileNotifyFromMail()))
                      ->addTo($this->datahelper->getSmsProfileNotifyToMail())
                      ->getTransport();
                $transport->sendMessage();
            } catch (\Exception $exception) {
            }
        }
    }

    /**
     * @param $additionalData
     * @param $error
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSmsProfileTemplateVariable($additionalData, $error)
    {
        /*$messageText = __('Sms failure for the number \'<b>' . $additionalData["toNumber"] . '</b>\'. and event is <b>' . $additionalData["transactionType"] . '</b><br/> The error message is <font size="2" color="red">"' . $error . '"</font>');*/
        $templateVars = [
           'store'     => $this->storeManager->getStore(),
           'toNumber'  => $additionalData["toNumber"],
           'transactionType' => $additionalData["transactionType"],
           'error' => $error
        ];

        return $templateVars;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTemplateOption()
    {
        $templateOptions = [
            'area' => Area::AREA_FRONTEND,
            'store' => $this->storeManager->getStore()->getId()
         ];

        return $templateOptions;
    }

    /**
     * @param $_fromMail
     * @return array
     */
    public function getSmsProfileFromMail($_fromMail)
    {
        $senderName = $this->_scopeConfig->getValue(
            'trans_email/ident_' . $_fromMail . '/name',
            ScopeInterface::SCOPE_STORE
        );

        $senderEmail = $this->_scopeConfig->getValue(
            'trans_email/ident_' . $_fromMail . '/email',
            ScopeInterface::SCOPE_STORE
        );

        $from = [
           'name' => $senderName,
           'email' => $senderEmail,
        ];

        return $from;
    }

    public function smsProfileClearelog()
    {
        $connection = $this->getCollection()->getConnection();
        $tableName = $this->getCollection()->getMainTable();
        $connection->truncateTable($tableName);
    }

    /**
     * @param $result
     * @param $additionalData
     * @param $error
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function addSmsProfileLogXML($result, $additionalData, $error)
    {
        $now = $this->timezone->date()->format('Y-m-d H:i:s');
        if ($this->datahelper->getSmsProfileLogStatus()) {
            $this->setSid($additionalData['phoneId']);
            $this->setApiService($additionalData['apiVersion']);
            $_numbers = str_replace(',', ', ', $additionalData['toNumber']);
            $this->setRecipientPhone($_numbers);
            $this->setTransactionType($additionalData['transactionType']);
            $this->setMessageBody($additionalData['smsBody']);
            $this->setStatus((isset($result->status)) ? (string) $result->status : '');
            $this->setIsError(0);
            if ($error != null) {
                $this->setIsError(1);
            }
            $this->setErrorMessage($this->getResultError($error));
            $this->setCreatedAt($now);
            $this->save();
        }
        if ($error != null) {
            $this->sendSmsProfileFailureMail($additionalData, $error);
        }
    }
}
