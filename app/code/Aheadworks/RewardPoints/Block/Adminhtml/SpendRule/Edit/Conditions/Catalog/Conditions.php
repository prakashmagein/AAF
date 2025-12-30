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

namespace Aheadworks\RewardPoints\Block\Adminhtml\SpendRule\Edit\Conditions\Catalog;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Data\Form as NativeForm;
use Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory;
use Aheadworks\RewardPoints\Block\Adminhtml\SpendRule\Edit\AbstractConditions;
use Aheadworks\RewardPoints\Block\Adminhtml\SpendRule\Edit\Conditions\AbstractForm;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Conditions
 */
class Conditions extends AbstractConditions
{
    /**
     * @var string
     */
    protected $_nameInLayout = 'aw_rp_rule_catalog_conditions';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Form $form
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Form $form,
        array $data = []
    ) {
        $this->form = $form;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Create form for controls
     *
     * @return NativeForm
     * @throws LocalizedException
     */
    protected function createForm(): NativeForm
    {
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix(AbstractForm::FORM_ID_PREFIX);
        return $form;
    }
}
