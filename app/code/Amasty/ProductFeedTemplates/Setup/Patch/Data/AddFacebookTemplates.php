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

class AddFacebookTemplates implements DataPatchInterface
{
    /**
     * @var string[]
     */
    private $templateFixtures = [
        'facebook_xml',
        'facebook_csv'
    ];

    /**
     * @var ImportFeedTemplates
     */
    private $importFeedTemplates;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ImportFeedTemplates $importFeedTemplates,
        LoggerInterface $logger
    ) {
        $this->importFeedTemplates = $importFeedTemplates;
        $this->logger = $logger;
    }

    public function apply(): self
    {
        try {
            $this->importFeedTemplates->import($this->templateFixtures, 'Amasty_ProductFeedTemplates::fixtures/');
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $this;
    }

    public static function getDependencies(): array
    {
        return [FeedTemplates::class];
    }

    public function getAliases(): array
    {
        return [];
    }
}
