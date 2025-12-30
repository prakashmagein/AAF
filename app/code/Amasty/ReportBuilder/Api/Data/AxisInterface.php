<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Api\Data;

interface AxisInterface
{
    public const ID = 'id';
    public const CHART_ID = 'chart_id';
    public const TYPE = 'type';
    public const VALUE = 'value';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return null|int
     */
    public function getChartId(): ?int;

    /**
     * @param null|int $chartId
     * @return void
     */
    public function setChartId(?int $chartId): void;

    /**
     * Getter for type.
     * Type is axis name.
     *
     * @return null|string
     */
    public function getType(): ?string;

    /**
     * @param string $type
     * @return void
     */
    public function setType(string $type): void;

    /**
     * Getter for value.
     * Value is report column name.
     *
     * @return null|string
     */
    public function getValue(): ?string;

    /**
     * @param string $value
     * @return void
     */
    public function setValue(string $value): void;
}
