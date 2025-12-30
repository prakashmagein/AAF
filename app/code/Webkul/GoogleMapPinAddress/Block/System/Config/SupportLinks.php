<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_GoogleMapPinAddress
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\GoogleMapPinAddress\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Module\PackageInfoFactory;

class SupportLinks extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var string $_template
     */
    protected $_template = 'Webkul_GoogleMapPinAddress::system/config/moduleinfo.phtml';

    public const MODULE_NAME = 'Webkul_GoogleMapPinAddress';

    /**
     * @var PackageInfoFactory
     */
    protected $_packageInfoFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PackageInfoFactory $packageInfoFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        PackageInfoFactory $packageInfoFactory,
        array $data = []
    ) {
        $this->packageInfoFactory = $packageInfoFactory;
        parent::__construct($context, $data);
    }

    /**
     * Render element html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * Get Module Version
     */
    public function getModuleVersion()
    {
        $packageInfo = $this->packageInfoFactory->create();
        $version = $packageInfo->getVersion(self::MODULE_NAME);
        return $version;
    }

    /**
     * Get the button and scripts contents.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }
}
