<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\EntityScheme\Builder;

use Amasty\ReportBuilder\Api\EntityInterface;
use Amasty\ReportBuilder\Api\EntityScheme\BuilderInterface;
use Amasty\ReportBuilder\Api\RelationInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Builder\Filter\Column\IsColumnExistInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Builder\Filter\Entity\IsTableExistInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Builder\Filter\Relation\IsRelationExistInterface;
use Amasty\ReportBuilder\Model\Report\Entity\IsRestricted as IsEntityRestricted;

class FilterBuilder implements BuilderInterface
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
     * @var IsRelationExistInterface
     */
    private $isRelationExist;

    /**
     * @var IsEntityRestricted
     */
    private $isEntityRestricted;

    public function __construct(
        IsTableExistInterface $isTableExist,
        IsColumnExistInterface $isColumnExist,
        IsRelationExistInterface $isRelationExist,
        IsEntityRestricted $isEntityRestricted
    ) {
        $this->isTableExist = $isTableExist;
        $this->isColumnExist = $isColumnExist;
        $this->isRelationExist = $isRelationExist;
        $this->isEntityRestricted = $isEntityRestricted;
    }

    /**
     * @param array $data
     * @return array
     */
    public function build(array $data = []): array
    {
        foreach ($data as $entityName => $entityData) {
            if (!$this->isTableExist->execute($data, $entityName)
                || $this->isEntityRestricted->execute($entityName)
            ) {
                unset($data[$entityName]);
                continue;
            }

            foreach ($entityData[EntityInterface::COLUMNS] as $columnName => $columnData) {
                if (!$this->isColumnExist->execute($data, $entityName, $columnName)) {
                    unset($data[$entityName][EntityInterface::COLUMNS][$columnName]);
                    continue;
                }
            }

            foreach ($entityData[EntityInterface::RELATIONS] as $relationName => $relation) {
                if (!$this->isRelationExist->execute($data, $entityName, $relationName)) {
                    unset($data[$entityName][EntityInterface::RELATIONS][$relationName]);
                    continue;
                }
            }
        }

        return $data;
    }
}
