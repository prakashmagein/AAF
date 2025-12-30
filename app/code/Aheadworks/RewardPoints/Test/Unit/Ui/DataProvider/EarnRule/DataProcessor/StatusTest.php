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
namespace Aheadworks\RewardPoints\Test\Unit\Ui\DataProvider\EarnRule\DataProcessor;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Ui\DataProvider\EarnRule\DataProcessor\Status;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Ui\DataProvider\EarnRule\DataProcessor\Status
 */
class StatusTest extends TestCase
{
    /**
     * @var Status
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

        $this->processor = $objectManager->getObject(Status::class, []);
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
                'result' => []
            ],
            [
                'data' => [EarnRuleInterface::STATUS => 0],
                'result' => [EarnRuleInterface::STATUS => '0']
            ],
            [
                'data' => [EarnRuleInterface::STATUS => 1],
                'result' => [EarnRuleInterface::STATUS => '1']
            ],
            [
                'data' => [EarnRuleInterface::STATUS => '0'],
                'result' => [EarnRuleInterface::STATUS => '0']
            ],
            [
                'data' => [EarnRuleInterface::STATUS => '1'],
                'result' => [EarnRuleInterface::STATUS => '1']
            ],
        ];
    }
}
