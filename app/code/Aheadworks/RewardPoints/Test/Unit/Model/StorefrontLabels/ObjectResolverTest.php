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
namespace Aheadworks\RewardPoints\Test\Unit\Model\StorefrontLabels;

use Aheadworks\RewardPoints\Api\Data\StorefrontLabelsInterface;
use Aheadworks\RewardPoints\Api\Data\StorefrontLabelsInterfaceFactory;
use Aheadworks\RewardPoints\Model\StorefrontLabels\ObjectResolver;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ObjectResolverTest
 *
 * @package Aheadworks\RewardPoints\Test\Unit\Model\StorefrontLabels
 */
class ObjectResolverTest extends TestCase
{
    /**
     * @var ObjectResolver
     */
    private $model;

    /**
     * @var StorefrontLabelsInterfaceFactory|MockObject
     */
    private $storefrontLabelsFactoryMock;

    /**
     * @var DataObjectHelper|MockObject
     */
    private $dataObjectHelperMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->storefrontLabelsFactoryMock = $this->createMock(
            StorefrontLabelsInterfaceFactory::class
        );
        $this->dataObjectHelperMock = $this->createMock(
            DataObjectHelper::class
        );
        $this->model = $objectManager->getObject(
            ObjectResolver::class,
            [
                'storefrontLabelsFactory' => $this->storefrontLabelsFactoryMock,
                'dataObjectHelper' => $this->dataObjectHelperMock
            ]
        );
    }

    /**
     * Test resolve method
     *
     * @param array|StorefrontLabelsInterface $label
     * @param StorefrontLabelsInterface $expected
     * @dataProvider resolveDataProvider
     */
    public function testResolve($label, $expected)
    {
        if (is_array($label)) {
            $this->storefrontLabelsFactoryMock->expects($this->once())
                ->method('create')
                ->willReturn($expected);
            $this->dataObjectHelperMock->expects($this->once())
                ->method('populateWithArray')
                ->with($expected, $label, StorefrontLabelsInterface::class);
        }

        $this->assertEquals($expected, $this->model->resolve($label));
    }

    /**
     * Data provider for resolve
     *
     * @return array
     */
    public function resolveDataProvider()
    {
        $labelMock = $this->getMockForAbstractClass(StorefrontLabelsInterface::class);
        return [
            [
                $labelMock,
                $labelMock
            ],
            [
                [
                    StorefrontLabelsInterface::STORE_ID => 1,
                    StorefrontLabelsInterface::PRODUCT_PROMO_TEXT => 'product promo text',
                    StorefrontLabelsInterface::CATEGORY_PROMO_TEXT => 'category promo text',
                ],
                $labelMock
            ]
        ];
    }
}
