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

namespace Aheadworks\RewardPoints\Block\Customer\RewardPointsBalance\Account;

use Aheadworks\RewardPoints\Api\Data\TransactionSearchResultsInterface;
use Aheadworks\RewardPoints\Api\Data\TransactionInterface;
use Aheadworks\RewardPoints\Api\TransactionRepositoryInterface;
use Aheadworks\RewardPoints\Block\Html\Pager;
use Aheadworks\RewardPoints\Model\Config\Frontend\Label\Resolver as LabelResolver;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Block\Account\Dashboard;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Framework\Api\SortOrderBuilder;
use Aheadworks\RewardPoints\Model\Comment\CommentPoolInterface;

class Transaction extends Dashboard
{
    /**
     * @var TransactionSearchResultsInterface
     */
    private $transactions;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param SubscriberFactory $subscriberFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $customerAccountManagement
     * @param TransactionRepositoryInterface $transactionRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param CommentPoolInterface $commentPool
     * @param LabelResolver $labelResolver
     * @param array $data
     */
    public function __construct(
        private readonly Context $context,
        CustomerSession $customerSession,
        SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly SortOrderBuilder $sortOrderBuilder,
        private readonly CommentPoolInterface $commentPool,
        private readonly LabelResolver $labelResolver,
        array $data = []
    ) {
        parent::__construct(
            $this->context,
            $customerSession,
            $subscriberFactory,
            $customerRepository,
            $customerAccountManagement,
            $data
        );
    }

    /**
     *  {@inheritDoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var Pager $pager */
        $pager = $this->getLayout()->createBlock(
            Pager::class,
            'aw_rp_transaction.pager'
        );

        $this->searchCriteriaBuilder->setCurrentPage($pager->getCurrentPage());
        $this->searchCriteriaBuilder->setPageSize($pager->getLimit());

        if ($this->getTransactions()) {
            $pager->setSearchResults($this->getTransactions());
            $this->setChild('pager', $pager);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Retrieve transaction list
     *
     * @return TransactionSearchResultsInterface
     */
    public function getTransactions()
    {
        if (empty($this->transactions)) {
            $customerId = $this->customerSession->getCustomerId();
            if ($customerId != null) {
                $this->searchCriteriaBuilder->addFilter(TransactionInterface::CUSTOMER_ID, $customerId);
                $this->sortOrderBuilder->setField(TransactionInterface::TRANSACTION_ID)->setDescendingDirection();
                $this->searchCriteriaBuilder->addSortOrder($this->sortOrderBuilder->create());
                $this->transactions = $this->transactionRepository->getList(
                    $this->searchCriteriaBuilder->create()
                );
            }
        }
        return $this->transactions;
    }

    /**
     * Retrieve renderer comment
     *
     * @param TransactionInterface $transaction
     * @return string
     */
    public function renderComment($transaction)
    {
        if ($commentInstance = $this->commentPool->get($transaction->getType())) {
            $commentLabel = $commentInstance->renderTranslatedComment(
                $transaction->getEntities(),
                null,
                $transaction->getCommentToCustomerPlaceholder()
                    ? $transaction->getCommentToCustomerPlaceholder()
                    : $transaction->getCommentToCustomer(),
                true,
                true
            );
        }
        if (empty($commentLabel)) {
            $commentLabel = $transaction->getCommentToCustomer();
        }
        return $commentLabel;
    }

    /**
     * Format date in short format
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::MEDIUM);
    }

    /**
     * Get label name reward points
     *
     * @return string
     * @throws LocalizedException
     */
    public function getLabelNameRewardPoints(): string
    {
        $websiteId = (int)$this->context->getStoreManager()->getWebsite()->getId();

        return $this->labelResolver->getLabelNameRewardPoints($websiteId);
    }
}
