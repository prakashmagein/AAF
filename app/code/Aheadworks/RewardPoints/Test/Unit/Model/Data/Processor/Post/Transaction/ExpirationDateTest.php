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
namespace Aheadworks\RewardPoints\Test\Unit\Model\Data\Processor\Post\Transaction;

use Aheadworks\RewardPoints\Model\Data\Processor\Post\Transaction\ExpirationDate;
use Aheadworks\RewardPoints\Model\DateTime;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Model\Filters\Transaction\ExpirationDateTest
 */
class ExpirationDateTest extends TestCase
{
    /**
     * @var ExpirationDate
     */
    private $object;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var DateTime|MockObject
     */
    private $dateTimeMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->dateTimeMock = $this->getMockBuilder(DateTime::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getDate',
                    'getTodayDate',
                    'getExpirationDate',
                ]
            )
            ->getMockForAbstractClass();

        $data = [
            'dateTime' => $this->dateTimeMock,
        ];
        $this->object = $this->objectManager->getObject(ExpirationDate::class, $data);
    }

    /**
     * Test filter method input date value
     */
    public function testProcessMethodInputDate()
    {
        $date = ['balance' => 10, 'expiration_date' => '2016-11-01 12:30:41AM'];
        $expectedDate = ['balance' => 10, 'expiration_date' => '2016-11-01 12:30:41AM'];

        $this->dateTimeMock->expects($this->once())
            ->method('getTodayDate')
            ->willReturn(0);

        $this->dateTimeMock->expects($this->exactly(2))
            ->method('getDate')
            ->with($date['expiration_date'])
            ->willReturn($date['expiration_date']);

        $this->assertEquals($expectedDate, $this->object->process($date));
    }

    /**
     * Test filter method input string throw exception
     */
    public function testProcessMethodInputStringThrowException()
    {
        $date = ['balance' => 10, 'expiration_date' => 'test_string'];

        $this->dateTimeMock->expects($this->once())
            ->method('getTodayDate')
            ->willReturn('2016-11-01 12:30:41AM');

        $this->dateTimeMock->expects($this->once())
            ->method('getDate')
            ->with($date['expiration_date'])
            ->willThrowException(
                new LocalizedException(
                    __('Invalid input date format %1', $date)
                )
            );

        $this->expectException(LocalizedException::class);

        $this->object->process($date);
    }
}
