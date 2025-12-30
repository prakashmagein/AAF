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
namespace Aheadworks\RewardPoints\Test\Unit\Model\Source\EarnRule;

use Aheadworks\RewardPoints\Model\Source\EarnRule\Status;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\Source\EarnRule\Status
 */
class StatusTest extends TestCase
{
    /**
     * @var Status
     */
    private $source;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->source = $objectManager->getObject(Status::class, []);
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $this->assertTrue(is_array($this->source->toOptionArray()));
    }
}
