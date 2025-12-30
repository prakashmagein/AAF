<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\EntityScheme\Builder\Column;

interface TypeModifierInterface
{
    /**
     * Modify column data by column type.
     *
     * @param array $data
     * @return array
     */
    public function execute(array $data): array;
}
