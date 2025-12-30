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
use Amasty\ReportBuilder\Model\ResourceModel\Table\IsColumnExist;

class IsSimpleColumnValid implements IsColumnValidInterface
{
    /**
     * @var SchemeProvider
     */
    private $schemeProvider;

    /**
     * @var IsColumnExist
     */
    private $isColumnExist;

    public function __construct(
        SchemeProvider $schemeProvider,
        IsColumnExist $isColumnExist
    ) {
        $this->schemeProvider = $schemeProvider;
        $this->isColumnExist = $isColumnExist;
    }

    /**
     * @param string $columnId
     * @return void
     * @throws NotExistColumnException
     */
    public function execute(string $columnId): void
    {
        $entityScheme = $this->schemeProvider->getEntityScheme();
        $column = $entityScheme->getColumnById($columnId);
        $entity = $entityScheme->getEntityByName($column->getEntityName());

        if (!$this->isColumnExist->execute($entity->getMainTable(), $column->getName())) {
            throw new NotExistColumnException(__(
                'Column \'%1\' does not exist for table \'%2\'',
                $column->getName(),
                $entity->getMainTable()
            ));
        }
    }
}
