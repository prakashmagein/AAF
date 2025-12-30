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
namespace Aheadworks\RewardPoints\Test\Unit\Block\Product\View;

use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Aheadworks\RewardPoints\Block\Product\View\Share;
use Aheadworks\RewardPoints\Model\Calculator\RateCalculator;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Block\Product\View\ShareTest
 */
class ShareTest extends TestCase
{
    /**
     * @var Context|MockObject
     */
    private $context;

    /**
     * @var CustomerRewardPointsManagementInterface|MockObject
     */
    private $customerRewardPointsService;

    /**
     * @var RateCalculator|MockObject
     */
    private $rateCalculator;

    /**
     * @var Session|MockObject
     */
    private $customerSession;

    /**
     * @var Registry|MockObject
     */
    private $coreRegistry;

    /**
     * @var Share
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

        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerRewardPointsService = $this->getMockBuilder(CustomerRewardPointsManagementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->rateCalculator = $this->getMockBuilder(RateCalculator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerSession = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->coreRegistry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $data = [
            'context' => $this->context,
            'customerRewardPointsService' => $this->customerRewardPointsService,
            'rateCalculator' => $this->rateCalculator,
            'customerSession' => $this->customerSession,
            'coreRegistry' => $this->coreRegistry
        ];

        $this->object = $objectManager->getObject(Share::class, $data);
    }

    /**
     * Test template property
     */
    public function testTemplateProperty()
    {
        $class = new \ReflectionClass(Share::class);
        $property = $class->getProperty('_template');
        $property->setAccessible(true);

        $this->assertEquals('Aheadworks_RewardPoints::product/view/share.phtml', $property->getValue($this->object));
    }
}
