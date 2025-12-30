<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Chart;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Column\OptionsResolver;
use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Magento\Framework\ObjectManagerInterface;

class AxisFactory
{
    public const AXIS_TYPE_TEXT = 'text';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Provider
     */
    private $provider;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Provider $provider,
        OptionsResolver $optionsResolver
    ) {
        $this->objectManager = $objectManager;
        $this->provider = $provider;
        $this->optionsResolver = $optionsResolver;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function create(int $reportId, string $columnId): ?AxisInterface
    {
        $column = $this->provider->getEntityScheme()->getColumnById($columnId);
        if ($column === null) {
            return null;
        }

        return $this->objectManager->create(AxisInterface::class, [
            AxisInterface::ALIAS_KEY => $column->getAlias(),
            AxisInterface::TYPE_KEY => $this->getAxisType($column),
            AxisInterface::OPTIONS_KEY => $this->optionsResolver->resolve($column)
        ]);
    }

    private function getAxisType(ColumnInterface $columnScheme): string
    {
        if (in_array($columnScheme->getFrontendModel(), ['select', 'multiselect'])) {
            return self::AXIS_TYPE_TEXT;
        }

        return $columnScheme->getType();
    }
}
