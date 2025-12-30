<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Entity;

use Amasty\ReportBuilder\Model\ConfigProvider;

class IsRestricted
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function execute(string $entityName): bool
    {
        return in_array($entityName, $this->configProvider->getExcludedEntities(), true);
    }
}
