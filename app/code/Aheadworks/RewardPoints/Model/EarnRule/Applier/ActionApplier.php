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
namespace Aheadworks\RewardPoints\Model\EarnRule\Applier;

use Aheadworks\RewardPoints\Api\Data\ActionInterface;
use Aheadworks\RewardPoints\Model\EarnRule\Action\Type as ActionType;
use Aheadworks\RewardPoints\Model\EarnRule\Action\TypePool as ActionTypePool;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class ActionApplier
 * @package Aheadworks\RewardPoints\Model\EarnRule\Applier
 */
class ActionApplier
{
    /**
     * @var ActionTypePool
     */
    private $actionTypePool;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param ActionTypePool $actionTypePool
     * @param Logger $logger
     */
    public function __construct(
        ActionTypePool $actionTypePool,
        Logger $logger
    ) {
        $this->actionTypePool = $actionTypePool;
        $this->logger = $logger;
    }

    /**
     * Apply action
     *
     * @param float $points
     * @param float $qty
     * @param ActionInterface $action
     * @return float
     */
    public function apply($points, $qty, $action)
    {
        try {
            /** @var ActionType $actionType */
            $actionType = $this->actionTypePool->getTypeByCode($action->getType());
            $processor = $actionType->getProcessor();

            $points = $processor->process($points, $qty, $action->getAttributes());
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }

        return $points;
    }
}
