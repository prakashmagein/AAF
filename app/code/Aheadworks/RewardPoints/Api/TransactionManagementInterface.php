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

namespace Aheadworks\RewardPoints\Api;

use Aheadworks\RewardPoints\Api\Data\TransactionInterface;
use Aheadworks\RewardPoints\Model\Source\Transaction\Type as TransactionType;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * @api
 */
interface TransactionManagementInterface
{
    /**
     * Create empty transaction instance
     *
     * @return TransactionInterface
     */
    public function createEmptyTransaction();

    /**
     * Create transaction
     *
     * @param CustomerInterface $customer
     * @param int $balance
     * @param string $expirationDate
     * @param string $commentToCustomer
     * @param string $commentToCustomerPlaceholder
     * @param string $commentToAdmin
     * @param string $commentToAdminPlaceholder
     * @param int $websiteId
     * @param int $transactionType
     * @param array $arguments
     * @return TransactionInterface
     * @throws CouldNotSaveException
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function createTransaction(
        CustomerInterface $customer,
        $balance,
        $expirationDate = null,
        $commentToCustomer = null,
        $commentToCustomerPlaceholder = null,
        $commentToAdmin = null,
        $commentToAdminPlaceholder = null,
        $websiteId = null,
        $transactionType = TransactionType::BALANCE_ADJUSTED_BY_ADMIN,
        $arguments = []
    );

    /**
     * Save transaction
     *
     * @param TransactionInterface $transaction
     * @param array $arguments
     * @return TransactionInterface
     * @throws CouldNotSaveException
     */
    public function saveTransaction(TransactionInterface $transaction, $arguments = []);

    /**
     * Hold transaction
     *
     * @param TransactionInterface $transaction
     * @return TransactionInterface
     * @throws CouldNotSaveException
     */
    public function holdTransaction(TransactionInterface $transaction): TransactionInterface;

    /**
     * Unhold transaction
     *
     * @param TransactionInterface $transaction
     * @return TransactionInterface
     * @throws CouldNotSaveException
     */
    public function unHoldTransaction(TransactionInterface $transaction): TransactionInterface;

    /**
     * Cancel transaction
     *
     * @param TransactionInterface $transaction
     * @return TransactionInterface
     * @throws CouldNotSaveException
     */
    public function cancelTransaction(TransactionInterface $transaction): TransactionInterface;
}
