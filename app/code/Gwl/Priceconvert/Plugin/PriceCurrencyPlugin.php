<?php
namespace Gwl\Priceconvert\Plugin;

use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class PriceCurrencyPlugin
{
    /**
     * @var FormatInterface
     */
    protected $localeFormat;

    /**
     * @var ResolverInterface
     */
    protected $localeResolver;

    /**
     * PriceCurrencyPlugin constructor.
     * @param FormatInterface $localeFormat
     * @param ResolverInterface $localeResolver
     */
    public function __construct(
        FormatInterface $localeFormat,
        ResolverInterface $localeResolver
    ) {
        $this->localeFormat = $localeFormat;
        $this->localeResolver = $localeResolver;
    }

    /**
     * Plugin method to modify the price display.
     *
     * @param PriceCurrencyInterface $subject
     * @param $result
     * @param $amount
     * @return mixed
     */
    public function afterFormat(
        PriceCurrencyInterface $subject,
        $result,
        $amount
    ) {

        //echo " ";
        // Check if the current locale is Arabic
        if ($this->localeResolver->getLocale() == 'ar_SA') {
            // Convert Arabic numbers to English numbers
            $result = str_replace(
                ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' , '٬', '٫'],
                ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ',', '.'],
                $result
            );
        }

        return $result;
    }
}