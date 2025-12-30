<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Block\Adminhtml\Renderer;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;
use Magefan\OrderEdit\Block\Adminhtml\Renderer\GridElement\Grid;

class GridElement extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @var Grid
     */
    private $grid;

    /**
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param Grid $grid
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        Grid $grid,
        array $data = []
    ) {
        $this->grid = $grid;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getElementHtml(): string
    {
        return $this->grid->getBlockGrid()->toHtml();
    }
}
