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
namespace Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver;

use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemInterface;

/**
 * Class ItemProcessor
 * @package Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver
 */
class ItemProcessor implements ItemProcessorInterface
{
    /**
     * @var ItemProcessorPool
     */
    public $processorPool;

    /**
     * @param ItemProcessorPool $processorPool
     */
    public function __construct(
        ItemProcessorPool $processorPool
    ) {
        $this->processorPool = $processorPool;
    }

    /**
     * Get earn item
     *
     * @param ItemInterface[] $groupedItems
     * @param bool $beforeTax
     * @return EarnItemInterface
     * @throws \Exception
     */
    public function getEarnItem($groupedItems, $beforeTax = true)
    {
        $productType = null;
        foreach ($groupedItems as $item) {
            if ($item->getParentItem() == null) {
                $productType = $item->getProductType();
                break;
            }
        }

        /** @var ItemProcessorInterface $processor */
        $processor = $this->processorPool->getProcessorByCode($productType);

        return $processor->getEarnItem($groupedItems, $beforeTax);
    }
}
