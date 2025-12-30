<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter;

class FilterModifier implements FilterModifierInterface
{
    /**
     * @var FilterResolverInterface
     */
    private $resolver;

    /**
     * @var FilterStorageInterface
     */
    private $storage;

    public function __construct(
        FilterResolverInterface $resolver,
        FilterStorageInterface $storage
    ) {
        $this->resolver = $resolver;
        $this->storage = $storage;
    }

    public function modify(string $columnName, ?array $condition = null): void
    {
        $this->resolver->resolve();
        if ($condition) {
            $this->storage->addFilter($columnName, $condition);
        } else {
            $this->storage->removeFilter($columnName);
        }
    }
}
