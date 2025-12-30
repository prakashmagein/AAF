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
namespace Aheadworks\RewardPoints\Test\Unit\Model\EarnRule\Action\Processor;

use Aheadworks\RewardPoints\Model\Action\AttributeProcessor;
use Aheadworks\RewardPoints\Model\EarnRule\Action\Processor\RateMultiplier;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\EarnRule\Action\Processor\RateMultiplier
 */
class RateMultiplierTest extends TestCase
{
    /**
     * @var RateMultiplier
     */
    private $processor;

    /**
     * @var AttributeProcessor|MockObject
     */
    private $attributeProcessorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->attributeProcessorMock = $this->createMock(AttributeProcessor::class);

        $this->processor = $objectManager->getObject(
            RateMultiplier::class,
            [
                'attributeProcessor' => $this->attributeProcessorMock,
            ]
        );
    }

    /**
     * Test process method
     *
     * @param float $points
     * @param float $qty
     * @param float $multiplier
     * @param $result
     * @dataProvider processDataProvider
     */
    public function testProcess($points, $qty, $multiplier, $result)
    {
        $attributes = [$this->createMock(AttributeInterface::class)];

        $this->attributeProcessorMock->expects($this->once())
            ->method('getAttributeValueByCode')
            ->with('multiplier', $attributes)
            ->willReturn($multiplier);

        $this->assertEquals($result, $this->processor->process($points, $qty, $attributes));
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            [
                'points' => 0,
                'qty' => 1,
                'multiplier' => 1.5,
                'result' => 0
            ],
            [
                'points' => 15,
                'qty' => 1,
                'multiplier' => 0.5,
                'result' => 7.5
            ],
            [
                'points' => 15,
                'qty' => 2,
                'multiplier' => 0.5,
                'result' => 7.5
            ],
            [
                'points' => 15,
                'qty' => 2,
                'multiplier' => 0,
                'result' => 0
            ],
            [
                'points' => 15,
                'qty' => 2,
                'multiplier' => 1.5,
                'result' => 22.5
            ],
        ];
    }
}
