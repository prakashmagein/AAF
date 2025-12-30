<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter;

use Amasty\ReportBuilder\Model\ResourceModel\Report\Data\Select;

class FilterApplier implements FilterApplierInterface
{
    /**
     * @var FilterResolverInterface
     */
    private $filterResolver;

    /**
     * @var InternalFilterApplier
     */
    private $internalFilterApplier;

    /**
     * @var FilterColumn
     */
    private $filterColumn;

    /**
     * @var ConditionPreprocessor\PreprocessorInterface[]
     */
    private $conditionPreprocessors;

    /**
     * @param FilterResolverInterface $filterResolver
     * @param InternalFilterApplier $internalFilterApplier
     * @param FilterColumn $filterColumn
     * @param ConditionPreprocessor\PreprocessorInterface[] $conditionPreprocessors
     */
    public function __construct(
        FilterResolverInterface $filterResolver,
        InternalFilterApplier $internalFilterApplier,
        FilterColumn $filterColumn,
        array $conditionPreprocessors = []
    ) {
        $this->filterResolver = $filterResolver;
        $this->internalFilterApplier = $internalFilterApplier;
        $this->filterColumn = $filterColumn;
        $this->conditionPreprocessors = $conditionPreprocessors;
    }

    public function apply(Select $select): void
    {
        $filters = $this->filterResolver->resolve();

        foreach ($filters as $filter => $conditions) {
            if ($this->internalFilterApplier->apply($filter, $conditions)) {
                continue;
            }

            $conditionsExpression = $this->prepareFilterConditions($filter, $conditions);
            if (!$conditionsExpression) {
                continue;
            }

            if ($this->filterColumn->isFilterCanUseWhere($filter)) {
                $select->where($conditionsExpression);
            } else {
                $select->having($conditionsExpression);
            }
        }
    }

    public function prepareFilterConditions(string $filter, array $conditions): string
    {
        foreach ($this->conditionPreprocessors as $preprocessor) {
            $result = $preprocessor->process($filter, $conditions);

            if ($result !== null) {
                return $result;
            }
        }

        return '';
    }
}
