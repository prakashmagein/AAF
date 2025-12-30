<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\EntityScheme\Builder\Xml\Reader;

use Amasty\ReportBuilder\Model\EntityScheme\Builder\Xml\Reader\Dom;

class Filesystem extends \Magento\Framework\Config\Reader\Filesystem
{
    // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
    public function __construct(
        \Magento\Framework\Config\FileResolverInterface $fileResolver,
        Converter $converter,
        SchemaLocator $schemaLocator,
        \Magento\Framework\Config\ValidationStateInterface $validationState,
        $fileName = null,
        $idAttributes = [],
        $domDocumentClass = Dom::class,
        $defaultScope = 'ambuilder_entity_scheme'
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }
}
