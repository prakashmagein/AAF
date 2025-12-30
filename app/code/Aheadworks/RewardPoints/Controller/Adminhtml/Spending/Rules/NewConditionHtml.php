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

use Aheadworks\RewardPoints\Controller\Adminhtml\RewardRules\Condition\Factory as RuleConditionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * Class NewConditionHtml
 */
class NewConditionHtml extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Aheadworks_RewardPoints::aw_reward_points_spending_rules';

    /**
     * Default value of prefix for conditions
     */
    const DEFAULT_CONDITIONS_PREFIX = 'conditions';

    /**
     * @var RuleConditionFactory
     */
    private $conditionFactory;

    /**
     * @param Context $context
     * @param RuleConditionFactory $conditionFactory
     */
    public function __construct(
        Context $context,
        RuleConditionFactory $conditionFactory
    ) {
        parent::__construct($context);
        $this->conditionFactory = $conditionFactory;
    }

    /**
     * New condition html action
     *
     * @return void
     */
    public function execute(): void
    {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $prefix = $request->getParam('prefix', self::DEFAULT_CONDITIONS_PREFIX);
        $typeArr = explode('|', str_replace('-', '/', $request->getParam('type')));
        $type = $typeArr[0];
        $attribute = !empty($typeArr[1]) ? $typeArr[1] : null;
        $formName = $request->getParam('form_namespace');
        $jsFormObject = $request->getParam('form');

        try {
            $conditionModel = $this->conditionFactory->create(
                $type,
                $id,
                $prefix,
                $attribute,
                $jsFormObject,
                $formName
            );

            $html = $conditionModel->asHtmlRecursive();
        } catch (\Exception $e) {
            $html = '';
        }

        $this->getResponse()->setBody($html);
    }
}
