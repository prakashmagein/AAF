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

use Aheadworks\RewardPoints\Api\SpendRuleRepositoryInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\SpendRule\CollectionFactory as SpendRuleCollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Backend\Model\View\Result\Redirect;

/**
 * Class MassDelete
 */
class MassDelete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Aheadworks_RewardPoints::aw_reward_points_spending_rules';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var SpendRuleCollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * @var SpendRuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param SpendRuleCollectionFactory $ruleCollectionFactory
     * @param SpendRuleRepositoryInterface $ruleRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        SpendRuleCollectionFactory $ruleCollectionFactory,
        SpendRuleRepositoryInterface $ruleRepository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->ruleCollectionFactory->create());
            $count = 0;
            foreach ($collection->getAllIds() as $ruleId) {
                $this->ruleRepository->deleteById($ruleId);
                $count++;
            }
            if ($count) {
                $this->messageManager->addSuccessMessage(__('A total of %1 rule(s) were deleted.', $count));
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
