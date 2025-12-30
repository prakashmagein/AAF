<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Api;

use Magento\Framework\Data\OptionSourceInterface;

interface ColumnInterface
{
    public const COLUMN_TABLE = 'amasty_report_builder_column';
    public const ID = 'id';
    public const ENTITY_NAME = 'entity_name';
    public const TITLE = 'title';
    public const NAME = 'name';
    public const PRIMARY = 'primary';
    public const TYPE = 'type';
    public const SOURCE_MODEL = 'source_model';
    public const OPTIONS = 'options';
    public const AGGREGATION_TYPE = 'aggregation_type';
    public const IS_DATE_FILTER = 'is_date_filter';
    public const ORDER = 'order';
    public const VISIBILITY = 'visibility';
    public const POSITION = 'position';
    public const CUSTOM_TITLE = 'custom_title';
    public const FILTER = 'filter';
    public const USE_FOR_PERIOD = 'use_for_period';
    public const USE_FOR_PERIOD_ATTRIBUTE = 'useForPeriod';
    public const FRONTEND_MODEL = 'frontend_model';
    public const FRONTEND_INPUT = 'frontend_input';
    public const FRONTEND_MODEL_ATTRIBUTE = 'frontendModel';
    public const ATTRIBUTE_ID = 'attribute_id';
    public const HIDDEN = 'hidden';
    public const COLUMN_TYPE = 'column_type';
    public const LINK = 'link';
    public const PARENT_COLUMN = 'parent_column';
    public const CUSTOM_EXPRESSION = 'custom_expression';
    public const UI_GRID_CLASS = 'ui_grid_class';

    public const ORDER_NONE = 0;
    public const ORDER_ASC = 1;
    public const ORDER_DESC = 2;

    /**
     * Method uses for initialization Column object from array
     *
     * @param array $columnConfig
     */
    public function init(array $columnConfig): void;

    public function setTitle(string $title): void;

    public function getTitle(): string;

    public function getCustomTitle(): string;

    public function setCustomTitle(string $customTitle): void;

    public function setName(string $name): void;

    public function getName(): string;

    public function setType(string $type): void;

    public function getType(): string;

    public function setSourceModel(string $sourceModel): void;

    public function getSourceModel(): ?string;

    public function setOptions(array $options): void;

    public function getOptions(): ?array;

    public function setAggregationType(string $aggregationType): void;

    public function getAggregationType(): string;

    public function getPrimary(): bool;

    public function setPrimary(bool $primary): void;

    public function getSource(): OptionSourceInterface;

    public function getFrontendModel(): string;

    public function setFrontendModel(string $frontendModel): void;

    public function setUseForPeriod(bool $useForPeriod): void;

    public function getUseForPeriod(): bool;

    public function getPosition(): int;

    public function setPosition(int $position): void;

    public function getEntityName(): ?string;

    public function setEntityName(string $entityName): void;

    public function getAlias(): string;

    public function getAttributeId(): ?int;

    public function getColumnId(): string;

    public function getAvailableAggregationTypes(): array;

    public function getColumnType(): string;

    public function setColumnType(string $columnType): void;

    public function setLink(string $link): void;

    public function getLink(): string;

    public function getParentColumn(): ?ColumnInterface;

    public function setParentColumn(ColumnInterface $column): void;

    public function setCustomExpression(string $customExpression): void;

    public function getCustomExpression(): string;

    /**
     * @return string|null
     */
    public function getUiGridClass(): ?string;

    /**
     * @param string $class
     */
    public function setUiGridClass(string $class): void;
}
