<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Cron\Stock;

use Amasty\ReportBuilder\Model\Indexer\Stock\IndexerPool;

class Indexer
{
    /**
     * @var IndexerPool
     */
    private $indexerPool;

    public function __construct(IndexerPool $indexerPool)
    {
        $this->indexerPool = $indexerPool;
    }

    public function execute(): void
    {
        $this->indexerPool->execute();
    }
}
