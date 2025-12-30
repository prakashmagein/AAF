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
namespace Aheadworks\RewardPoints\Test\Unit\Block\Adminhtml\Transaction\NewAction;

use Aheadworks\RewardPoints\Block\Adminhtml\Transaction\NewAction\BackButton;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Block\Adminhtml\Transaction\NewAction\BackButtonTest
 */
class BackButtonTest extends TestCase
{
    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlBuilderMock;

    /**
     * @var BackButton
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

        $this->prepareContext();

        $this->urlBuilderMock = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUrl'])
            ->getMockForAbstractClass();

        $data = [
            'context' => $this->contextMock,
        ];

        $this->object = $objectManager->getObject(BackButton::class, $data);
    }

    /**
     * Test getBackUrl method
     */
    public function testGetBackUrlMethod()
    {
        $backUrl = 'aw_reward_points/transaction/index';
        $this->expectsGetUrlMethod($backUrl);

        $this->assertEquals($backUrl, $this->object->getBackUrl());
    }

    /**
     * Test getButtonData method
     */
    public function testGetButtonDataMethod()
    {
        $backUrl = 'aw_reward_points/transaction/index';
        $expectsData = [
            'label' => 'Back',
            'on_click' => 'location.href = \'' . $backUrl . '\';',
            'class' => 'back',
            'sort_order' => 10
        ];
        $this->expectsGetUrlMethod($backUrl);

        $this->assertEquals($expectsData, $this->object->getButtonData());
    }

    /**
     * Expects getUrl
     *
     * @param string $url
     */
    private function expectsGetUrlMethod($url)
    {
        $this->contextMock->expects($this->once())
            ->method('getUrlBuilder')
            ->willReturn($this->urlBuilderMock);

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('*/*/')
            ->willReturn($url);
    }

    /**
     * Prepare context mock
     */
    private function prepareContext()
    {
        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getUrlBuilder'
                ]
            )
            ->getMock();
    }
}
