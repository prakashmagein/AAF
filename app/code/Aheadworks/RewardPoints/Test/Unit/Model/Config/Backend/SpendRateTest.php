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
namespace Aheadworks\RewardPoints\Test\Unit\Model\Config\Backend;

use Aheadworks\RewardPoints\Model\Config\Backend\SpendRate;
use Aheadworks\RewardPoints\Model\ResourceModel\SpendRate as SpendRateResource;
use Aheadworks\RewardPoints\Model\ResourceModel\SpendRate\Collection;
use Aheadworks\RewardPoints\Model\ResourceModel\SpendRate\CollectionFactory;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Model\Config\Backend\SpendRateTest
 */
class SpendRateTest extends TestCase
{
    /**
     * @var SpendRateResource|MockObject
     */
    private $spendRateResourceMock;

    /**
     * @var CollectionFactory|MockObject
     */
    private $spendRateCollectionFactoryMock;

    /**
     * @var Collection|MockObject
     */
    private $spendRateCollectionMock;

    /**
     * @var Serialize
     */
    private $serializerMock;

    /**
     * @var SpendRate
     */
    private $object;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->spendRateResourceMock = $this->getMockBuilder(SpendRateResource::class)
            ->disableOriginalConstructor()
            ->setMethods(
                ['saveConfigValue']
            )
            ->getMock();

        $this->spendRateCollectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(
                ['create']
            )
            ->getMock();

        $this->serializerMock = $this->getMockBuilder(Serialize::class)
            ->setMethods(
                ['serialize']
            )
            ->getMock();

        $this->spendRateCollectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(
                ['toConfigDataArray']
            )
            ->getMock();

        $data = [
            'spendRateResource' => $this->spendRateResourceMock,
            'spendRateCollectionFactory' => $this->spendRateCollectionFactoryMock,
            'serializer' => $this->serializerMock
        ];

        $this->object = $objectManager->getObject(SpendRate::class, $data);
    }

    /**
     * Test beforeSave method
     *
     * @dataProvider valueProvider
     * @param mixed $internalValue
     * @param string $expectedValue
     *
     */
    public function testBeforeSave($internalValue, $expectedValue)
    {
        $this->serializerMock
            ->method('serialize')
            ->with($internalValue)
            ->willReturn($expectedValue);

        $this->object->setValue($internalValue);
        $this->object->beforeSave();

        $this->assertEquals($internalValue, $this->object->getSpendRateValue());
        $this->assertEquals($expectedValue, $this->object->getValue());
    }

    /**
     * Test afterSave method
     *
     * @dataProvider valueProvider
     */
    public function testAfterSave($expectedValue)
    {
        $this->object->setSpendRateValue($expectedValue);

        $this->spendRateResourceMock->expects($this->once())
            ->method('saveConfigValue')
            ->with($expectedValue)
            ->willReturnSelf();

        $this->object->afterSave();
    }

    /**
     * Test afterLoad method
     */
    public function testAfterLoad()
    {
        $expectedResult = [[1, 2, 3, 4], [5, 6, 7, 8]];

        $this->spendRateCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->spendRateCollectionMock);

        $this->spendRateCollectionMock->expects($this->once())
            ->method('toConfigDataArray')
            ->willReturn($expectedResult);

        $this->object->afterLoad();

        $this->assertEquals($expectedResult, $this->object->getValue());
    }

    /**
     * Test data provider
     *
     * @return array
     */
    public function valueProvider()
    {
        return [
            ['setTestConfigValue', 's:18:"setTestConfigValue";'],
            [15, 'i:15;'],
            [null, 'N;'],
            [new \stdClass(), 'O:8:"stdClass":0:{}'],
            [[1,2,3,4], 'a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}']
        ];
    }
}
