<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Controller\Adminhtml\Report;

use Amasty\ReportBuilder\Model\Chart\IsChartTypeAvailable;
use Amasty\ReportBuilder\Model\ReportResolver;
use Amasty\ReportBuilder\Model\View\ChartConfig;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class Chart extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Amasty_ReportBuilder::report_edit';
    private const PARAM_NAME = 'report_id';

    /**
     * @var ReportResolver
     */
    private $reportResolver;

    /**
     * @var ChartConfig
     */
    private $chartConfig;

    /**
     * @var IsChartTypeAvailable
     */
    private $isChartTypeAvailable;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        ReportResolver $reportResolver,
        ChartConfig $chartConfig,
        IsChartTypeAvailable $isChartTypeAvailable,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->reportResolver = $reportResolver;
        $this->chartConfig = $chartConfig;
        $this->isChartTypeAvailable = $isChartTypeAvailable;
        $this->logger = $logger;
    }

    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $reportId = (int) $this->getRequest()->getParam(self::PARAM_NAME);
        $chartData = [];

        try {
            $report = $this->reportResolver->resolve($reportId);
            $chart = $report->getExtensionAttributes()->getChart();
            if ($chart === null || !$this->isChartTypeAvailable->execute($chart->getChartType())) {
                return $resultJson->setData($chartData);
            }
        } catch (NoSuchEntityException $e) {
            return $resultJson->setData($chartData);
        }

        try {
            $chartData = $this->chartConfig->getChartConfig($report->getReportId());
        } catch (LocalizedException $e) {
            $this->logger->debug($e->getMessage());
        }

        return $resultJson->setData($chartData);
    }
}
