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
namespace Aheadworks\RewardPoints\Block\Adminhtml\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;
use Aheadworks\RewardPoints\Model\ThirdPartyModule\Manager as ThirdPartyModuleManager;

/**
 * Class Subscription
 *
 * @package Aheadworks\RewardPoints\Block\Adminhtml\Form\Field
 */
class Subscription extends Field
{
    /**
     * @var ThirdPartyModuleManager
     */
    private $thirdPartyModuleManager;

    /**
     * @param Context $context
     * @param ThirdPartyModuleManager $thirdPartyModuleManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        ThirdPartyModuleManager $thirdPartyModuleManager,
        array $data = []
    ) {
        $this->thirdPartyModuleManager = $thirdPartyModuleManager;
        parent::__construct($context, $data);
    }

    /**
     * Display field if SARP2 is enabled
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        return $this->thirdPartyModuleManager->isSarp2ModuleEnabled() ? parent::render($element) :  '';
    }
}
