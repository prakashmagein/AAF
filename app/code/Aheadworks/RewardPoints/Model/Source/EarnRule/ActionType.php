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
namespace Aheadworks\RewardPoints\Model\Source\EarnRule;

use Aheadworks\RewardPoints\Model\EarnRule\Action\TypePool as ActionTypePool;
use Aheadworks\RewardPoints\Model\EarnRule\Action\TypeInterface as ActionTypeInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class ActionType
 * @package Aheadworks\RewardPoints\Model\Source\EarnRule
 */
class ActionType implements OptionSourceInterface
{
    /**
     * @var ActionTypePool
     */
    private $actionTypePool;

    /**
     * @var array
     */
    private $options;

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
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [];
            try {
                /** @var ActionTypeInterface $type */
                foreach ($this->actionTypePool->getTypes() as $code => $type) {
                    $this->options[] = [
                        'value' => $code,
                        'label' => __($type->getTitle())
                    ];
                }
            } catch (\Exception $exception) {
                $this->logger->critical($exception->getMessage());
            }
        }
        return $this->options;
    }
}
