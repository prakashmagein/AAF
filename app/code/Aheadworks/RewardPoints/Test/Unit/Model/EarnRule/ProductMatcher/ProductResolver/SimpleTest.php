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
namespace Aheadworks\RewardPoints\Test\Unit\Model\EarnRule\ProductMatcher\ProductResolver;

use Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\ProductResolver\Simple;
use Magento\Catalog\Model\Product;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\ProductResolver\Simple
 */
class SimpleTest extends TestCase
{
    /**
     * @var Simple
     */
    private $resolver;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->resolver = $objectManager->getObject(Simple::class, []);
    }

    /**
     * Test getProductsForValidation method
     */
    public function testGetProductsForValidation()
    {
        $productMock = $this->createMock(Product::class);

        $this->assertEquals([$productMock], $this->resolver->getProductsForValidation($productMock));
    }
}
