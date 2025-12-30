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

namespace Aheadworks\RewardPoints\Model\ResourceModel\SpendRule\Indexer\Product\DataCollector;

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Model\Indexer\SpendRule\ProductInterface as SpendRuleProductInterface;
use Aheadworks\RewardPoints\Model\SpendRule\ProductMatcher\Result\Item as ProductMatcherResultItem;

/**
 * Class ProductMatcherItemsProcessor
 */
class ProductMatcherItemsProcessor
{
    /**
     * Prepare product matcher result items data
     *
     * @param ProductMatcherResultItem[] $items
     * @param SpendRuleInterface $rule
     * @return array
     */
    public function prepareData(array $items, SpendRuleInterface $rule): array
    {
        $data = [];
        $customerGroupIds = $rule->getCustomerGroupIds();

        /** @var ProductMatcherResultItem $item */
        foreach ($items as $item) {
            $websiteIds = $item->getWebsiteIds();
            foreach ($websiteIds as $websiteId) {
                foreach ($customerGroupIds as $customerGroupId) {
                    $data[] = [
                        SpendRuleProductInterface::RULE_ID => $rule->getId(),
                        SpendRuleProductInterface::FROM_DATE => $rule->getFromDate(),
                        SpendRuleProductInterface::TO_DATE => $rule->getToDate(),
                        SpendRuleProductInterface::CUSTOMER_GROUP_ID => $customerGroupId,
                        SpendRuleProductInterface::WEBSITE_ID => $websiteId,
                        SpendRuleProductInterface::PRODUCT_ID => $item->getProductId(),
                        SpendRuleProductInterface::PRIORITY => $rule->getPriority(),
                    ];
                }
            }
        }

        return $data;
    }
}
