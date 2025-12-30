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
namespace Aheadworks\RewardPoints\Test\Unit\Model\Data\Filter\Transaction;

use Aheadworks\RewardPoints\Api\Data\TransactionInterface;
use Aheadworks\RewardPoints\Model\Data\Filter\Transaction\CustomerSelection;
use Magento\Customer\Model\Config\Share;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Model\Filters\Transaction\CustomerSelectionTest
 */
class CustomerSelectionTest extends TestCase
{
    /**
     * @var CustomerSelection
     */
    private $object;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Share|MockObject
     */
    private $configShareMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->configShareMock = $this->createMock(Share::class);
        $data = [
            'dateTime' => $this->configShareMock,
            'fieldName' => 'customer_selections'
        ];
        $this->object = $this->objectManager->getObject(CustomerSelection::class, $data);
    }

    /**
     * Test filter method
     *
     * @dataProvider dataProviderFilterTest
     * @param mixed $value
     * @param mixed $expected
     */
    public function testFilterMethod($value, $expected)
    {
        $this->assertEquals($expected, $this->object->filter($value));
    }

    /**
     * Data provider for filter test
     *
     * @return array
     */
    public function dataProviderFilterTest()
    {
        return [
            [[1], []],
            [[], []],
            [[null], []],
            [[''], []],
            [[new \stdClass()], []],
            [[CustomerSelection::DEFAULT_FIELD_NAME => 1], []],
            [[CustomerSelection::DEFAULT_FIELD_NAME => []], []],
            [[CustomerSelection::DEFAULT_FIELD_NAME => null], []],
            [[CustomerSelection::DEFAULT_FIELD_NAME => ''], []],
            [[CustomerSelection::DEFAULT_FIELD_NAME => new \stdClass()], []],
            [[CustomerSelection::DEFAULT_FIELD_NAME => [1]], [[
                TransactionInterface::CUSTOMER_ID => null,
                TransactionInterface::CUSTOMER_NAME => null,
                TransactionInterface::CUSTOMER_EMAIL => null,
                TransactionInterface::COMMENT_TO_CUSTOMER => null,
                TransactionInterface::COMMENT_TO_ADMIN => null,
                TransactionInterface::BALANCE => null,
                TransactionInterface::EXPIRATION_DATE => null,
                TransactionInterface::WEBSITE_ID => null,
            ]]],
            [[CustomerSelection::DEFAULT_FIELD_NAME => [null]], [[
                TransactionInterface::CUSTOMER_ID => null,
                TransactionInterface::CUSTOMER_NAME => null,
                TransactionInterface::CUSTOMER_EMAIL => null,
                TransactionInterface::COMMENT_TO_CUSTOMER => null,
                TransactionInterface::COMMENT_TO_ADMIN => null,
                TransactionInterface::BALANCE => null,
                TransactionInterface::EXPIRATION_DATE => null,
                TransactionInterface::WEBSITE_ID => null,
            ]]],
            [
                [
                    CustomerSelection::DEFAULT_FIELD_NAME => [
                        [
                            TransactionInterface::CUSTOMER_ID => 1,
                            TransactionInterface::CUSTOMER_NAME => 'Test User',
                            TransactionInterface::CUSTOMER_EMAIL => 'test@test.com',
                            TransactionInterface::WEBSITE_ID => [1]
                        ]
                    ],
                    TransactionInterface::COMMENT_TO_CUSTOMER => 'Comment To Customer',
                    TransactionInterface::COMMENT_TO_ADMIN => 'Comment To admin',
                    TransactionInterface::BALANCE => 150,
                    TransactionInterface::EXPIRATION_DATE => '2016-11-01',
                    TransactionInterface::WEBSITE_ID => 1,
                ],
                [
                    [
                        TransactionInterface::CUSTOMER_ID => 1,
                        TransactionInterface::CUSTOMER_NAME => 'Test User',
                        TransactionInterface::CUSTOMER_EMAIL => 'test@test.com',
                        TransactionInterface::COMMENT_TO_CUSTOMER => 'Comment To Customer',
                        TransactionInterface::COMMENT_TO_ADMIN => 'Comment To admin',
                        TransactionInterface::BALANCE => 150,
                        TransactionInterface::EXPIRATION_DATE => '2016-11-01',
                        TransactionInterface::WEBSITE_ID => 1,
                    ]
                ]
            ],
        ];
    }
}
