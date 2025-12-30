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
namespace Aheadworks\RewardPoints\Api;

use Aheadworks\RewardPoints\Api\Data\EarnRateInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface EarnRateRepositoryInterface
{
    /**
     * Retrieve earn rate by id
     *
     * @param  int $id
     * @return EarnRateInterface
     */
    public function getById($id);

    /**
     * Retrieve earn rate
     *
     * @param  int $customerGroupId
     * @param  int $lifetimeSalesAmount
     * @param  string $websiteId
     * @return EarnRateInterface
     */
    public function get($customerGroupId, $lifetimeSalesAmount, $websiteId = null);

    /**
     * Save earn rate
     *
     * @param  EarnRateInterface $earnRate
     * @return EarnRateInterface
     */
    public function save(EarnRateInterface $earnRate);

    /**
     * Delete earn rate by id
     *
     * @param  int $id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Delete earn rate
     *
     * @param EarnRateInterface $earnRate
     * @return boolean
     */
    public function delete(EarnRateInterface $earnRate);

    /**
     * Retrieve earn rates matching the specified criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @return EarnRateSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria);
}
