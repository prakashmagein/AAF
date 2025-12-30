<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    2.4.0
 * @copyright  Copyright (c) 2024 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RewardPoints\Test\Unit\Plugin\Block\Product;

use Aheadworks\RewardPoints\Block\ProductList\Grouped\ProductText;
use Aheadworks\RewardPoints\Block\ProductList\Grouped\ProductTextFactory;
use Aheadworks\RewardPoints\Plugin\Block\Product\GroupedTypePlugin;
use Magento\Catalog\Model\Product;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\GroupedProduct\Block\Product\View\Type\Grouped as GroupedTypeBlock;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Plugin\Block\Product\GroupedTypePlugin
 */
class GroupedTypePluginTest extends TestCase
{
    /**
     * @var GroupedTypePlugin
     */
    private $plugin;

    /**
     * @var ProductTextFactory|MockObject
     */
    private $productTextFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->productTextFactoryMock = $this->createMock(ProductTextFactory::class);

        $this->plugin = $objectManager->getObject(
            GroupedTypePlugin::class,
            [
                'productTextFactory' => $this->productTextFactoryMock,
            ]
        );
    }

    /**
     * Test aroundGetProductPrice method
     */
    public function testAroundGetProductPrice()
    {
        $blockMock = $this->createMock(GroupedTypeBlock::class);
        $productMock = $this->createMock(Product::class);
        $nativeBlockHtml = '<div>HTML Content</div>';
        $productTextHtml = '<div>Product Text HTML Content</div>';

        $proceed = function ($query) use ($productMock, $nativeBlockHtml) {
            $this->assertEquals($productMock, $query);
            return $nativeBlockHtml;
        };

        $productTextMock = $this->createMock(ProductText::class);
        $this->productTextFactoryMock->expects($this->once())
            ->method('create')
            ->with(['data' => ['product' => $productMock]])
            ->willReturn($productTextMock);

        $productTextMock->expects($this->once())
            ->method('toHtml')
            ->willReturn($productTextHtml);

        $this->assertEquals(
            $nativeBlockHtml . $productTextHtml,
            $this->plugin->aroundGetProductPrice($blockMock, $proceed, $productMock)
        );
    }
}
