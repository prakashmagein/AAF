<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model;

use Amasty\Base\Model\ConfigProviderAbstract;

class ConfigProvider extends ConfigProviderAbstract
{
    public const EXCLUDED_ENTITIES = 'general/excluded_entities';

    /**
     * @var string
     */
    protected $pathPrefix = 'amasty_report_builder/';

    public function getExcludedEntities(): array
    {
        $excludedEntities = $this->getValue(self::EXCLUDED_ENTITIES);
        if (!$excludedEntities) {
            return [];
        }

        return explode(',', $excludedEntities);
    }
}
