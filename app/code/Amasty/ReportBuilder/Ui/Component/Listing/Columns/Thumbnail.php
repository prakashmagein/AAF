<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Ui\Component\Listing\Columns;

use Magento\Catalog\Model\Product\Image;
use Magento\Catalog\Model\Product\ImageFactory;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Catalog product image preview UI component for listing.
 */
class Thumbnail extends Column
{
    public const NAME = 'thumbnail';

    public const IMAGE_TYPE = 'thumbnail';

    /**
     * @var Image
     */
    private $imageModel;

    /**
     * @var ImageFactory
     */
    private $productImageFactory;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ImageFactory $productImageFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->productImageFactory = $productImageFactory;
    }

    /**
     * Prepare thumbnail image configuration.
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            $imageModel = $this->getImageModel();
            foreach ($dataSource['data']['items'] as &$item) {
                $imageModel->setBaseFile($item[$fieldName]);
                $item[$fieldName . '_src'] = $imageModel->getUrl();
            }
        }

        return $dataSource;
    }

    private function getImageModel(): Image
    {
        if ($this->imageModel === null) {
            $this->imageModel = $this->productImageFactory->create();
            $this->imageModel->setDestinationSubdir(self::IMAGE_TYPE);
        }

        return $this->imageModel;
    }
}
