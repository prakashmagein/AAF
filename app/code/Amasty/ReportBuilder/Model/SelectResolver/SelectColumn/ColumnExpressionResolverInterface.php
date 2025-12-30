<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn;

interface ColumnExpressionResolverInterface
{
    /**
     * Retrieve column sql expression by its alias
     *
     * @param string $columnAlias
     * @param bool $useInternal
     * @return string
     */
    public function resolve(string $columnAlias, bool $useInternal = false): string;
}
