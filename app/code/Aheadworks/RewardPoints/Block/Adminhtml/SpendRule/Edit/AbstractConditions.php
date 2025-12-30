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

namespace Aheadworks\RewardPoints\Block\Adminhtml\SpendRule\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Aheadworks\RewardPoints\Block\Adminhtml\SpendRule\Edit\Conditions\AbstractForm;
use Magento\Framework\Phrase;

/**
 * Class AbstractConditions
 */
class AbstractConditions extends Generic implements TabInterface
{
    /**
     * @var AbstractForm
     */
    protected $form;

    /**
     * Tab class getter
     *
     * @return string|null
     */
    public function getTabClass(): ?string
    {
        return null;
    }

    /**
     * Return URL link to Tab content
     *
     * @return string|null
     */
    public function getTabUrl(): ?string
    {
        return null;
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded(): bool
    {
        return false;
    }

    /**
     * Return Tab label
     *
     * @return Phrase|string
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * Return Tab title
     *
     * @return Phrase|string
     */
    public function getTabTitle()
    {
        return __('Conditions');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab(): bool
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden(): bool
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = $this->createForm();
        $this->form->prepareForm($form);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
