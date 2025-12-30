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

namespace Aheadworks\RewardPoints\Model\Calculator\Spending\Calculator;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class CalculatorPool
 */
class Pool
{
    /**
     * @param CalculatorInterface[] $calculators
     */
    public function __construct(private $calculators = [])
    {
    }

    /**
     * Retrieves calculator by type
     *
     * @param string $type
     * @return CalculatorInterface $calculator
     * @throws LocalizedException
     */
    public function getCalculator(string $type): CalculatorInterface
    {
        if (!isset($this->calculators[$type])) {
            throw new LocalizedException(__('Unknown calculator: %s requested', $type));
        }

        $calculator = $this->calculators[$type];
        if (!$calculator instanceof CalculatorInterface) {
            throw new \InvalidArgumentException(__(
                'Calculator instance %s does not implement required interface.',
                CalculatorInterface::class
            )->render());
        }

        return $calculator;
    }
}
