<?php
namespace Galaxyweblinks\Liltshipping\Plugin;

class ShippingPlugin {
    public function aroundCollectRates(
        \Magento\Shipping\Model\Shipping $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Address\RateRequest $request
    ) {
        $result = $proceed($request);

        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                $product = $item->getProduct();

                $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/price.log');
                $logger = new \Zend_Log();
                $logger->addWriter($writer);
                $logger->info('sanitary2 '.json_encode($product));

                // Get Aramex shipping method for the product
               // $aramexShippingMethod = $this->getAramexShippingMethod($product);

                // Do something with the Aramex shipping method
            }
        }

        return $result;
    }

    private function getAramexShippingMethod($product) {
        // Implement this method to get the Aramex shipping method for a product
    }
}