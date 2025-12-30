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

namespace Aheadworks\RewardPoints\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface SpendRuleSearchResultsInterface
 * @api
 */
interface SpendRuleSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get rule list
     *
     * @return \Aheadworks\RewardPoints\Api\Data\SpendRuleInterface[]
     */
    public function getItems();

    /**
     * Set rule list
     *
     * @param \Aheadworks\RewardPoints\Api\Data\SpendRuleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
