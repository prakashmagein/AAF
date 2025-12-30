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

use Aheadworks\RewardPoints\Block\Adminhtml\Transaction\NewAction\GenericButton;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Block\Adminhtml\Transaction\NewAction\GenericButtonTest
 */
class GenericButtonTest extends TestCase
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
     * @var GenericButton
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

        $this->object = $objectManager->getObject(GenericButton::class, $data);
    }

    /**
     * Test getUrl method
     *
     * @dataProvider dataProviderGetUrl
     * @param string $route
     * @param array $params
     * @param string $expects
     */
    public function testGetUrlMethod($route, $params, $expects)
    {
        $this->contextMock->expects($this->once())
            ->method('getUrlBuilder')
            ->willReturn($this->urlBuilderMock);

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with($route, $params)
            ->willReturn($expects);

        $this->assertEquals($expects, $this->object->getUrl($route, $params));
    }

    /**
     * Data provider for testGetUrlMethod
     *
     * @return array
     */
    public function dataProviderGetUrl()
    {
        return [
            [
                'sales/order/info',
                ['order_id' => 11, 'store' => 1],
                'sales/order/info/order_id/11/store/1'
            ]
        ];
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
