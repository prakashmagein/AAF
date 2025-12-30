<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Api\Data;

interface ReportColumnInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**
     * String constants for property names
     */
    public const ID = 'id';

    public const COLUMN_ID = 'column_id';

    public const REPORT_ID = 'report_id';

    public const IS_DATE_FILTER = 'is_date_filter';

    public const ORDER = 'order';

    public const FILTER = 'filter';

    public const VISIBILITY = 'visibility';

    public const POSITION = 'position';

    public const CUSTOM_TITLE = 'custom_title';

    public const AGGREGATION_TYPE = 'aggregation_type';

    /**
     * Getter for Id.
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Setter for Id.
     *
     * @param int|null $id
     *
     * @return void
     */
    public function setId(?int $id): void;

    /**
     * Getter for ColumnId.
     *
     * @return string|null
     */
    public function getColumnId(): ?string;

    /**
     * Setter for ColumnId.
     *
     * @param string|null $columnId
     *
     * @return void
     */
    public function setColumnId(?string $columnId): void;

    /**
     * Getter for ReportId.
     *
     * @return int|null
     */
    public function getReportId(): ?int;

    /**
     * Setter for ReportId.
     *
     * @param int|null $reportId
     *
     * @return void
     */
    public function setReportId(?int $reportId): void;

    /**
     * Getter for IsDateFilter.
     *
     * @return bool|null
     */
    public function getIsDateFilter(): ?bool;

    /**
     * Setter for IsDateFilter.
     *
     * @param bool|null $isDateFilter
     *
     * @return void
     */
    public function setIsDateFilter(?bool $isDateFilter): void;

    /**
     * Getter for Order.
     *
     * @return int|null
     */
    public function getOrder(): ?int;

    /**
     * Setter for Order.
     *
     * @param int|null $order
     *
     * @return void
     */
    public function setOrder(?int $order): void;

    /**
     * Getter for Filter.
     *
     * @return string|null
     */
    public function getFilter(): ?string;

    /**
     * Setter for Filter.
     *
     * @param string|null $filter
     *
     * @return void
     */
    public function setFilter(?string $filter): void;

    /**
     * Getter for Visibility.
     *
     * @return bool|null
     */
    public function getVisibility(): ?bool;

    /**
     * Setter for Visibility.
     *
     * @param bool|null $visibility
     *
     * @return void
     */
    public function setVisibility(?bool $visibility): void;

    /**
     * Getter for Position.
     *
     * @return int|null
     */
    public function getPosition(): ?int;

    /**
     * Setter for Position.
     *
     * @param int|null $position
     *
     * @return void
     */
    public function setPosition(?int $position): void;

    /**
     * Getter for CustomTitle.
     *
     * @return string|null
     */
    public function getCustomTitle(): ?string;

    /**
     * Setter for CustomTitle.
     *
     * @param string|null $customTitle
     *
     * @return void
     */
    public function setCustomTitle(?string $customTitle): void;

    /**
     * Getter for AggregationType.
     *
     * @return string|null
     */
    public function getAggregationType(): ?string;

    /**
     * Setter for AggregationType.
     *
     * @param string|null $aggregationType
     *
     * @return void
     */
    public function setAggregationType(?string $aggregationType): void;

    /**
     * @return \Amasty\ReportBuilder\Api\Data\ReportColumnExtensionInterface
     */
    public function getExtensionAttributes(): ReportColumnExtensionInterface;

    /**
     * @param \Amasty\ReportBuilder\Api\Data\ReportColumnExtensionInterface $extensionAttributes
     *
     * @return \Amasty\Acart\Api\Data\RuleInterface
     */
    public function setExtensionAttributes(
        ReportColumnExtensionInterface $extensionAttributes
    ): self;
}
