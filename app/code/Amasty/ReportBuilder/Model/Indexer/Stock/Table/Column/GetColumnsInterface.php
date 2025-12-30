<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Indexer\Stock\Table\Column;

interface GetColumnsInterface
{
    /**
     * Getting columns with config.
     *
     * @return array
     */
    public function execute(): array;
}
