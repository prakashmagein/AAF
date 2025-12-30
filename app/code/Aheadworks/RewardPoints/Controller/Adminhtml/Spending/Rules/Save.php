<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    2.4.0
 * @copyright  Copyright (c) 2024 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RewardPoints\Controller\Adminhtml\Spending\Rules;

use Aheadworks\RewardPoints\Model\SpendRule\ActionManagement as SpendRuleActionManagement;
use Aheadworks\RewardPoints\Model\Data\ProcessorInterface;
use Aheadworks\RewardPoints\Ui\DataProvider\SpendRule\FormDataProvider as RuleFormDataProvider;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Save
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Aheadworks_RewardPoints::aw_reward_points_spending_rules';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var ProcessorInterface
     */
    private $postDataProcessor;

    /**
     * @var SpendRuleActionManagement
     */
    private $ruleActionManagement;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param ProcessorInterface $postDataProcessor
     * @param SpendRuleActionManagement $ruleManagement
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        ProcessorInterface $postDataProcessor,
        SpendRuleActionManagement $ruleActionManagement
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->postDataProcessor = $postDataProcessor;
        $this->ruleActionManagement = $ruleActionManagement;
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $postData = $this->getRequest()->getPostValue();
        if ($postData) {
            try {
                $ruleId = isset($postData['id']) ? $postData['id'] : false;
                $data = $this->postDataProcessor->process($postData);

                $rule = $ruleId
                    ? $this->ruleActionManagement->updateRule($ruleId, $data)
                    : $this->ruleActionManagement->createRule($data);

                $this->dataPersistor->clear(RuleFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('Rule was saved successfully'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $rule->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the rule'));
            }
            $this->dataPersistor->set(RuleFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $postData);
            if ($ruleId) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $ruleId, '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/new', ['_current' => true]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
