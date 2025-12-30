<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Indexer\Stock;

class IndexerPool
{
    /**
     * @var IndexerInterface[]
     */
    private $pool;

    public function __construct(array $pool = [])
    {
        $this->pool = $pool;
    }

    public function execute(): void
    {
        foreach ($this->pool as $indexer) {
            $indexer->execute();
        }
    }
}
