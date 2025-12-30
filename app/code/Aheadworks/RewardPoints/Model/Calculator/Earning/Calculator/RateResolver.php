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
namespace Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator;

use Aheadworks\RewardPoints\Api\Data\EarnRateInterface;
use Aheadworks\RewardPoints\Api\Data\EarnRateSearchResultsInterface;
use Aheadworks\RewardPoints\Api\EarnRateRepositoryInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class RateResolver
 * @package Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator
 */
class RateResolver
{
    /**
     * @var EarnRateRepositoryInterface
     */
    private $earnRateRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param EarnRateRepositoryInterface $earnRateRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        EarnRateRepositoryInterface $earnRateRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->earnRateRepository = $earnRateRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Get earn rate
     *
     * @param int $customerGroupId
     * @param int $websiteId
     * @return EarnRateInterface|null
     */
    public function getEarnRate($customerGroupId, $websiteId)
    {
        $this->searchCriteriaBuilder
            ->addFilter(
                EarnRateInterface::CUSTOMER_GROUP_ID,
                [GroupInterface::CUST_GROUP_ALL, $customerGroupId],
                'in'
            )
            ->addFilter(EarnRateInterface::WEBSITE_ID, $websiteId);

        /** @var EarnRateSearchResultsInterface $rateResult */
        $earnRateResult = $this->earnRateRepository->getList($this->searchCriteriaBuilder->create());
        $earnRates = $earnRateResult->getItems();

        $maxRate = 0;
        $maxEarnRate = null;
        /** @var EarnRateInterface $earnRate */
        foreach ($earnRates as $earnRate) {
            $currentRate = $earnRate->getPoints() / $earnRate->getBaseAmount();
            if ($currentRate > $maxRate) {
                $maxRate = $currentRate;
                $maxEarnRate = $earnRate;
            }
        }

        return $maxEarnRate;
    }
}
