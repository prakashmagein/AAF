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

use Aheadworks\RewardPoints\Block\Adminhtml\Transaction\NewAction\ResetButton;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Block\Adminhtml\Transaction\NewAction\ResetButtonTest
 */
class ResetButtonTest extends TestCase
{
    /**
     * @var ResetButton
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

        $this->object = $objectManager->getObject(ResetButton::class, []);
    }

    /**
     * Test getButtonData method
     */
    public function testGetButtonDataMethod()
    {
        $expectsData = [
            'label' => 'Reset',
            'class' => 'reset',
            'on_click' => 'location.reload();',
            'sort_order' => 30
        ];
        $this->assertEquals($expectsData, $this->object->getButtonData());
    }
}
