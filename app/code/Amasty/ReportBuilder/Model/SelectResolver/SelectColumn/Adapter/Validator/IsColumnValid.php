<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\Adapter\Validator;

use Amasty\ReportBuilder\Exception\NotExistColumnException;
use Amasty\ReportBuilder\Model\EntityScheme\Provider as SchemeProvider;

class IsColumnValid implements IsColumnValidInterface
{
    /**
     * @var SchemeProvider
     */
    private $schemeProvider;

    /**
     * @var IsColumnValidInterface
     */
    private $isColumnValidDefault;

    /**
     * @var IsColumnValidInterface[]
     */
    private $validatorPool;

    public function __construct(
        SchemeProvider $schemeProvider,
        IsColumnValidInterface $isColumnValidDefault,
        array $validatorPool = []
    ) {
        $this->schemeProvider = $schemeProvider;
        $this->isColumnValidDefault = $isColumnValidDefault;
        $this->validatorPool = $validatorPool;
    }

    /**
     * @param string $columnId
     * @return void
     * @throws NotExistColumnException
     */
    public function execute(string $columnId): void
    {
        $column = $this->schemeProvider->getEntityScheme()->getColumnById($columnId);
        if ($column === null) {
            throw new NotExistColumnException(__('Column with ID %1 does not exist', $columnId));
        }

        $columnValidator = $this->validatorPool[$column->getColumnType()] ?? $this->isColumnValidDefault;
        $columnValidator->execute($columnId);
    }
}
