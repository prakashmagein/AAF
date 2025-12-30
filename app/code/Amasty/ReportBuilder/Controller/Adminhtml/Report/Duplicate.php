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
use Amasty\ReportBuilder\Model\Report\Command\DuplicateInterface;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class Duplicate extends Action
{
    /**
     * @var ReportRepositoryInterface
     */
    private $reportRepository;

    /**
     * @var DuplicateInterface
     */
    private $duplicateCommand;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        DuplicateInterface $duplicateCommand,
        Context $context
    ) {
        parent::__construct($context);
        $this->reportRepository = $reportRepository;
        $this->duplicateCommand = $duplicateCommand;
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $reportId = (int) $this->getRequest()->getParam(ReportInterface::REPORT_ID);

        try {
            $reportToDuplicate = $this->reportRepository->getById($reportId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This Report no longer exists.'));
            return $redirect->setRefererUrl();
        }

        try {
            $newReport = $this->duplicateCommand->execute($reportToDuplicate);
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $redirect->setRefererUrl();
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('An error occurred while saving report. See exception log for details')
            );
            return $redirect->setRefererUrl();
        }

        $this->messageManager->addSuccessMessage(__('%1 report was successfully duplicated.', $newReport->getName()));

        $redirect->setPath('*/*/edit', [ReportInterface::REPORT_ID => $newReport->getReportId()]);

        return $redirect;
    }
}
