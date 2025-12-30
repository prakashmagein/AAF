<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder\Eav\Relation;

use Amasty\ReportBuilder\Model\SelectResolver\ColumnResolverInterface;
use Amasty\ReportBuilder\Model\SelectResolver\RelationBuilder;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\SelectEavColumn;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Store\Model\Store;

/**
 * EAV value table relation builder fore default store value
 */
class DefaultDataBuilder implements BuilderInterface
{
    /**
     * @var ResourceConnection
     */
    private $connectionResource;

    public function __construct(
        ResourceConnection $connectionResource
    ) {
        $this->connectionResource = $connectionResource;
    }

    public function execute(
        SelectEavColumn $selectColumn,
        string $linkedField,
        string $indexField,
        string $tableName
    ): array {
        $tableAlias = sprintf('%s_default_table', $selectColumn->getAlias());

        return [
            RelationBuilder::TYPE => Select::LEFT_JOIN,
            RelationBuilder::ALIAS => $tableAlias,
            RelationBuilder::TABLE => $tableName,
            RelationBuilder::COLUMNS => sprintf('%s.value', $tableAlias),
            RelationBuilder::CONDITION => sprintf(
                '%s.%s = %s.%s AND %1$s.attribute_id = \'%d\' AND %1$s.store_id = %d',
                $tableAlias,
                $indexField,
                $selectColumn->getEntityName(),
                $linkedField,
                $selectColumn->getAttributeId(),
                Store::DEFAULT_STORE_ID
            )
        ];
    }

    public function isApplicable(string $tableName): bool
    {
        return $this->connectionResource->getConnection()->tableColumnExists($tableName, 'store_id');
    }
}
