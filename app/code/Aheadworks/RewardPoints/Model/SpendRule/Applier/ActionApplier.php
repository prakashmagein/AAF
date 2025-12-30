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

namespace Aheadworks\RewardPoints\Model\SpendRule\Applier;

use Aheadworks\RewardPoints\Api\Data\ActionInterface;
use Aheadworks\RewardPoints\Model\Calculator\Spending\SpendItemInterface;
use Aheadworks\RewardPoints\Model\SpendRule\Action\Type as ActionType;
use Aheadworks\RewardPoints\Model\SpendRule\Action\TypePool as ActionTypePool;
use Psr\Log\LoggerInterface as Logger;
use Aheadworks\RewardPoints\Model\Calculator\Spending\Calculator\RateResolver;

/**
 * Class ActionApplier
 */
class ActionApplier
{
    /**
     * @param ActionTypePool $actionTypePool
     * @param Logger $logger
     * @param RateResolver $rateResolver
     */
    public function __construct(
        private ActionTypePool $actionTypePool,
        private Logger $logger,
        private RateResolver $rateResolver
    ) {
    }

    /**
     * Apply action
     *
     * @param ActionInterface[] $action
     * @param int|null $customerId
     * @param int|null $websiteId
     * @param SpendItemInterface $spendItem
     * @return bool
     */
    public function apply(
        array $action,
        ?int $customerId,
        ?int $websiteId,
        SpendItemInterface $spendItem
    ): bool {
        try {
            $result = false;
            $actionTypesData = $this->getActionItemsByType($action);
            foreach ($actionTypesData as $type => $actionItems) {
                /** @var ActionType $actionType */
                $actionType = $this->actionTypePool->getTypeByCode($type);
                $processor = $actionType->getProcessor();
                foreach ($actionItems as $actionItem) {
                    $result = $processor->process(
                        $spendItem,
                        $actionItem->getAttributes(),
                        $customerId,
                        $websiteId
                    );
                }
            }
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }

        return $result;
    }

    /**
     * Get action items by type
     *
     * @param ActionInterface[] $action
     * @return array
     */
    private function getActionItemsByType(array $action): array
    {
        $result = [];
        foreach ($action as $actionItem) {
            $result[$actionItem->getType()][] = $actionItem;
        }
        return $result;
    }
}
