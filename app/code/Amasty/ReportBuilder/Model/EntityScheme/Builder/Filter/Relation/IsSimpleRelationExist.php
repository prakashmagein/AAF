<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\EntityScheme\Builder\Filter\Relation;

use Amasty\ReportBuilder\Api\EntityInterface;
use Amasty\ReportBuilder\Api\RelationInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Builder\Filter\Column\IsColumnExistInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Builder\Filter\Entity\IsTableExistInterface;
use Amasty\ReportBuilder\Model\Report\Entity\IsRestricted as IsEntityRestricted;

class IsSimpleRelationExist implements IsRelationExistInterface
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

    public function execute(array $schemeData, string $entityName, string $relationName): bool
    {
        $relation = $schemeData[$entityName][EntityInterface::RELATIONS][$relationName];
        return !$this->isEntityRestricted->execute($relation[RelationInterface::NAME])
            && $this->isTableExist->execute($schemeData, $relation[RelationInterface::NAME])
            && $this->isColumnExist->execute(
                $schemeData,
                $relation[RelationInterface::NAME],
                $relation[RelationInterface::REFERENCE_COLUMN]
            );
    }
}
