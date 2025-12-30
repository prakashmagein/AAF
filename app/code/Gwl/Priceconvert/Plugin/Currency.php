<?php
namespace Gwl\Priceconvert\Plugin;

use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\Locale\FormatInterface;
  
class Currency
{
    /**
     * @var FormatInterface
     */
    private $localeFormat;
    /**
     * @var CurrencyInterface
     */
    private $localeCurrency;

    /**
     * Currency constructor.
     *
     * @param FormatInterface $localeFormat
     * @param CurrencyInterface $localeCurrency
     */
    public function __construct(
        CurrencyInterface $localeCurrency,
        FormatInterface $localeFormat

    ) {

        $this->localeFormat = $localeFormat;
        $this->localeCurrency = $localeCurrency;
    }

    public function aroundFormatTxt(
        \Magento\Directory\Model\Currency $subject,
        callable $proceed,
        $price,
        $options = []
    ) {
        if (!is_numeric($price)) {
            $price = $this->localeFormat->getNumber($price);
        }

        $price = sprintf("%F", $price);

        return $this->localeCurrency->getCurrency($subject->getCode())->toCurrency($price, $options);
    }

}
