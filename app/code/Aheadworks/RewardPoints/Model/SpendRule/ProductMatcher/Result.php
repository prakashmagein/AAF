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

namespace Aheadworks\RewardPoints\Model\SpendRule\ProductMatcher;

use Aheadworks\RewardPoints\Model\SpendRule\ProductMatcher\Result\Item;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class Result
 */
class Result extends AbstractSimpleObject
{
    /**#@+
     * Constants for keys.
     */
    public const ITEMS = 'items';
    public const TOTAL_COUNT = 'total_count';
    /**#@-*/

    /**
     * Get items
     *
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->_get(self::ITEMS);
    }

    /**
     * Set items
     *
     * @param Item[] $items
     * @return $this
     */
    public function setItems(array $items): Result
    {
        return $this->setData(self::ITEMS, $items);
    }

    /**
     * Get total count
     *
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->_get(self::TOTAL_COUNT);
    }

    /**
     * Set total count
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount(int $totalCount): Result
    {
        return $this->setData(self::TOTAL_COUNT, $totalCount);
    }
}
