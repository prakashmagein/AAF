<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface ReportInterface extends ExtensibleDataInterface
{
    public const MAIN_TABLE = 'amasty_report_builder_report';
    public const REPORT_ID = 'report_id';
    public const NAME = 'name';
    public const MAIN_ENTITY = 'main_entity';
    public const STORE_IDS = 'store_ids';
    public const USE_PERIOD = 'is_use_period';
    public const DISPLAY_CHART = 'display_chart';
    public const CHART_AXIS_X = 'chart_axis_x';
    public const CHART_AXIS_Y = 'chart_axis_y';

    /**
     * @deprecated column data moved to separated class
     */
    public const COLUMN_ID = \Amasty\ReportBuilder\Api\Data\ReportColumnInterface::COLUMN_ID;
    public const SCHEME_ENTITY = 'entity';
    public const SCHEME_SOURCE_ENTITY = 'source_entity';
    public const COLUMNS = 'columns';
    /**
     * @deprecated column data moved to separated class
     */
    public const REPORT_COLUMN_ID = \Amasty\ReportBuilder\Api\Data\ReportColumnInterface::COLUMN_ID;
    /**
     * @deprecated column data moved to separated class
     */
    public const REPORT_COLUMN_FILTER = \Amasty\ReportBuilder\Api\Data\ReportColumnInterface::FILTER;
    public const SCHEME = 'scheme';

    public const PERSIST_NAME = 'amasty_reportbuilder_report';

    /**
     * @return int
     */
    public function getReportId(): int;

    /**
     * @param int|null $reportId
     *
     * @return void
     */
    public function setReportId(?int $reportId): void;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name): void;

    /**
     * @return string|null
     */
    public function getMainEntity(): ?string;

    /**
     * @param string $entity
     *
     * @return void
     */
    public function setMainEntity(string $entity): void;

    /**
     * @return bool
     */
    public function getUsePeriod(): bool;

    /**
     * @param bool $usePeriod
     *
     * @return void
     */
    public function setUsePeriod(bool $usePeriod = false): void;

    /**
     * @return bool
     */
    public function getDisplayChart(): bool;

    /**
     * @param bool $displayChart
     *
     * @return void
     */
    public function setDisplayChart(bool $displayChart = false): void;

    /**
     * @param array $columns
     *
     * @return void
     */
    public function setColumns(array $columns): void;

    /**
     * @return array
     */
    public function getAllColumns(): array;

    /**
     * @param array $scheme
     *
     * @return void
     */
    public function setRelationScheme(array $scheme): void;

    /**
     * @return array|null
     */
    public function getRelationScheme(): ?array;

    /**
     * @return array
     */
    public function getAllEntities(): array;

    /**
     * @return \Amasty\ReportBuilder\Api\Data\ReportExtensionInterface
     */
    public function getExtensionAttributes(): \Amasty\ReportBuilder\Api\Data\ReportExtensionInterface;

    /**
     * @param \Amasty\ReportBuilder\Api\Data\ReportExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        \Amasty\ReportBuilder\Api\Data\ReportExtensionInterface $extensionAttributes
    ): void;
}
