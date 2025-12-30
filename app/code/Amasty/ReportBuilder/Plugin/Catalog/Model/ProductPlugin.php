<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Plugin\Catalog\Model;

use Amasty\ReportBuilder\Model\Indexer\Eav\Processor;
use Magento\Catalog\Model\Product;

class ProductPlugin
{
    /**
     * @var Processor
     */
    private $processor;

    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * @param Product $subject
     */
    public function beforeEavReindexCallback(Product $subject): void
    {
        if ($subject->isObjectNew() || $subject->isDataChanged()) {
            $this->processor->reindexRow($subject->getEntityId());
        }
    }
}
