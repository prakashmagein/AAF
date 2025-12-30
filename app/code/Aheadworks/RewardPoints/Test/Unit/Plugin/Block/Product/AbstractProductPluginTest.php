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

use Aheadworks\RewardPoints\Block\ProductList\CategoryText;
use Aheadworks\RewardPoints\Block\ProductList\CategoryTextFactory;
use Aheadworks\RewardPoints\Plugin\Block\Product\AbstractProductPlugin;
use Magento\Catalog\Block\Product\ListProduct as ListProductBlock;
use Magento\Catalog\Model\Product;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Plugin\Block\Product\AbstractProductPlugin
 */
class AbstractProductPluginTest extends TestCase
{
    /**
     * @var AbstractProductPlugin
     */
    private $plugin;

    /**
     * @var CategoryTextFactory|MockObject
     */
    private $categoryTextFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->categoryTextFactoryMock = $this->createMock(CategoryTextFactory::class);

        $this->plugin = $objectManager->getObject(
            AbstractProductPlugin::class,
            [
                'categoryTextFactory' => $this->categoryTextFactoryMock,
            ]
        );
    }

    /**
     * Test aroundGetProductPrice method
     */
    public function testAroundGetProductPrice()
    {
        $listProductMock = $this->createMock(ListProductBlock::class);
        $productMock = $this->createMock(Product::class);
        $nativeBlockHtml = '<div>HTML Content</div>';
        $categoryTextHtml = '<div>Category Text HTML Content</div>';

        $proceed = function ($query) use ($productMock, $nativeBlockHtml) {
            $this->assertEquals($productMock, $query);
            return $nativeBlockHtml;
        };

        $categoryTextMock = $this->createMock(CategoryText::class);
        $this->categoryTextFactoryMock->expects($this->once())
            ->method('create')
            ->with(['data' => ['product' => $productMock]])
            ->willReturn($categoryTextMock);

        $categoryTextMock->expects($this->once())
            ->method('toHtml')
            ->willReturn($categoryTextHtml);

        $this->assertEquals(
            $nativeBlockHtml . $categoryTextHtml,
            $this->plugin->aroundGetProductPrice($listProductMock, $proceed, $productMock)
        );
    }
}
