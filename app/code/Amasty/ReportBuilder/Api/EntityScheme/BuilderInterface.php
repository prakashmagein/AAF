<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Api\EntityScheme;

interface BuilderInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function build(array $data = []): array;
}
