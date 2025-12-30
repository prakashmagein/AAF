<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Source\ExcludedEntities;

use Amasty\ReportBuilder\Api\EntityInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Builder\Xml;
use Magento\Framework\Data\OptionSourceInterface;

class Entity implements OptionSourceInterface
{
    /**
     * @var Xml
     */
    private $xmlBuilder;

    public function __construct(Xml $xmlBuilder)
    {
        $this->xmlBuilder = $xmlBuilder;
    }

    public function toOptionArray(): array
    {
        $schemeData = $this->xmlBuilder->build();

        $options =  [];
        foreach ($schemeData as $entityData) {
            $title = $entityData[EntityInterface::TITLE];
            if (!empty($entityData[EntityInterface::PRIMARY])) {
                $title .= ' (' . __('Main Entity') . ')';
            }
            $options[] = [
                'value' => $entityData[EntityInterface::NAME],
                'label' => $title
            ];
        }

        return $options;
    }
}
