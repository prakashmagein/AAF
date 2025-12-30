<?php
namespace Gwl\Datalayer\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class PaymentFailure implements ObserverInterface
{
    protected $checkoutSession;
    protected $layout;
    protected $productRepository;
    protected $priceHelper;

    public function __construct(
        CheckoutSession $checkoutSession,
        LayoutInterface $layout,
        ProductRepositoryInterface $productRepository,
        PriceHelper $priceHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->layout = $layout;
        $this->productRepository = $productRepository;
        $this->priceHelper = $priceHelper;
    }

    public function execute(Observer $observer)
    {
        $quote = $this->checkoutSession->getQuote();
        if (!$quote) {
            return;
        }

        $paymentMethod = $quote->getPayment()->getMethod();
        $failureReason = __('Transaction Declined'); // Set failure reason dynamically if possible
        $currency = $quote->getQuoteCurrencyCode();
        $totalValue = $quote->getGrandTotal();
        $couponCode = $quote->getCouponCode() ?: 'N/A'; // Get applied coupon

        $items = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $product = $this->productRepository->getById($item->getProductId());
            $items[] = [
                'item_name' => $product->getName(),
                'item_id' => $product->getSku(),
                'original_price' => $this->priceHelper->currency($product->getPrice(), false, false),
                'price' => $this->priceHelper->currency($item->getPrice(), false, false),
                'discount' => $this->priceHelper->currency($product->getPrice() - $item->getPrice(), false, false),
                'currency' => $currency,
                'item_brand' => $product->getAttributeText('brand') ?: 'Unknown',
                'item_category' => $product->getAttributeText('category') ?: 'Uncategorized',
                'quantity' => (int) $item->getQty(),
                'item_url' => $product->getProductUrl(),
            ];
        }

        // Generate the dataLayer script
        $dataLayerScript = '<script>
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                "event": "payment_failure",
                "ecommerce": {
                    "currency": "' . $currency . '",
                    "value": ' . $totalValue . ',
                    "coupon": "' . $couponCode . '",
                    "payment_type": "' . $paymentMethod . '",
                    "failure_reason": "' . $failureReason . '",
                    "items": ' . json_encode($items) . '
                }
            });
        </script>';

        // Inject script into footer
        $this->layout->getBlock('before_body_end')->append($dataLayerScript);
    }
}
