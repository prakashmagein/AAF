<?php
namespace Gwl\Datalayerevents\Observer;

use Bss\GA4\Model\DataItem;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class AddToWishlist implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var DataItem
     */
    

    protected $logger;

    /**
     * @param Session $customerSession
     * @param RequestInterface $request
     */
    public function __construct(
        Session                                 $customerSession,
        \Magento\Framework\App\RequestInterface $request,
        LoggerInterface $logger,
    ) {
        $this->customerSession = $customerSession;
        $this->request = $request;
        $this->logger = $logger;
        
    }

    /**
     * Set data to event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        $product = $observer->getProduct();
        $productId = $product->getId();
        $productName = $product->getName();
        $productSku = $product->getSku();
        $productPrice = $product->getFinalPrice();
        $originalPrice = $product->getPrice();
        $discount = $originalPrice - $productPrice;
        $productImage = $product->getImage();
        $category = "Default";

        $wishlistData = [
            "event" => "add_to_wishlist",
            "ecommerce" => [
                "currency" => "SAR",
                "value" => $productPrice,
                "item_img" => $productImage,
                "items" => [
                    [
                        "item_name" => $productName,
                        "item_id" => $productSku,
                        "original_price" => $originalPrice,
                        "price" => $productPrice,
                        "discount" => $discount,
                        "currency" => "SAR",
                        "item_brand" => "",
                        "item_category" => $category,
                        "item_list_name" => "Wishlist",
                        "item_list_id" => "WISHLIST",
                        "quantity" => $this->request->getParam('qty') ?? 1
                    ]
                ]
            ]
        ];

        $this->logger->info('Wishlist Added to Cart array: ' . json_encode($wishlistData));


        if ($this->customerSession->getProductDataWishlist()) {
            $this->customerSession->unsProductDataWishlist();
        }
        $this->customerSession->setProductDataWishlist($wishlistData);

        $this->customerSession->clear();
    }
}
