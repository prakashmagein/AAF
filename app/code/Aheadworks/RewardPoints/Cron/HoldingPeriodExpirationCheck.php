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
use Aheadworks\RewardPoints\Model\Source\Transaction\Status;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\RewardPoints\Model\TransactionRepository;
use Aheadworks\RewardPoints\Api\Data\TransactionInterface;
use Aheadworks\RewardPoints\Model\Flag;
use Aheadworks\RewardPoints\Model\FlagFactory;
use Aheadworks\RewardPoints\Api\TransactionManagementInterface;
use Aheadworks\RewardPoints\Model\Service\PointsSummaryService;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class HoldingPeriodExpirationCheck
 */
class HoldingPeriodExpirationCheck extends CronAbstract
{
    /**
     * @var TransactionManagementInterface
     */
    private $transactionService;

    /**
     * @var PointsSummaryService
     */
    private $pointsSummaryService;

    /**
     * @param DateTime $dateTime
     * @param CustomerRewardPointsManagementInterface $customerRewardPointsService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TransactionRepository $transactionRepository
     * @param FlagFactory $flagFactory
     * @param TransactionManagementInterface $transactionService
     * @param PointsSummaryService $pointsSummaryService
     */
    public function __construct(
        DateTime $dateTime,
        CustomerRewardPointsManagementInterface $customerRewardPointsService,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TransactionRepository $transactionRepository,
        FlagFactory $flagFactory,
        TransactionManagementInterface $transactionService,
        PointsSummaryService $pointsSummaryService
    ) {
        parent::__construct(
            $dateTime,
            $customerRewardPointsService,
            $searchCriteriaBuilder,
            $transactionRepository,
            $flagFactory
        );
        $this->transactionService = $transactionService;
        $this->pointsSummaryService = $pointsSummaryService;
    }

    /**
     * Activate transactions that were on hold
     *
     * @return $this
     */
    public function execute()
    {
        if ($this->isLocked(Flag::AW_RP_HOLDING_PERIOD_EXPIRATION_CHECK_LAST_EXEC_TIME)) {
            return $this;
        }
        $this->unHoldTransactions();
        $this->setFlagData(Flag::AW_RP_HOLDING_PERIOD_EXPIRATION_CHECK_LAST_EXEC_TIME);

        return $this;
    }

    /**
     * Activate holding period expired transactions
     *
     * @return $this
     */
    private function unHoldTransactions(): HoldingPeriodExpirationCheck
    {
        $this->searchCriteriaBuilder
            ->addFilter(TransactionInterface::STATUS, Status::ON_HOLD)
            ->addFilter(TransactionInterface::HOLDING_PERIOD_EXPIRATION_DATE, 'expired');

        $expiredTransactions = $this->transactionRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();

        foreach ($expiredTransactions as $expiredTransaction) {
            $updatedTransaction = $this->transactionService->unHoldTransaction($expiredTransaction);
            $this->pointsSummaryService->addPointsSummaryToCustomer($updatedTransaction);

            $notifiedStatus = $this->customerRewardPointsService->sendNotification(
                $expiredTransaction->getCustomerId(),
                TransactionInterface::BALANCE_UPDATE_NOTIFIED,
                [
                    'balance' => $expiredTransaction->getBalance(),
                    'expiration_date' => $expiredTransaction->getExpirationDate(),
                    'comment' => $expiredTransaction->getCommentToCustomer()
                ],
                $expiredTransaction->getWebsiteId()
            );
            $customerBalance = $this->customerRewardPointsService->getCustomerRewardPointsBalance(
                $expiredTransaction->getCustomerId(),
                $expiredTransaction->getWebsiteId()
            );

            $updatedTransaction->setBalanceUpdateNotified($notifiedStatus);
            $updatedTransaction->setCurrentBalance($customerBalance);
            $this->transactionService->saveTransaction($updatedTransaction);
        }

        return $this;
    }
}
