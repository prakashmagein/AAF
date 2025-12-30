<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\EntityScheme\Builder\Xml\Reader\Converter;

class NodeParser
{
    /**
     * @var array
     */
    private $parsers;

    public function __construct(
        array $parsers = []
    ) {
        $this->parsers = $parsers;
    }

    public function parse(\DOMNode $childNode): array
    {
        $output[$childNode->nodeName] = isset($this->parsers[$childNode->nodeName])
            ? $this->parsers[$childNode->nodeName]->parse($childNode)
            : $childNode->nodeValue;

        return $output;
    }
}
