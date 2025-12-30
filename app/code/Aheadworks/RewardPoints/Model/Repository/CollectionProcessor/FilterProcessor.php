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
namespace Aheadworks\RewardPoints\Model\Repository\CollectionProcessor;

use Aheadworks\RewardPoints\Model\Repository\CollectionProcessorInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Filter;

/**
 * Class FilterProcessor
 * @package Aheadworks\RewardPoints\Model\Repository\CollectionProcessor
 */
class FilterProcessor implements CollectionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($searchCriteria, $collection)
    {
        /** @var FilterGroup $filterGroup */
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            if ($filterGroup->getFilters()) {
                /** @var Filter $filter */
                foreach ($filterGroup->getFilters() as $filter) {
                    $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                    $fields[] = $filter->getField();
                    $conditions[] = [$condition => $filter->getValue()];
                }
                if ($fields) {
                    $collection->addFieldToFilter($fields, $conditions);
                }
            }
        }
    }
}
