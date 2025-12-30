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

namespace Aheadworks\RewardPoints\Model\SpendRule\Condition\Rule;

use Aheadworks\RewardPoints\Model\SpendRule\Condition\AbstractCondition;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\CombineFactory as RuleCombineFactory;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Combine;
use Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory as ConditionProductCombineFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Aheadworks\RewardPoints\Block\Adminhtml\SpendRule\Edit\Conditions\Catalog\Form as CatalogForm;

/**
 * Class Catalog
 */
class Catalog extends AbstractCondition
{
    const CONDITION_PREFIX = 'catalog';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param RuleCombineFactory $condCombineFactory
     * @param ConditionProductCombineFactory $condProdCombineFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        RuleCombineFactory $condCombineFactory,
        ConditionProductCombineFactory $condProdCombineFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $localeDate, null, null, $data);
        $this->condCombineFactory = $condCombineFactory;
        $this->condProdCombineFactory = $condProdCombineFactory;
    }

    /**
     * Reset rule combine conditions
     *
     * @param Combine|null $conditions
     * @return $this
     */
    protected function _resetConditions($conditions = null): Catalog
    {
        parent::_resetConditions($conditions);
        $this->getConditions($conditions)
            ->setId('1')
            ->setPrefix(CatalogForm::CONDITION_FIELD_NAME);
        return $this;
    }
}
