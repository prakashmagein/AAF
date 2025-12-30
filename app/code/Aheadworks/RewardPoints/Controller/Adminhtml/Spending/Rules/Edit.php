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
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\Model\View\Result\Page;

/**
 * Class Edit
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Aheadworks_RewardPoints::aw_reward_points_spending_rules';

    /**
     * Key used for registry to store rule
     */
    const CURRENT_RULE_KEY = 'aw_reward_points_current_rule';

    /**
     * @var SpendRuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @param Context $context
     * @param SpendRuleRepositoryInterface $ruleRepository
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        SpendRuleRepositoryInterface $ruleRepository,
        PageFactory $resultPageFactory,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->ruleRepository = $ruleRepository;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $ruleId = (int) $this->getRequest()->getParam('id');
        if ($ruleId) {
            try {
                $rule = $this->ruleRepository->get($ruleId);
                $this->registry->register(self::CURRENT_RULE_KEY, $rule);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('The rule #%1 not found.', $ruleId)
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
        }
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_RewardPoints::aw_reward_points_spending_rules')
            ->getConfig()->getTitle()->prepend(
                $ruleId
                    ? __('Edit "%1" Rule', $rule->getName())
                    : __('New Rule')
            );

        return $resultPage;
    }
}
