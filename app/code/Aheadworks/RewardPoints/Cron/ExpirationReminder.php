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

namespace Aheadworks\RewardPoints\Cron;

use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Aheadworks\RewardPoints\Model\Config;
use Aheadworks\RewardPoints\Model\FlagFactory;
use Aheadworks\RewardPoints\Model\Source\NotifiedStatus;
use Aheadworks\RewardPoints\Model\Source\Transaction\Status;
use Aheadworks\RewardPoints\Model\Source\Transaction\Type;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\RewardPoints\Model\TransactionRepository;
use Aheadworks\RewardPoints\Api\Data\TransactionInterface;
use Aheadworks\RewardPoints\Model\Flag;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Stdlib\DateTime\DateTime;

class ExpirationReminder extends CronAbstract
{
    /**
     * @param DateTime $dateTime
     * @param CustomerRewardPointsManagementInterface $customerRewardPointsService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TransactionRepository $transactionRepository
     * @param FlagFactory $flagFactory
     * @param Config $config
     */
    public function __construct(
        DateTime $dateTime,
        CustomerRewardPointsManagementInterface $customerRewardPointsService,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TransactionRepository $transactionRepository,
        FlagFactory $flagFactory,
        private readonly Config $config
    ) {
        parent::__construct(
            $dateTime,
            $customerRewardPointsService,
            $searchCriteriaBuilder,
            $transactionRepository,
            $flagFactory
        );
    }

    /**
     * Execute the cron job
     *
     * @return $this
     * @throws CouldNotSaveException
     */
    public function execute()
    {
        if ($this->canSendNotification()) {
            if ($this->isLocked(Flag::AW_RP_EXPIRATION_REMINDER_LAST_EXEC_TIME)) {
                return $this;
            }
            $this->sendExpiredReminder();
            $this->setFlagData(Flag::AW_RP_EXPIRATION_REMINDER_LAST_EXEC_TIME);

            return $this;
        }

        return $this;
    }

    /**
     * Send expired reminder
     *
     * @return $this
     * @throws CouldNotSaveException
     */
    private function sendExpiredReminder()
    {
        $this->searchCriteriaBuilder
            ->addFilter(TransactionInterface::STATUS, Status::ACTIVE)
            ->addFilter(TransactionInterface::EXPIRATION_NOTIFIED, NotifiedStatus::WAITING)
            ->addFilter(TransactionInterface::STATUS, Status::ACTIVE)
            ->addFilter(TransactionInterface::EXPIRATION_DATE, 'will_expire');

        $willExpireTransactions = $this->transactionRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();

        $customersData = [];
        foreach ($willExpireTransactions as $willExpireTransaction) {
            $customerId = $willExpireTransaction->getCustomerId();
            if (!isset($customersData[$customerId])) {
                $customersData[$customerId] = [
                    'store_id' => null,
                    'comment' => null,
                    'balance' => $willExpireTransaction->getBalance() + $willExpireTransaction->getBalanceAdjusted(),
                    'expiration_date' => $willExpireTransaction->getExpirationDate(),
                    'notified_status' => NotifiedStatus::NO
                ];
            } else {
                $customersData[$customerId]['balance'] +=
                    $willExpireTransaction->getBalance() + $willExpireTransaction->getBalanceAdjusted();
            }
        }

        foreach ($customersData as $customerId => $customerData) {
            $customersData[$customerId]['notified_status'] = $this->customerRewardPointsService->sendNotification(
                $customerId,
                TransactionInterface::EXPIRATION_NOTIFIED,
                $customerData
            );
        }

        foreach ($willExpireTransactions as $willExpireTransaction) {
            $customerId = $willExpireTransaction->getCustomerId();
            $willExpireTransaction->setExpirationNotified($customersData[$customerId]['notified_status']);
            $this->transactionRepository->save($willExpireTransaction);
        }

        return $this;
    }

    /**
     * Can send notification
     *
     * @return bool
     */
    private function canSendNotification(): bool
    {
        $balanceUpdateActions = explode(',', $this->config->getBalanceUpdateActions());

        return in_array(Type::POINTS_EXPIRED, $balanceUpdateActions, false);
    }
}
