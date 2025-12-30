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

namespace Aheadworks\RewardPoints\Model\SpendRule\Condition;

use Magento\Rule\Model\Condition\Combine as ConditionCombine;
use Magento\Rule\Model\Action\Collection as ActionCollection;
use Magento\SalesRule\Model\Rule\Condition\CombineFactory as ConditionCombineFactory;
use Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory as ConditionProductCombineFactory;
use Magento\Rule\Model\AbstractModel;
use Magento\SalesRule\Model\Rule\Condition\Product\Combine;

/**
 * Class AbstractCondition
 */
class AbstractCondition extends AbstractModel
{
    /**
     * @var ConditionCombineFactory
     */
    protected $condCombineFactory;

    /**
     * @var ConditionProductCombineFactory
     */
    protected $condProdCombineFactory;

    /**
     * Retrieve rule combine conditions instance
     *
     * @return ConditionCombine
     */
    public function getConditionsInstance()
    {
        return $this->condCombineFactory->create();
    }

    /**
     * Retrieve rule actions collection instance
     *
     * @return ActionCollection|Combine
     */
    public function getActionsInstance()
    {
        return $this->condProdCombineFactory->create();
    }
}
