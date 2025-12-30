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
namespace Aheadworks\RewardPoints\Test\Unit\Model\Repository;

use Aheadworks\RewardPoints\Model\Repository\CollectionProcessor;
use Aheadworks\RewardPoints\Model\Repository\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\ConfigurationMismatchException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\Repository\CollectionProcessor
 */
class CollectionProcessorTest extends TestCase
{
    /**
     * @var CollectionProcessor
     */
    private $processor;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->processor = $objectManager->getObject(CollectionProcessor::class, []);
    }

    /**
     * Test process method
     *
     * @param CollectionProcessorInterface[]|MockObject[] $processors
     * @param SearchCriteria|MockObject $searchCriteria
     * @param AbstractCollection|MockObject $collection
     * @throws \ReflectionException
     * @dataProvider processDataProvider
     */
    public function testProcess($processors, $searchCriteria, $collection)
    {
        $this->setProperty('processors', $processors);

        $this->assertNull($this->processor->process($searchCriteria, $collection));
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $collectionMock = $this->createMock(AbstractCollection::class);

        return [
            [
                'processors' => [],
                'searchCriteria' => $searchCriteriaMock,
                'collection' => $collectionMock
            ],
            [
                'processors' => [$this->getProcessorMock($searchCriteriaMock, $collectionMock)],
                'searchCriteria' => $searchCriteriaMock,
                'collection' => $collectionMock
            ],
            [
                'processors' => [
                    $this->getProcessorMock($searchCriteriaMock, $collectionMock),
                    $this->getProcessorMock($searchCriteriaMock, $collectionMock)
                ],
                'searchCriteria' => $searchCriteriaMock,
                'collection' => $collectionMock
            ]
        ];
    }

    /**
     * Test process method if not valid processor
     */
    public function testProcessNotValidProcessor()
    {
        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $collectionMock = $this->createMock(AbstractCollection::class);

        $processorMock = $this->createMock(DataObject::class);
        $processors = [$processorMock];
        $this->setProperty('processors', $processors);

        $this->expectException(ConfigurationMismatchException::class);

        $this->assertNull($this->processor->process($searchCriteriaMock, $collectionMock));
    }

    /**
     * Get processor mock
     *
     * @param SearchCriteria|MockObject $searchCriteriaMock
     * @param AbstractCollection|MockObject $collectionMock
     * @return CollectionProcessorInterface|MockObject
     */
    private function getProcessorMock($searchCriteriaMock, $collectionMock)
    {
        $processorMock = $this->createMock(CollectionProcessorInterface::class);
        $processorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $collectionMock)
            ->willReturn(null);

        return $processorMock;
    }

    /**
     * Set property
     *
     * @param string $propertyName
     * @param mixed $value
     * @return mixed
     * @throws \ReflectionException
     */
    private function setProperty($propertyName, $value)
    {
        $class = new \ReflectionClass($this->processor);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->processor, $value);

        return $this;
    }
}
