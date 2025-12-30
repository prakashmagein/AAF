<?php
namespace Gwl\Priceconvert\Plugin;

use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Currency\Data\Currency as MagentoCurrency;
  
class CurrencyData
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
    public function beforeSetLocale(MagentoCurrency $subject, $locale = null)
    {
        // Custom logic to apply after `setLocale` execution
        if ($locale === 'ar_SA') {
            // Apply a default locale or custom behavior
            $locale = 'en_US';
        }
        $locale = 'en_US';
        // Example: Apply custom locale data or log changes
        //$subject->setData('locale', $locale);
//
return [$locale];
    }

}
