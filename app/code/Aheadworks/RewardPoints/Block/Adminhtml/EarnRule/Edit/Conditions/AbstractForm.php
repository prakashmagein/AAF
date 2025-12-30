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

namespace Aheadworks\RewardPoints\Block\Adminhtml\EarnRule\Edit\Conditions;

use Aheadworks\RewardPoints\Model\EarnRule\Condition\AbstractCart as CartRuleFactory;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Rule\Cart;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Rule\Catalog;
use Magento\Framework\Data\Form as DataForm;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Rule\Block\Conditions as ConditionsBlock;
use Magento\Rule\Model\AbstractModel;
use Magento\Rule\Model\Condition\AbstractCondition;

/**
 * Class AbstractForm
 *
 */
abstract class AbstractForm
{
    /**#@+
     * Constants defined for form with conditions for abstract form
     */
    const FORM_NAME = 'aw_reward_points_earning_rules_form';
    const FORM_ID_PREFIX = 'rule_';
    const NEW_CHILD_URL_ROUTE = '*/*/newConditionHtml';

    const FORM_FIELDSET_NAME = '';
    const CONDITION_FIELD_NAME = '';
    /**#@-*/

    /**
     * @var ConditionsBlock
     */
    protected $conditions;

    /**
     * @var array
     */
    protected $formData;

    /**
     * @var CartRuleFactory
     */
    protected $cartRule;

    /**
     * @var DataProvider
     */
    protected $formDataProvider;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var string
     */
    protected $fieldsetTemplate = 'Magento_CatalogRule::promo/fieldset.phtml';

    /**
     * Prepare form
     *
     * @param DataForm $form
     */
    public function prepareForm(DataForm $form)
    {
        $fieldset = $this->addFieldsetToForm($form);
        $this->prepareFieldset($fieldset);
    }

    /**
     * Add fieldset to specified form
     *
     * @param DataForm $form
     * @return Fieldset
     */
    protected function addFieldsetToForm(DataForm $form)
    {
        return $form->addFieldset(static::FORM_FIELDSET_NAME, []);
    }

    /**
     * Prepare field set for form
     *
     * @param Fieldset $fieldset
     */
    protected function prepareFieldset(Fieldset $fieldset): void
    {
        $conditionData = $this->formDataProvider->getConditions(static::CONDITION_FIELD_NAME);
        $conditionRule = $this->getConditionRule($conditionData);
        $fieldset->setRenderer($this->getFieldsetRenderer());
        $conditionRule->setJsFormObject(static::FORM_ID_PREFIX . static::FORM_FIELDSET_NAME);
        $this->addFieldsToFieldset($fieldset, $conditionRule);
        $this->setConditionFormName(
            $conditionRule->getConditions(),
            static::FORM_NAME,
            static::FORM_ID_PREFIX . static::FORM_FIELDSET_NAME
        );
    }

    /**
     * Retrieve condition rule object from condition array
     *
     * @param mixed $conditionData
     * @return AbstractModel
     */
    protected function getConditionRule($conditionData): AbstractModel
    {
        $cartRule = $this->cartRule->create();
        if (isset($conditionData) && (is_array($conditionData))) {
            $cartRule->setConditions([])
                ->getConditions()
                ->loadArray($conditionData);
        }
        return $cartRule;
    }

    /**
     * Add necessary fields to form fieldset
     *
     * @param Fieldset $fieldset
     * @param Catalog|Cart $conditionData
     */
    protected function addFieldsToFieldset(Fieldset $fieldset, $conditionData)
    {
        $fieldset
            ->setLegend($this->getLegend())
            ->addField(
                static::CONDITION_FIELD_NAME,
                'text',
                [
                    'name' => static::CONDITION_FIELD_NAME,
                    'label' => __('Conditions'),
                    'title' => __('Conditions'),
                    'data-form-part' => static::FORM_NAME
                ]
            )
            ->setRule($conditionData)
            ->setRenderer($this->conditions);
    }

    /**
     * Handles addition of form name to condition and its conditions
     *
     * @param AbstractCondition $conditions
     * @param string $formName
     * @param string $jsFormObject
     * @return void
     */
    protected function setConditionFormName(AbstractCondition $conditions, string $formName, string $jsFormObject): void
    {
        $conditions->setFormName($formName);
        $conditions->setJsFormObject($jsFormObject);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName, $jsFormObject);
            }
        }
    }

    /**
     * Get legend for fieldset
     *
     * @return Phrase
     */
    protected function getLegend(): Phrase
    {
        return __('For all products matching the conditions below');
    }

    /**
     * Retrieve renderer for form fieldset
     *
     * @return RendererInterface
     */
    abstract protected function getFieldsetRenderer(): RendererInterface;
}
