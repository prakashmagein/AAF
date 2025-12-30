<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder\Eav;

use Amasty\ReportBuilder\Api\Data\SelectColumnInterface;
use Amasty\ReportBuilder\Api\EntityInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Data\SelectFactory;
use Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder\Eav\Column\ExpressionResolver;
use Amasty\ReportBuilder\Model\SelectResolver\ColumnResolverInterface;
use Amasty\ReportBuilder\Model\SelectResolver\RelationBuilder;
use Amasty\ReportBuilder\Model\SelectResolver\RelationStorageInterface;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnExpression;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\SelectEavColumn;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Sql\ColumnValueExpression;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SubSelectRelationBuilder
{
    public const ENTITY_ID = 'entity_id';
    public const AMASTY_REPORT_BUILDER_EAV_INDEX_TABLE = 'amasty_report_builder_eav_index';
    public const PRODUCT_ENTITY_NAME = 'catalog_product';

    public const RELATIONS = 'relations';

    public const SELECT = 'select';

    public const COLUMNS = 'columns';

    private const NAME_TO_ENTITY_MAP = [
        'customer' => CustomerInterface::class,
    ];

    /**
     * @var Provider
     */
    private $provider;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var array
     */
    private $entitiesWithRowId;

    /**
     * @var SelectFactory
     */
    private $selectFactory;

    /**
     * @var RelationResolver
     */
    private $attributeRelationResolver;

    /**
     * @var ExpressionResolver
     */
    private $columnExpressionResolver;

    /**
     * @var RelationStorageInterface
     */
    private $relationStorage;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var ColumnExpression
     */
    private $columnExpression;

    /**
     * @var string
     */
    private $linkField = self::ENTITY_ID;

    public function __construct(
        Provider $provider,
        MetadataPool $metadataPool,
        SelectFactory $selectFactory,
        RelationResolver $attributeRelationResolver,
        ExpressionResolver $columnExpressionResolver,
        RelationStorageInterface $relationStorage,
        ResourceConnection $resourceConnection,
        ColumnExpression $columnExpression,
        array $entitiesWithRowId = []
    ) {
        $this->provider = $provider;
        $this->metadataPool = $metadataPool;
        $this->entitiesWithRowId = $entitiesWithRowId;
        $this->selectFactory = $selectFactory;
        $this->attributeRelationResolver = $attributeRelationResolver;
        $this->columnExpressionResolver = $columnExpressionResolver;
        $this->relationStorage = $relationStorage;
        $this->resourceConnection = $resourceConnection;
        $this->columnExpression = $columnExpression;
    }

    public function build(SelectEavColumn $selectColumn): array
    {
        $columnId = $selectColumn->getColumnId();
        $entityName = $selectColumn->getEntityName();
        $columnAlias = $selectColumn->getAlias();
        $tableName = $this->getColumnTableName($selectColumn);
        $linkedFiled = $this->getLinkedField($selectColumn);
        $indexField = $this->getIndexField($columnId, $linkedFiled);
        $scheme = $this->provider->getEntityScheme();
        $entity = $scheme->getEntityByName($entityName);
        $alias = sprintf('%s_attribute', $columnAlias);
        $columnExpression = $this->columnExpression->collectExpression($selectColumn);

        $content = $this->buildContent($entity, $linkedFiled, $selectColumn, $indexField, $tableName);

        $relation = [
            RelationBuilder::TYPE => \Magento\Framework\DB\Select::LEFT_JOIN,
            RelationBuilder::ALIAS => $alias,
            RelationBuilder::TABLE => $content[self::SELECT],
            RelationBuilder::PARENT => $entityName,
            RelationBuilder::CONDITION => sprintf(
                '%s.%s = %s.%s',
                $alias,
                $linkedFiled,
                $entityName,
                $linkedFiled
            ),
            RelationBuilder::COLUMNS => [$columnAlias => new ColumnValueExpression($columnExpression)],
            RelationBuilder::CONTENT => $content
        ];

        $this->relationStorage->addRelation($relation);

        return $relation;
    }

    private function getLinkedField(SelectColumnInterface $selectColumn): string
    {
        $entityName = $selectColumn->getEntityName();
        if (array_key_exists($entityName, $this->entitiesWithRowId)) {
            $this->linkField = $this->metadataPool->getMetadata(
                $this->entitiesWithRowId[$entityName]
            )->getLinkField();
        } elseif (isset(self::NAME_TO_ENTITY_MAP[$entityName])) {
            $this->linkField = $this->metadataPool->getMetadata(
                self::NAME_TO_ENTITY_MAP[$entityName]
            )->getLinkField();
        }

        return $this->linkField;
    }

    private function getColumnTableName(SelectColumnInterface $selectColumn): string
    {
        $entityName = $selectColumn->getEntityName();
        $entityScheme = $this->provider->getEntityScheme();
        $entity = $entityScheme->getEntityByName($entityName);
        $columnEntity = $entityScheme->getColumnById($selectColumn->getColumnId());
        $backendType = $columnEntity->getType();
        $isEavEntity = in_array($backendType, ['int', 'decimal'])
            && $entityName === self::PRODUCT_ENTITY_NAME;
        $table = $isEavEntity
            ? self::AMASTY_REPORT_BUILDER_EAV_INDEX_TABLE
            : $entity->getMainTable();

        return sprintf('%s_%s', $this->resourceConnection->getTableName($table), $backendType);
    }

    private function getIndexField(string $columnId, string $linkedField): string
    {
        $entityScheme = $this->provider->getEntityScheme();
        $column = $entityScheme->getColumnById($columnId);
        $entityName = $column->getEntityName();
        $backendType = $column->getType();
        $isEavEntity = in_array($backendType, ['int', 'decimal'])
            && $entityName == self::PRODUCT_ENTITY_NAME;
        return  $isEavEntity ? self::ENTITY_ID : $linkedField;
    }

    /**
     * @param EntityInterface|null $entity
     * @param string $linkedFiled
     * @param SelectEavColumn $selectColumn
     * @param string $indexField
     * @param string $tableName
     *
     * @return array
     */
    private function buildContent(
        EntityInterface $entity,
        string $linkedFiled,
        SelectEavColumn $selectColumn,
        string $indexField,
        string $tableName
    ): array {
        $select = $this->selectFactory->create();
        $select->from(
            [
                $entity->getName() => $this->resourceConnection->getTableName($entity->getMainTable())
            ]
        );
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->columns(sprintf('%s.%s', $entity->getName(), $linkedFiled));

        $relations = $this->attributeRelationResolver->resolve($selectColumn, $linkedFiled, $indexField, $tableName);
        foreach ($relations as $relation) {
            $select->joinByType(
                $relation[RelationBuilder::TYPE],
                [$relation[RelationBuilder::ALIAS] => $relation[RelationBuilder::TABLE]],
                $relation[RelationBuilder::CONDITION]
            );
        }

        $columns = $this->columnExpressionResolver->resolve($selectColumn, $relations);
        $select->columns($columns);
        $select->group(sprintf('%s.%s', $entity->getName(), $linkedFiled));

        return [
            self::RELATIONS => $relations,
            self::SELECT => $select,
            self::COLUMNS => $columns
        ];
    }
}
