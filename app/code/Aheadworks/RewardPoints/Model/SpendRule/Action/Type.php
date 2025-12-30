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

/**
 * Class Type
 */
class Type implements TypeInterface
{
    /**
     * Type constructor.
     *
     * @param ProcessorInterface $processor
     * @param array $attributeCodes
     */
    public function __construct(
        private ProcessorInterface $processor,
        private $attributeCodes = []
    ) {
    }

    /**
     * Get processor
     *
     * @return ProcessorInterface
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * Get attribute codes
     *
     * @return string[]
     */
    public function getAttributeCodes()
    {
        return $this->attributeCodes;
    }
}
