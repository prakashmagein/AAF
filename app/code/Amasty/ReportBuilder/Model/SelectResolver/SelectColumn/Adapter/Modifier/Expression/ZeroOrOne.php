<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\Adapter\Modifier\Expression;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Api\Data\SelectColumnInterface;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\Adapter\Modifier\ModifierInterface;
use Magento\Framework\App\ResourceConnection;

class ZeroOrOne implements ModifierInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param SelectColumnInterface $selectColumn
     * @param ColumnInterface|null $schemeColumn
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function modify(
        SelectColumnInterface $selectColumn,
        ?ColumnInterface $schemeColumn
    ): void {
        $expression = $selectColumn->getAggregatedExpression();
        $expression = $this->wrapExpression($expression);
        $selectColumn->setAggregatedExpression($expression);
        
        if ($expression = $selectColumn->getExternalAggregatedExpression()) {
            $expression = $this->wrapExpression($expression);
            $selectColumn->setExternalAggregatedExpression($expression);
        }
    }

    private function wrapExpression(
        string $expression
    ): string {
        $connection = $this->resourceConnection->getConnection();

        return $connection->getCheckSql(
            sprintf('%s > 0', $expression),
            1,
            0
        )->__toString();
    }
}
