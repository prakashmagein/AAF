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
namespace Aheadworks\RewardPoints\Model\Data\Command\Transaction;

use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Aheadworks\RewardPoints\Model\Data\CommandInterface;
use Aheadworks\RewardPoints\Model\Data\Processor\Post\Transaction\Processor
    as TransactionPostDataProcessor;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Create
 */
class Create implements CommandInterface
{
    /**
     * @var TransactionPostDataProcessor
     */
    private $transactionPostDataProcessor;

    /**
     * @var CustomerRewardPointsManagementInterface
     */
    private $customerRewardPointsService;

    /**
     * @param TransactionPostDataProcessor $transactionPostDataProcessor
     * @param CustomerRewardPointsManagementInterface $customerRewardPointsService
     */
    public function __construct(
        TransactionPostDataProcessor $transactionPostDataProcessor,
        CustomerRewardPointsManagementInterface $customerRewardPointsService
    ) {
        $this->transactionPostDataProcessor = $transactionPostDataProcessor;
        $this->customerRewardPointsService = $customerRewardPointsService;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        $data = $this->transactionPostDataProcessor->process($data);
        $customerSelection = $this->transactionPostDataProcessor->filter($data);
        if (!empty($customerSelection)) {
            foreach ($customerSelection as $transactionData) {
                $this->customerRewardPointsService->resetCustomer();
                $this->customerRewardPointsService->saveAdminTransaction($transactionData);
            }
        } else {
            throw new LocalizedException(
                __('Invalid customer selection')
            );
        }
        return true;
    }
}
