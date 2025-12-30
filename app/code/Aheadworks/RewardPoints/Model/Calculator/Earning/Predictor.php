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

namespace Aheadworks\RewardPoints\Model\Calculator\Earning;

use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\CalculationRequestInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\CalculationRequestInterfaceFactory;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\General as GeneralCalculator;
use Aheadworks\RewardPoints\Model\Calculator\ResultInterface;
use Aheadworks\RewardPoints\Model\Config;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Predictor
 */
class Predictor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var GeneralCalculator
     */
    private $generalCalculator;

    /**
     * @var CalculationRequestInterfaceFactory
     */
    private $calculationRequestFactory;

    /**
     * @param Config $config
     * @param GeneralCalculator $generalCalculator
     */
    public function __construct(
        Config                      $config,
        GeneralCalculator           $generalCalculator,
        CalculationRequestInterfaceFactory $calculationRequestFactory
    ) {
        $this->config = $config;
        $this->generalCalculator = $generalCalculator;
        $this->calculationRequestFactory = $calculationRequestFactory;
    }

    /**
     * Calculate max possible earning points for a customer
     *
     * @param EarnItemInterface[] $items
     * @param int $customerId
     * @param int $websiteId
     * @param bool|false $mergeRuleIds
     * @return ResultInterface
     */
    public function calculateMaxPointsForCustomer($items, $customerId, $websiteId, $mergeRuleIds = false)
    {

        if (empty($items)) {
            return $this->generalCalculator->getEmptyResult();
        }

        $calculationRequest = $this->prepareCalculationRequest($customerId, null, $websiteId);

        /** @var ResultInterface[] $results */
        $results = [];
        foreach ($items as $item) {
            $calculationRequest->setItems([$item]);
            $result = $this->generalCalculator->calculate([$item], (int)$customerId, $websiteId);
            $calculationRequest->setPoints($result->getPoints());
            $results[] = $this->generalCalculator->calculatePointsByRules($result, $calculationRequest);
        }

        /** @var ResultInterface $maxResult */
        $maxResult = reset($results);
        /** @var ResultInterface $result */
        foreach ($results as $result) {
            if ($result->getPoints() > $maxResult->getPoints()) {
                $maxResult = $result;
            }
        }

        if ($mergeRuleIds) {
            $maxResult->setAppliedRuleIds($this->getMergedAppliedRuleIds($results));
        }

        return $maxResult;
    }

    /**
     * Calculate max possible earning points for a customer group
     *
     * @param EarnItemInterface[] $items
     * @param int $websiteId
     * @param int $customerGroupId
     * @param bool|false $mergeRuleIds
     * @return ResultInterface
     */
    public function calculateMaxPointsForCustomerGroup(
        array $items,
              $websiteId,
        int   $customerGroupId,
        bool  $mergeRuleIds = false
    ): ResultInterface {

        if (empty($items)) {
            return $this->generalCalculator->getEmptyResult();
        }

        $calculationRequest = $this->prepareCalculationRequest(null, $customerGroupId, $websiteId);

        /** @var ResultInterface[] $results */
        $results = [];
        foreach ($items as $item) {
            $calculationRequest->setItems([$item]);
            $result = $this->generalCalculator->calculateByCustomerGroup([$item], (int)$customerGroupId, $websiteId);
            $calculationRequest->setPoints($result->getPoints());
            $results[] = $this->generalCalculator->calculatePointsByRules($result, $calculationRequest);
        }

        /** @var ResultInterface $maxResult */
        $maxResult = reset($results);
        foreach ($results as $result) {
            if ($result->getPoints() > $maxResult->getPoints()) {
                $maxResult = $result;
            }
        }

        if ($mergeRuleIds) {
            $maxResult->setAppliedRuleIds($this->getMergedAppliedRuleIds($results));
        }

        return $maxResult;
    }

    /**
     * Calculate max possible earning points for a guest
     *
     * @param EarnItemInterface[] $items
     * @param int $websiteId
     * @param bool|false $mergeRuleIds
     * @return ResultInterface
     */
    public function calculateMaxPointsForGuest($items, $websiteId, $mergeRuleIds = false)
    {
        $customerGroupId = $this->config->getDefaultCustomerGroupIdForGuest();
        $maxResult = $this->calculateMaxPointsForCustomerGroup($items, $websiteId, (int)$customerGroupId, $mergeRuleIds);
        return $maxResult;
    }

    /**
     * Get merged applied rule IDs
     *
     * @param ResultInterface[] $results
     * @return int[]
     */
    private function getMergedAppliedRuleIds(array $results): array
    {
        $appliedRuleIds = [];
        foreach ($results as $result) {
            $appliedRuleIds[] = $result->getAppliedRuleIds();
        }

        return array_unique(array_merge(...$appliedRuleIds));
    }

    /**
     * Prepare calculation request
     *
     * @param int|null $customerId
     * @param int|null $customerGroupId
     * @param int|null $websiteId
     * @param bool $needCalculateCartRule
     * @return CalculationRequestInterface
     */
    public function prepareCalculationRequest(
        $customerId,
        $customerGroupId,
        $websiteId,
        $isNeedCalculateCartRule = false
    ): CalculationRequestInterface {

        /** @var CalculationRequestInterface $calculationRequest */
        $calculationRequest = $this->calculationRequestFactory->create();

        $calculationRequest->setCustomerId($customerId);
        $calculationRequest->setCustomerGroupId($customerGroupId);
        $calculationRequest->setWebsiteId($websiteId);
        $calculationRequest->setIsNeedCalculateCartRule($isNeedCalculateCartRule);

        return $calculationRequest;
    }
}
