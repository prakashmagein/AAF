<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\EntityScheme\Builder\Filter\Column;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Api\EntityInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Builder\Filter\Entity\IsTableExistInterface;
use Amasty\ReportBuilder\Model\Report\Entity\IsRestricted as IsEntityRestricted;

class IsForeignColumnExist implements IsColumnExistInterface
{
    /**
     * @var IsTableExistInterface
     */
    private $isTableExist;

    /**
     * @var IsColumnExistInterface
     */
    private $isColumnExist;

    /**
     * @var IsEntityRestricted
     */
    private $isEntityRestricted;

    public function __construct(
        IsTableExistInterface $isTableExist,
        IsColumnExistInterface $isColumnExist,
        IsEntityRestricted $isEntityRestricted
    ) {
        $this->isTableExist = $isTableExist;
        $this->isColumnExist = $isColumnExist;
        $this->isEntityRestricted = $isEntityRestricted;
    }

    /**
     * @param array $schemeData
     * @param string $entityName
     * @param string $columnName
     * @return bool
     */
    public function execute(array $schemeData, string $entityName, string $columnName): bool
    {
        $link = $schemeData[$entityName][EntityInterface::COLUMNS][$columnName][ColumnInterface::LINK];
        [$referenceEntityName, $referenceColumnName] = explode('.', $link);

        return !$this->isEntityRestricted->execute($referenceEntityName)
            && $this->isTableExist->execute($schemeData, $referenceEntityName)
            && $this->isColumnExist->execute($schemeData, $referenceEntityName, $referenceColumnName);
    }
}
