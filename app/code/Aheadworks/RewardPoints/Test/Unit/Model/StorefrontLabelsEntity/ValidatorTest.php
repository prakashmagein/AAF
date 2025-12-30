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
namespace Aheadworks\RewardPoints\Test\Unit\Model\StorefrontLabelsEntity;

use Aheadworks\RewardPoints\Api\Data\StorefrontLabelsEntityInterface;
use Aheadworks\RewardPoints\Api\Data\StorefrontLabelsInterface;
use Aheadworks\RewardPoints\Model\StorefrontLabelsEntity\Validator;
use Aheadworks\RewardPoints\Ui\Component\Listing\Columns\Store\Options as StoreOptions;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\StorefrontLabelsEntity\Validator
 */
class ValidatorTest extends TestCase
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->validator = $objectManager->getObject(
            Validator::class,
            []
        );
    }

    /**
     * Test isValid method
     *
     * @param bool $isValid
     * @param StorefrontLabelsEntityInterface $storefrontLabelsEntity
     * @dataProvider isValidDataProvider
     */
    public function testIsValid(
        $isValid,
        $storefrontLabelsEntity
    ) {
        $this->assertEquals($isValid, $this->validator->isValid($storefrontLabelsEntity));
    }

    /**
     * @return array
     */
    public function isValidDataProvider()
    {
        return [
            [
                'isValid' => false,
                'storefrontLabelsEntity' => $this->getStorefrontLabelsEntityMock(null)
            ],
            [
                'isValid' => false,
                'storefrontLabelsEntity' => $this->getStorefrontLabelsEntityMock([])
            ],
            [
                'isValid' => false,
                'storefrontLabelsEntity' => $this->getStorefrontLabelsEntityMock(
                    [
                        $this->getStorefrontLabelsMock(123)
                    ]
                )
            ],
            [
                'isValid' => false,
                'storefrontLabelsEntity' => $this->getStorefrontLabelsEntityMock(
                    [
                        $this->getStorefrontLabelsMock(1),
                        $this->getStorefrontLabelsMock(1),
                    ]
                )
            ],
            [
                'isValid' => false,
                'storefrontLabelsEntity' => $this->getStorefrontLabelsEntityMock(
                    [
                        $this->getStorefrontLabelsMock(StoreOptions::ALL_STORE_VIEWS),
                        $this->getStorefrontLabelsMock(StoreOptions::ALL_STORE_VIEWS),
                    ]
                )
            ],
            [
                'isValid' => false,
                'storefrontLabelsEntity' => $this->getStorefrontLabelsEntityMock(
                    [
                        $this->getStorefrontLabelsMock(StoreOptions::ALL_STORE_VIEWS),
                        $this->getStorefrontLabelsMock(1),
                        $this->getStorefrontLabelsMock(1),
                    ]
                )
            ],
            [
                'isValid' => true,
                'storefrontLabelsEntity' => $this->getStorefrontLabelsEntityMock(
                    [
                        $this->getStorefrontLabelsMock(StoreOptions::ALL_STORE_VIEWS),
                        $this->getStorefrontLabelsMock(1),
                        $this->getStorefrontLabelsMock(2),
                    ]
                )
            ],
        ];
    }

    /**
     * Get storefront labels entity mock
     *
     * @param array|StorefrontLabelsInterface[]|\PHPUnit\Framework\MockObject\MockObject[] $labels
     * @return StorefrontLabelsEntityInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getStorefrontLabelsEntityMock($labels)
    {
        $storefrontLabelsEntityMock = $this->createMock(StorefrontLabelsEntityInterface::class);

        $storefrontLabelsEntityMock->expects($this->any())
            ->method('getLabels')
            ->willReturn($labels);

        return $storefrontLabelsEntityMock;
    }

    /**
     * Get storefront labels entity mock
     *
     * @param $storeId
     * @return StorefrontLabelsInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getStorefrontLabelsMock($storeId)
    {
        $storefrontLabelsMock = $this->createMock(StorefrontLabelsInterface::class);

        $storefrontLabelsMock->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);

        return $storefrontLabelsMock;
    }
}
