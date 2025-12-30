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

/**
 * Usual EAV value table relation builder.
 * If EAV have store based scope then this builder shouldn't be used
 */
class NonStoreDataBuilder implements BuilderInterface
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
        $tableAlias = sprintf('%s_table', $selectColumn->getAlias());

        return [
            RelationBuilder::TYPE => Select::LEFT_JOIN,
            RelationBuilder::ALIAS => $tableAlias,
            RelationBuilder::TABLE => $tableName,
            RelationBuilder::COLUMNS => sprintf('%s.value', $tableAlias),
            RelationBuilder::CONDITION => sprintf(
                '%s.%s = %s.%s AND %1$s.attribute_id = \'%d\'',
                $tableAlias,
                $indexField,
                $selectColumn->getEntityName(),
                $linkedField,
                $selectColumn->getAttributeId()
            )
        ];
    }

    public function isApplicable(string $tableName): bool
    {
        return !$this->connectionResource->getConnection()->tableColumnExists($tableName, 'store_id');
    }
}
