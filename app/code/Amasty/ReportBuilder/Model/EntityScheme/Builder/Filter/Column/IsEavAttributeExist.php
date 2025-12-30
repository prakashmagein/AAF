<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\EntityScheme\Builder\Filter\Column;

use Magento\Eav\Model\Config;

class IsEavAttributeExist implements IsColumnExistInterface
{
    /**
     * @var Config
     */
    private $eavConfig;

    public function __construct(Config $eavConfig)
    {
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param array $schemeData
     * @param string $entityName
     * @param string $columnName
     * @return bool
     */
    public function execute(array $schemeData, string $entityName, string $columnName): bool
    {
        $attributesForEntity = $this->eavConfig->getEntityAttributes($entityName);

        return array_key_exists($columnName, $attributesForEntity);
    }
}
