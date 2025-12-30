<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Plugin\Reports\Model\Source\Reports;

use Amasty\ReportBuilder\Model\ResourceModel\Report\CollectionFactory;
use Amasty\Reports\Model\Source\Reports;

class AddReportsFromBuilder
{
    /**
     * @var CollectionFactory
     */
    private $reportCollectionFactory;

    public function __construct(
        CollectionFactory $reportCollectionFactory
    ) {
        $this->reportCollectionFactory = $reportCollectionFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterToOptionArray(
        Reports $subject,
        array $result
    ): array {
        /** @var \Amasty\ReportBuilder\Model\ResourceModel\Report\Collection $reportCollection */
        $reportCollection = $this->reportCollectionFactory->create();
        $reports = [];

        foreach ($reportCollection as $item) {
            $reports[] = [
                'label' => $item->getName(),
                'value' => 'amreportbuilder' . $item->getReportId()
            ];
        }

        $result[] = [
            'label' => __('Amasty Custom Reports Builder'),
            'value' => $reports
        ];

        return $result;
    }
}
