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

namespace Aheadworks\RewardPoints\Model\Meta;

/**
 * Class Processor
 */
class Processor implements ProcessorInterface
{
    /**
     * Processor constructor.
     *
     * @param ProcessorInterface[] $processors
     */
    public function __construct(private array $processors = [])
    {
    }

    /**
     * Process meta
     *
     * @param array $meta
     * @param array $data
     * @return array
     */
    public function process($meta, $data)
    {
        foreach ($this->processors as $processor) {
            $meta = $processor->process($meta, $data);
        }
        return $meta;
    }
}
