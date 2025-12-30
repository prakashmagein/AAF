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
namespace Aheadworks\RewardPoints\Test\Unit\Controller\Adminhtml\Customers;

use Aheadworks\RewardPoints\Controller\Adminhtml\Customers\Upload;
use Aheadworks\RewardPoints\Model\FileUploader;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Controller\Adminhtml\Customers\Upload
 */
class UploadTest extends TestCase
{
    /**
     * @var Upload
     */
    private $controller;

    /**
     * @var FileUploader|MockObject
     */
    private $fileUploaderMock;

    /**
     * @var ResultFactory|MockObject
     */
    private $resultFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->fileUploaderMock = $this->getMockBuilder(FileUploader::class)
            ->setMethods(['saveToTmpFolder'])
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'resultFactory' => $this->resultFactoryMock
            ]
        );

        $this->controller = $objectManager->getObject(
            Upload::class,
            [
                'context' => $contextMock,
                'fileUploader' => $this->fileUploaderMock
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $result = [
            'name' => '1.csv',
            'size' => 264,
            'path' => '/var/www/aheadworks/ecommerce/pub/media/aw_rewardpoints/imports',
            'file' => '1.csv',
            'url' => 'https://ecommerce.aheadworks.com/pub/media/aw_rewardpoints/imports/1.csv',
            'full_path' => '/var/www/aheadworks/ecommerce/pub/media/aw_rewardpoints/imports/1.csv',
        ];

        $this->fileUploaderMock->expects($this->once())
            ->method('saveToTmpFolder')
            ->with(Upload::FILE_ID)
            ->willReturn($result);
        $resultJsonMock = $this->getMockBuilder(ResultJson::class)
            ->setMethods(['setData'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->with($result)
            ->willReturnSelf();
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($resultJsonMock);

        $this->assertSame($resultJsonMock, $this->controller->execute());
    }
}
