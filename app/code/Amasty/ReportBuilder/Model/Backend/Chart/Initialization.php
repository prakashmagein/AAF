<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Backend\Chart;

use Amasty\ReportBuilder\Api\Data\ChartInterface;
use Amasty\ReportBuilder\Model\Report\Chart\Query\GetByIdInterface;
use Amasty\ReportBuilder\Model\Report\Chart\Query\GetNewInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Initialization
{
    /**
     * @var GetByIdInterface
     */
    private $getById;

    /**
     * @var GetNewInterface
     */
    private $getNew;

    public function __construct(GetByIdInterface $getById, GetNewInterface $getNew)
    {
        $this->getById = $getById;
        $this->getNew = $getNew;
    }

    /**
     * @throws InputException
     * @throws LocalizedException
     */
    public function execute(int $reportId, array $data): ChartInterface
    {
        if (!empty($data[ChartInterface::ID])) {
            try {
                $chart = $this->getById->execute((int)$data[ChartInterface::ID]);
            } catch (NoSuchEntityException $e) {
                throw new LocalizedException(__($e->getRawMessage(), $e->getParameters()));
            }
        } else {
            $chart = $this->getNew->execute();
        }

        $chart->setReportId($reportId);
        if (isset($data[ChartInterface::CHART_TYPE])) {
            $chart->setChartType($data[ChartInterface::CHART_TYPE]);
        } else {
            throw InputException::requiredField(__('Chart Type'));
        }

        return $chart;
    }
}
