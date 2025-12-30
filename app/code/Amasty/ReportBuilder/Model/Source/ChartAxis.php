<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Source;

use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Amasty\ReportBuilder\Model\Report\ColumnsResolver;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

class ChartAxis implements OptionSourceInterface
{
    /**
     * @var Provider
     */
    private $provider;

    /**
     * @var ColumnsResolver
     */
    private $columnsResolver;

    public function __construct(
        Provider $provider,
        ColumnsResolver $columnsResolver
    ) {
        $this->provider = $provider;
        $this->columnsResolver = $columnsResolver;
    }

    public function toOptionArray(): array
    {
        $options =  [['value' => '', 'label' => __('Please select column')]];
        $scheme = $this->provider->getEntityScheme();

        foreach ($this->columnsResolver->getReportColumns() as $reportColumn) {
            try {
                if (!$reportColumn->getCustomTitle() && $scheme->getColumnById($reportColumn->getColumnId()) === null) {
                    continue;
                }
            } catch (LocalizedException $e) {
                continue;
            }

            $options[] = [
                'value' => $reportColumn->getColumnId(),
                'label' => $reportColumn->getCustomTitle()
                    ?: $scheme->getColumnById($reportColumn->getColumnId())->getTitle()
            ];
        }

        return $options;
    }
}
