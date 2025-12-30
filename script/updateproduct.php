<?php
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\ObjectManager;

require __DIR__ . '../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

// Get the product collection factory
$productCollectionFactory = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');

// Get the product repository
$productRepository = $objectManager->get('\Magento\Catalog\Api\ProductRepositoryInterface');

// Set the special price from and to dates
$specialPriceFromDate = '2024-09-01';  // Start date in YYYY-MM-DD format
$specialPriceToDate = '2024-10-30';    // End date in YYYY-MM-DD format

// Load all products
$collection = $productCollectionFactory->create();
$collection->addAttributeToSelect('*');  // Select all attributes (if needed)

// Loop through each product and update special price and dates
foreach ($collection as $product) {
    try {
        $regularPrice = $product->getPrice(); 
        // Remove the special price
        $product->setSpecialPrice($regularPrice);

        // Set the special price "from" date
        $product->setSpecialFromDate($specialPriceFromDate);

        // Set the special price "to" date
        $product->setSpecialToDate($specialPriceToDate);

        // Save the updated product
        $productRepository->save($product);

        echo "Updated product SKU: " . $product->getSku() . "\n";
    } catch (\Exception $e) {
        echo "Error updating product SKU: " . $product->getSku() . " - " . $e->getMessage() . "\n";
    }
}

echo "All products have been updated.\n";
