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

namespace Aheadworks\RewardPoints\Block\Adminhtml\SpendRule\Edit\Conditions\Cart;

use Aheadworks\RewardPoints\Model\SpendRule\Condition\AbstractCondition as ConditionRuleFactory;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Rule\CartFactory;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Framework\Phrase;
use Magento\Rule\Block\Conditions as ConditionsBlock;
use Magento\Framework\UrlInterface;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Rule\Cart;
use Aheadworks\RewardPoints\Block\Adminhtml\SpendRule\Edit\Conditions\AbstractForm;
use Aheadworks\RewardPoints\Block\Adminhtml\SpendRule\Edit\Conditions\DataProvider;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\View\LayoutInterface;

/**
 * Class Form
 */
class Form extends AbstractForm
{
    /**#@+
     * Constants defined for form with conditions for cart form
     */
    const FORM_FIELDSET_NAME = 'cart_conditions_fieldset';
    const CONDITION_FIELD_NAME = Cart::CONDITION_PREFIX;
    /**#@-*/

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @param ConditionsBlock $conditions
     * @param CartFactory $conditionRule
     * @param DataProvider $formDataProvider
     * @param UrlInterface $urlBuilder
     * @param LayoutInterface $layout
     */
    public function __construct(
        ConditionsBlock $conditions,
        CartFactory $conditionRule,
        DataProvider $formDataProvider,
        UrlInterface $urlBuilder,
        LayoutInterface $layout
    ) {
        $this->conditions = $conditions;
        $this->conditionRule = $conditionRule;
        $this->formDataProvider = $formDataProvider;
        $this->urlBuilder = $urlBuilder;
        $this->layout = $layout;
    }

    /**
     * Retrieve renderer for form fieldset
     *
     * @return RendererInterface
     */
    protected function getFieldsetRenderer(): RendererInterface
    {
        return $this->layout->createBlock(Fieldset::class)
                ->setTemplate($this->fieldsetTemplate)
                ->setNameInLayout('aw_rp_rule_cart_renderer')
                ->setNewChildUrl(
                    $this->urlBuilder->getUrl(
                        static::NEW_CHILD_URL_ROUTE,
                        [
                            'form'   => static::FORM_ID_PREFIX . static::FORM_FIELDSET_NAME,
                            'prefix' => Cart::CONDITION_PREFIX,
                            'rule'   => base64_encode(Cart::class),
                            'form_namespace' => static::FORM_NAME
                        ]
                    )
                );
    }

    /**
     * Get legend for fieldset
     *
     * @return Phrase
     */
    protected function getLegend(): Phrase
    {
        return __('Apply the rule only if the following conditions are met (leave blank for all products).');
    }
}
