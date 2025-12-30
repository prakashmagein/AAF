<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\RelationValidator;

use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Provider as SchemeProvider;

class IsRelationValid implements IsRelationValidInterface
{
    /**
     * @var SchemeProvider
     */
    private $schemeProvider;

    /**
     * @var IsRelationValidInterface
     */
    private $isRelationValidDefault;

    /**
     * @var IsRelationValidInterface[]
     */
    private $validatorPool;

    public function __construct(
        SchemeProvider $schemeProvider,
        IsRelationValidInterface $isRelationValidDefault,
        array $validatorPool = []
    ) {
        $this->schemeProvider = $schemeProvider;
        $this->isRelationValidDefault = $isRelationValidDefault;
        $this->validatorPool = $validatorPool;
    }

    public function execute(array $relation): void
    {
        $entityScheme = $this->schemeProvider->getEntityScheme();

        $sourceEntity = $entityScheme->getEntityByName($relation[ReportInterface::SCHEME_SOURCE_ENTITY]);
        $relationScheme = $sourceEntity->getRelation($relation[ReportInterface::SCHEME_ENTITY]);

        $relationValidator = $this->validatorPool[$relationScheme->getRelationshipType()]
            ?? $this->isRelationValidDefault;

        $relationValidator->execute($relation);
    }
}
