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
namespace Aheadworks\RewardPoints\Model\Customer\Processor;

use Aheadworks\RewardPoints\Api\Data\TransactionInterface;
use Aheadworks\RewardPoints\Model\Data\CommandInterface;
use Aheadworks\RewardPoints\Model\Data\Filter\Transaction\CustomerSelection
    as TransactionCustomerSelectionFilter;
use Magento\Customer\Api\CustomerNameGenerationInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Saving
 *
 * @package Aheadworks\RewardPoints\Model\Customer\Processor
 */
class Saving
{
    /**#@+
     * Constants defined for fetching data from the request
     */
    const AW_REWARD_POINTS_CUSTOMER_FIELDSET_NAME = 'aw_reward_points_customer_section';
    const AW_REWARD_POINTS_TRANSACTION_DATA_KEY = 'balance_adjustment';
    /**#@-*/

    /**
     * @var CommandInterface
     */
    private $createCommand;

    /**
     * @var CustomerNameGenerationInterface
     */
    private $customerNameGeneration;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param CommandInterface $createCommand
     * @param CustomerNameGenerationInterface $customerNameGeneration
     * @param Logger $logger
     */
    public function __construct(
        CommandInterface $createCommand,
        CustomerNameGenerationInterface $customerNameGeneration,
        Logger $logger
    ) {
        $this->createCommand = $createCommand;
        $this->customerNameGeneration = $customerNameGeneration;
        $this->logger = $logger;
    }

    /**
     * Process reward points balance adjustment after customer saving
     *
     * @param CustomerInterface $customer
     * @param RequestInterface $request
     */
    public function processBalanceAdjustment($customer, $request)
    {
        try {
            $awRewardPointsCustomerSectionData = $request->getParam(
                self::AW_REWARD_POINTS_CUSTOMER_FIELDSET_NAME,
                []
            );
            $transactionData =
                $awRewardPointsCustomerSectionData[self::AW_REWARD_POINTS_TRANSACTION_DATA_KEY] ?? [];

            if ($this->isNeedToCreateTransaction($transactionData)) {
                $transactionData[TransactionCustomerSelectionFilter::DEFAULT_FIELD_NAME] = [
                    [
                        TransactionInterface::CUSTOMER_ID => $customer->getId(),
                        TransactionInterface::CUSTOMER_NAME => $this->customerNameGeneration->getCustomerName($customer),
                        TransactionInterface::CUSTOMER_EMAIL => $customer->getEmail(),
                        TransactionInterface::WEBSITE_ID => $customer->getWebsiteId(),
                    ]
                ];
                $this->createCommand->execute($transactionData);
            }
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
    }

    /**
     * Check if transaction data contains all required parameters
     *
     * @param array $transactionData
     * @return bool
     */
    private function isNeedToCreateTransaction($transactionData)
    {
        return (
            isset(
                $transactionData[TransactionInterface::BALANCE],
                $transactionData[TransactionInterface::WEBSITE_ID]
            ) && !empty($transactionData[TransactionInterface::BALANCE])
            && !empty($transactionData[TransactionInterface::WEBSITE_ID])
        );
    }
}
