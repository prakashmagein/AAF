<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Plugin\Reports\Model\Source\Reports;

use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Api\ReportRepositoryInterface;
use Amasty\ReportBuilder\Plugin\Reports\Model\Email\ReportContent\GetContent;
use Amasty\Reports\Model\Source\Reports;
use Magento\Framework\Phrase;

class GetLabel
{
    /**
     * @var ReportRepositoryInterface
     */
    private $reportRepository;

    public function __construct(
        ReportRepositoryInterface $reportRepository
    ) {
        $this->reportRepository = $reportRepository;
    }

    /**
     * @return Phrase|string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetLabelByValue(
        Reports $subject,
        callable $proceed,
        string $value
    ) {
        if (strpos($value, GetContent::AMREPORTBUILDER_REPORT_IDENTIFIER) === 0) {
            $reportId = (int)str_replace(GetContent::AMREPORTBUILDER_REPORT_IDENTIFIER, '', $value);
            /** @var ReportInterface $report **/
            $report = $this->reportRepository->getById($reportId);
            $result = $report->getName();
        } else {
            $result = $proceed($value);
        }

        return $result;
    }
}
