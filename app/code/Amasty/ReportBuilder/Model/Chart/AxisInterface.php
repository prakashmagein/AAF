<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Chart;

interface AxisInterface
{
    public const ALIAS_KEY = 'alias';
    public const TYPE_KEY = 'type';
    public const OPTIONS_KEY = 'options';

    public function setAlias(string $alias): void;

    public function getAlias(): string;

    public function setType(string $type): void;

    public function getType(): string;

    public function setOptions(array $options): void;

    public function getOptions(): array;
}
