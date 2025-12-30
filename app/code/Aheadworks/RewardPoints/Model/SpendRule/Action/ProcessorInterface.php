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

namespace Aheadworks\RewardPoints\Model\SpendRule\Action;

use Aheadworks\RewardPoints\Model\Calculator\Spending\SpendItemInterface;
use Magento\Framework\Api\AttributeInterface;

/**
 * Interface ProcessorInterface
 */
interface ProcessorInterface
{
    /**
     * Process
     *
     * @param SpendItemInterface $spendItem
     * @param AttributeInterface[] $attributes
     * @param int|null $customerId
     * @param int|null $websiteId
     * @return bool
     */
    public function process(
        SpendItemInterface $spendItem,
        array $attributes,
        ?int $customerId = null,
        ?int $websiteId = null
    ): bool;
}
