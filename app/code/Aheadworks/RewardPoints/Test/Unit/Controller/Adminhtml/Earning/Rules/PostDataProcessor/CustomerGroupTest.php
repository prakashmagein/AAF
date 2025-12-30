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
namespace Aheadworks\RewardPoints\Test\Unit\Controller\Adminhtml\Earning\Rules\PostDataProcessor;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Controller\Adminhtml\Earning\Rules\PostDataProcessor\CustomerGroup;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Controller\Adminhtml\Earning\Rules\PostDataProcessor\CustomerGroup
 */
class CustomerGroupTest extends TestCase
{
    /**
     * @var CustomerGroup
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

        $this->processor = $objectManager->getObject(CustomerGroup::class, []);
    }

    /**
     * Test process method
     *
     * @param array $data
     * @param array $result
     * @dataProvider processDataProvider
     */
    public function testProcess($data, $result)
    {
        $this->assertSame($result, $this->processor->process($data));
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            [
                'data' => [],
                'result' => [
                    EarnRuleInterface::CUSTOMER_GROUP_IDS => []
                ]
            ],
            [
                'data' => [
                    EarnRuleInterface::CUSTOMER_GROUP_IDS => []
                ],
                'result' => [
                    EarnRuleInterface::CUSTOMER_GROUP_IDS => []
                ]
            ],
            [
                'data' => [
                    EarnRuleInterface::CUSTOMER_GROUP_IDS => ['10']
                ],
                'result' => [
                    EarnRuleInterface::CUSTOMER_GROUP_IDS => [10]
                ]
            ],
            [
                'data' => [
                    EarnRuleInterface::CUSTOMER_GROUP_IDS => ['1', 2, '3']
                ],
                'result' => [
                    EarnRuleInterface::CUSTOMER_GROUP_IDS => [1, 2, 3]
                ]
            ],
        ];
    }
}
