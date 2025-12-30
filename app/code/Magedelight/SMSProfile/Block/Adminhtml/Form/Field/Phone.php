<?php
namespace Magedelight\SMSProfile\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magedelight\SMSProfile\Block\Adminhtml\Form\Field\CountryColumn;

/**
 * Class Phone
 */
class Phone extends AbstractFieldArray
{

    /**
     * @var countryRenderer
     */
    private $countryRenderer;

    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('country', [
            'label' => __('Country'),
            'renderer' => $this->getCountryRenderer(),
            'class' => 'required-entry'
        ]);
        $this->addColumn('digit', ['label' => __('Digit'), 'class' => 'required-entry validate-number']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $country = $row->getCountry();
        if ($country !== null) {
            $options['option_' . $this->getCountryRenderer()->calcOptionHash($country)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return CountryColumn
     * @throws LocalizedException
     */
    private function getCountryRenderer()
    {
        if (!$this->countryRenderer) {
            $this->countryRenderer = $this->getLayout()->createBlock(
                CountryColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->countryRenderer;
    }
}
