<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver;

use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Amasty\ReportBuilder\Model\ReportResolver;
use Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter\FilterResolverInterface;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnExpressionResolverInterface;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnResolverInterface;

class Context
{
    /**
     * @var ReportResolver
     */
    private $reportResolver;

    /**
     * @var Provider
     */
    private $schemeProvider;

    /**
     * @var ColumnResolverInterface
     */
    private $columnResolver;

    /**
     * @var ColumnExpressionResolverInterface
     */
    private $columnExpressionResolver;

    /**
     * @var EntitySimpleRelationResolver
     */
    private $simpleRelationResolver;

    /**
     * @var FilterResolverInterface
     */
    private $filterResolver;

    public function __construct(
        ReportResolver $reportResolver,
        Provider $schemeProvider,
        ColumnResolverInterface $columnResolver,
        FilterResolverInterface $filterResolver,
        ColumnExpressionResolverInterface $columnExpressionResolver,
        EntitySimpleRelationResolver $simpleRelationResolver
    ) {
        $this->reportResolver = $reportResolver;
        $this->schemeProvider = $schemeProvider;
        $this->columnResolver = $columnResolver;
        $this->filterResolver = $filterResolver;
        $this->columnExpressionResolver = $columnExpressionResolver;
        $this->simpleRelationResolver = $simpleRelationResolver;
    }

    public function getReportResolver(): ReportResolver
    {
        return $this->reportResolver;
    }

    public function getEntitySchemeProvider(): Provider
    {
        return $this->schemeProvider;
    }

    public function getColumnResolver(): ColumnResolverInterface
    {
        return $this->columnResolver;
    }

    public function getColumnExpressionResolver(): ColumnExpressionResolverInterface
    {
        return $this->columnExpressionResolver;
    }

    public function getSimpleRelationResolver(): EntitySimpleRelationResolver
    {
        return $this->simpleRelationResolver;
    }

    public function getFilterResolver(): FilterResolverInterface
    {
        return $this->filterResolver;
    }
}
