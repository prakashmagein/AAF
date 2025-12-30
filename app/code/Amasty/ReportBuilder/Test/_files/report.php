<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Api\Data\ReportInterfaceFactory;
use Amasty\ReportBuilder\Model\ReportRepository;
use Amasty\ReportBuilder\Test\Registry;
use Magento\Framework\Api\DataObjectHelper;
use Magento\TestFramework\Helper\Bootstrap;

/** @var ReportInterfaceFactory $stockFactory */
$reportFactory = Bootstrap::getObjectManager()->get(ReportInterfaceFactory::class);
/** @var ReportRepository $reportRepository */
$reportRepository = Bootstrap::getObjectManager()->get(ReportRepository::class);
/** @var DataObjectHelper $dataObjectHelper */
$dataObjectHelper = Bootstrap::getObjectManager()->get(DataObjectHelper::class);

$reportData = [
    ReportInterface::NAME => 'test',
    ReportInterface::MAIN_ENTITY => 'order',
    ReportInterface::USE_PERIOD => false,
    ReportInterface::DISPLAY_CHART => false,
    ReportInterface::COLUMNS => [
        [
            ReportColumnInterface::COLUMN_ID => 'order.entity_id'
        ]
    ],
    ReportInterface::SCHEME => [
        'order_item' => [
            ReportInterface::SCHEME_SOURCE_ENTITY => 'order',
            ReportInterface::SCHEME_ENTITY => 'order_item'
        ]
    ]
];

/** @var ReportInterface $report */
$report = $reportFactory->create();
$report->addData($reportData);
$reportRepository->save($report);

Registry::$REPORT_ID = $report->getReportId();
