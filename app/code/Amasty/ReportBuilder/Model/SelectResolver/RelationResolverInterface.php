<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver;

interface RelationResolverInterface
{
    const TYPE = 'type';
    const TABLE = 'table';
    const ALIAS = 'alias';
    const EXPRESSION = 'expression';
    const ADDITIONAL_EXPRESSIONS = 'additional_expressions';
    const PARENT = 'parent';

    /**
     * Get all prepared relations of a report for building sql query
     *
     * @return array
     */
    public function resolve(): array;

    public function getRelationByName(string $name): array;
}
