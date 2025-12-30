<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\EntityScheme\Builder\Filter\Entity;

use Amasty\ReportBuilder\Api\EntityInterface;
use Amasty\ReportBuilder\Model\ResourceModel\Table\IsTableExist as IsTableExistResource;

class IsTableExist implements IsTableExistInterface
{
    /**
     * @var IsTableExistResource
     */
    private $isTableExistResource;

    public function __construct(IsTableExistResource $isTableExistResource)
    {
        $this->isTableExistResource = $isTableExistResource;
    }

    /**
     * @param array $schemeData
     * @param string $entityName
     * @return bool
     */
    public function execute(array $schemeData, string $entityName): bool
    {
        $tableName = $schemeData[$entityName][EntityInterface::MAIN_TABLE] ?? null;
        return $tableName && $this->isTableExistResource->execute($tableName);
    }
}
