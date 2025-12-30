<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Source;

use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Website implements OptionSourceInterface
{
    /**
     * @var WebsiteCollectionFactory
     */
    private $websiteCollectionFactory;

    public function __construct(WebsiteCollectionFactory $websiteCollectionFactory)
    {
        $this->websiteCollectionFactory = $websiteCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options =  [['value' => '', 'label' => __('--Please Select Entity--')]];
        $collection = $this->websiteCollectionFactory->create();

        foreach ($collection as $website) {
            $options[] = [
                'value' => $website->getWebsiteId(),
                'label' => $website->getName()
            ];
        }

        return $options;
    }
}
