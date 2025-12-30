<?php

namespace Gwl\Priceconvert\Plugin\Pdf;

use Magento\Sales\Model\Order\Pdf\Invoice as OriginalInvoice;

class InvoicePlugin
{
    public function aroundGetPdf(OriginalInvoice $subject, callable $proceed, $invoices = [])
    {
        $pdf = $proceed($invoices);

        foreach ($invoices as $invoice) {
            foreach ($invoice->getAllItems() as $item) {
                $basePrice = $item->getBasePrice();
                $specialPrice = $item->getPrice();
                $extraDiscount = $basePrice - $specialPrice;

                // Add extra discount as a custom attribute for rendering
                $item->setData('extra_discount', $extraDiscount);
            }
        }

        return $pdf;
    }
}
