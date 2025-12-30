<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Feed Templates
 */

namespace Amasty\ProductFeedTemplates\Setup\Patch\Data;

use Amasty\Feed\Setup\Operation\ImportFeedTemplates;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Psr\Log\LoggerInterface;

class FeedTemplates implements DataPatchInterface
{
    /**
     * @var string[]
     */
    private $templateFixtures = [
        'ebay_product_xml',
        'ebay_product_csv',
        'ebay_inventory_xml',
        'ebay_inventory_csv',
        'instagram_catalog_xml',
        'instagram_catalog_csv',
        'tiktok_xml',
        'tiktok_csv',
        'pinterest_xml',
        'pinterest_csv'
    ];

    /**
     * @var ImportFeedTemplates
     */
    private $importFeedTemplates;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        ImportFeedTemplates $importFeedTemplates,
        LoggerInterface $logger
    ) {
        $this->importFeedTemplates = $importFeedTemplates;
        $this->logger = $logger;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): FeedTemplates
    {
        try {
            $this->importFeedTemplates->import($this->templateFixtures, 'Amasty_ProductFeedTemplates::fixtures/');
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $this;
    }
}
