<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Api\EntityScheme;

interface ProviderInterface
{
    /**
     * @return SchemeInterface
     */
    public function getEntityScheme(): SchemeInterface;

    /**
     * return void
     */
    public function clear(): void;
}
