<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Controller\Adminhtml\Report;

use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Api\ReportRepositoryInterface;
use Amasty\ReportBuilder\Model\Backend\Report\GetInvalidColumns;
use Amasty\ReportBuilder\Model\ReportRegistry;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Amasty_ReportBuilder::report_edit';

    /**
     * @var ReportRepositoryInterface
     */
    private $reportRepository;

    /**
     * @var ReportRegistry
     */
    private $registry;

    /**
     * @var GetInvalidColumns
     */
    private $getInvalidColumns;

    public function __construct(
        Context $context,
        ReportRepositoryInterface $reportRepository,
        ReportRegistry $registry,
        GetInvalidColumns $getInvalidColumns
    ) {
        parent::__construct($context);
        $this->reportRepository = $reportRepository;
        $this->registry = $registry;
        $this->getInvalidColumns = $getInvalidColumns;
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Amasty_ReportBuilder::Amasty_ReportBuilder');
        $resultPage->getConfig()->getTitle()->prepend(__('Amasty Custom Reports Builder'));

        return $resultPage;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $reportId = (int)$this->getRequest()->getParam(ReportInterface::REPORT_ID);

        try {
            if ($reportId) {
                /**
                 * @var ReportInterface $report
                 */
                $report = $this->reportRepository->getById($reportId);
            } else {
                $report = $this->reportRepository->getNew();
            }

            $this->registry->setReport($report);
            $this->processInvalidColumns();
        } catch (NoSuchEntityException $exception) {
            $this->messageManager->addErrorMessage(__('This Report no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $text = $report->getReportId() ? __('Edit Report "%1"', $report->getName()) : __('New Report');
        $this->initPage($resultPage)->getConfig()->getTitle()->prepend($text);

        return $resultPage;
    }

    /**
     * Determine is report have an invalid columns and show error message about it.
     */
    private function processInvalidColumns(): void
    {
        if ($invalidColumns = $this->getInvalidColumns->execute()) {
            $this->messageManager->addComplexErrorMessage(
                'addInvalidColumnsMessage',
                [
                    'invalid_columns' => $invalidColumns
                ]
            );
        }
    }
}
