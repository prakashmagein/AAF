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

namespace Aheadworks\RewardPoints\Plugin\Controller\Newsletter\Manage;

use Aheadworks\RewardPoints\Model\Config;
use Aheadworks\RewardPoints\Model\Service\PointsSummaryService;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\DataObject;
use Magento\Newsletter\Controller\Manage\Save;
use Aheadworks\RewardPoints\Api\Data\PointsSummaryInterface;
use Aheadworks\RewardPoints\Model\Source\SubscribeStatus;
use Aheadworks\RewardPoints\Api\TransactionRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\RewardPoints\Api\Data\TransactionInterface;

class SavePlugin
{
    /**
     * @param PointsSummaryService $pointsSummaryService
     * @param StoreManagerInterface $storeManager
     * @param Validator $formKeyValidator
     * @param CustomerSession $customerSession
     * @param RequestInterface $request
     * @param ManagerInterface $messageManager
     * @param DataObject $dataObject
     * @param TransactionRepositoryInterface $transactionRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Config $config
     */
    public function __construct(
        private readonly PointsSummaryService $pointsSummaryService,
        private readonly StoreManagerInterface $storeManager,
        private readonly Validator $formKeyValidator,
        private readonly CustomerSession $customerSession,
        private readonly RequestInterface $request,
        private readonly ManagerInterface $messageManager,
        private readonly DataObject $dataObject,
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly Config $config
    ) {
    }

    /**
     * Save newsletter subscriptions
     *
     * @param Save $subject
     * @param null $result
     * @return $this
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute($subject, $result)
    {
        if (!$this->formKeyValidator->validate($this->request) || !$this->hasCustomerTransactions()) {
            return $result;
        }

        $customerId = $this->customerSession->getCustomerId();
        $websiteId = (int)$this->storeManager->getWebsite()->getId();
        if ($customerId === null) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while saving your %1 subscription.',
                    $this->config->getLabelNameRewardPoints($websiteId)
                )
            );
        } else {
            try {
                $balanceUpdate = (bool)$this->request->getParam('aw_rewardpoints_is_balance_update_subscribed', false)
                    ? SubscribeStatus::SUBSCRIBED
                    : SubscribeStatus::UNSUBSCRIBED;
                $expirationReminders = (bool)$this->request->getParam('aw_rewardpoints_is_expiration_subscribed', false)
                    ? SubscribeStatus::SUBSCRIBED
                    : SubscribeStatus::UNSUBSCRIBED;
                $summaryData = $this->dataObject->setData(
                    [
                        PointsSummaryInterface::CUSTOMER_ID => $customerId,
                        PointsSummaryInterface::WEBSITE_ID => $this->storeManager->getStore()->getWebsiteId(),
                        PointsSummaryInterface::BALANCE_UPDATE_NOTIFICATION_STATUS => $balanceUpdate,
                        PointsSummaryInterface::EXPIRATION_NOTIFICATION_STATUS => $expirationReminders
                    ]
                );
                $this->pointsSummaryService->updateCustomerSummary($summaryData);
                $this->messageManager->addSuccessMessage(__('Your %1 subscription settings were updated.',
                        $this->config->getLabelNameRewardPoints($websiteId)
                    )
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving your %1 subscription.',
                        $this->config->getLabelNameRewardPoints($websiteId)
                    )
                );
            }
        }

        return $result;
    }

    /**
     * Retrieve the customer has transactions or not
     *
     * @return bool
     */
    public function hasCustomerTransactions()
    {
        $transactions = [];
        $customerId = $this->customerSession->getCustomerId();
        if ($customerId != null) {
            $this->searchCriteriaBuilder->addFilter(TransactionInterface::CUSTOMER_ID, $customerId);
            $transactions = $this->transactionRepository
                ->getList($this->searchCriteriaBuilder->create())
                ->getItems();
        }
        return $transactions && count($transactions);
    }
}
