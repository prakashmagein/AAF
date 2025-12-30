<?php
declare(strict_types=1);

namespace Magedelight\SMSProfile\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;

class CountryColumn extends Select
{
    private $countryConfig;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Directory\Model\Config\Source\Country $countryConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->countryConfig = $countryConfig;
    }
    

    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    private function getSourceOptions(): array
    {
        $options = $this->countryConfig->toOptionArray();
        $options[0]=['label' => 'Default', 'value' => 'default'];
        return $options;
    }
}
