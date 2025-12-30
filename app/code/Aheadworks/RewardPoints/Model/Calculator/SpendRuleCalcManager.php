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

namespace Aheadworks\RewardPoints\Model\Calculator;

use Aheadworks\RewardPoints\Model\Config;
use Aheadworks\RewardPoints\Model\Calculator\Spending\Calculator\General as GeneralCalculator;
use Aheadworks\RewardPoints\Model\Calculator\Spending\Calculator\CalculationRequestInterfaceFactory;
use Aheadworks\RewardPoints\Model\Calculator\Spending\Calculator\CalculationRequestInterface;
use Aheadworks\RewardPoints\Model\Calculator\Spending\SpendItemInterface;
use Magento\Quote\Model\Quote;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class SpendRuleCalcManager
 */
class SpendRuleCalcManager
{
    /**
     * SpendRuleCalcManager constructor.
     *
     * @param GeneralCalculator $generalCalculator
     * @param Config $config
     * @param CalculationRequestInterfaceFactory $calculationRequestFactory
     * @param Logger $logger
     */
    public function __construct(
        private GeneralCalculator $generalCalculator,
        private Config $config,
        private CalculationRequestInterfaceFactory $calculationRequestFactory,
        private Logger $logger
    ) {
    }

    /**
     * Retrieve calculation spending points value by rules
     * Update spend items data by rules
     *
     * @param SpendItemInterface[] $spendItems
     * @param int|null $customerId
     * @param int|null $websiteId
     * @param Quote|null $quote
     * @return SpendItemInterface[]
     */
    public function calculationByRules(array $spendItems, ?int $customerId, ?int $websiteId, ?Quote $quote): array
    {
        $result = $spendItems;
        if (!$websiteId) {
            return $result;
        }
        try {
            if (!$customerId) {
                $customerGroupId = $this->config->getDefaultCustomerGroupIdForGuest();
            }
            $calculationRequest = $this->prepareCalculationRequest(
                $customerId ?? null,
                $customerGroupId ?? null,
                $websiteId,
                $spendItems,
                $quote
            );

            $this->generalCalculator->calculateByRules($calculationRequest);
            $result = $this->distributeItemsShareCoveredPercent($calculationRequest->getItems());
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $result;
    }

    /**
     * Distributing share covered percent for items
     *
     * @param SpendItemInterface[] $spendItems
     * @return array
     */
    private function distributeItemsShareCoveredPercent(array $spendItems): array
    {
        foreach ($spendItems as $spendItem) {
            $appliedRuleIds = $spendItem->getAppliedRuleIds();
            $rulesCount = $appliedRuleIds ? count($appliedRuleIds) : 0;
            if ($rulesCount) {
                $itemPercent = (float)$spendItem->getShareCoveredPercent();
                $newPercent = round($itemPercent / $rulesCount, 2);
                $spendItem->setShareCoveredPercent($newPercent > 100 ? 100 : $newPercent);
            }
        }
        return $spendItems;
    }

    /**
     * Prepare calculation request
     *
     * @param int|null $customerId
     * @param int|null $customerGroupId
     * @param int|null $websiteId
     * @param SpendItemInterface[] $items
     * @param Quote|null $quote
     * @return CalculationRequestInterface
     */
    public function prepareCalculationRequest(
        ?int $customerId,
        ?int $customerGroupId,
        ?int $websiteId,
        array $items,
        Quote $quote = null
    ): CalculationRequestInterface {
        /** @var CalculationRequestInterface $calculationRequest */
        $calculationRequest = $this->calculationRequestFactory->create();

        $calculationRequest->setCustomerId((int)$customerId);
        $calculationRequest->setCustomerGroupId($customerGroupId);
        $calculationRequest->setItems($items);
        $calculationRequest->setWebsiteId($websiteId);
        if ($quote) {
            $calculationRequest->setQuote($quote);
        }

        return $calculationRequest;
    }
}
