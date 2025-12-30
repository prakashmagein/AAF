<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Source;

use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Magento\Framework\Data\OptionSourceInterface;

class PrimaryEntity implements OptionSourceInterface
{
    /**
     * @var Provider
     */
    private $entitySchemeProvider;

    public function __construct(Provider $provider)
    {
        $this->entitySchemeProvider = $provider;
    }

    public function toOptionArray(): array
    {
        $options =  [['value' => '', 'label' => __('--Please Select Entity--')]];
        $scheme = $this->entitySchemeProvider->getEntityScheme();

        foreach ($scheme->getAllEntitiesOptionArray(true) as $name => $title) {
            $options[] = [
                'value' => $name,
                'label' => $title
            ];
        }

        return $options;
    }
}
