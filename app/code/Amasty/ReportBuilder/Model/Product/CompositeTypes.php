<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Product;

class CompositeTypes
{
    /**
     * @var string[]
     */
    private $types;

    public function __construct(array $types = [])
    {
        $this->types = $types;
    }

    public function get(): array
    {
        return $this->types;
    }
}
