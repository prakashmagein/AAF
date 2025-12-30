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

namespace Aheadworks\RewardPoints\Model\Validator\Product\Price;

use Magento\Framework\Exception\ConfigurationMismatchException;

class PriceItemProcessorPool
{
    /**
     * Array key for default processor
     */
    private const DEFAULT_PROCESSOR_CODE = 'default';

    /**
     * @param PriceItemProcessorInterface[] $processors
     */
    public function __construct(
        private readonly array $processors = []
    ) {
    }

    /**
     * Get processor by code
     *
     * @param string $code
     * @return PriceItemProcessorInterface
     * @throws ConfigurationMismatchException
     */
    public function getProcessorByCode(string $code): PriceItemProcessorInterface
    {
        if (!isset($this->processors[$code])) {
            $code = self::DEFAULT_PROCESSOR_CODE;
        }
        $processor = $this->processors[$code];
        if (!$processor instanceof PriceItemProcessorInterface) {
            throw new ConfigurationMismatchException(
                __('Item processor must implements %1', PriceItemProcessorInterface::class)
            );
        }

        return $processor;
    }
}
