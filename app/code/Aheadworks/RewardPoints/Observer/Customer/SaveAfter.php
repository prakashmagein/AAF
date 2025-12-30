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
namespace Aheadworks\RewardPoints\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\RequestInterface;
use Aheadworks\RewardPoints\Model\Customer\Processor\Saving as CustomerSavingProcessor;

/**
 * Class SaveAfter
 *
 * @package Aheadworks\RewardPoints\Observer\Customer
 */
class SaveAfter implements ObserverInterface
{
    /**
     * @var CustomerSavingProcessor
     */
    private $customerSavingProcessor;

    /**
     * @param CustomerSavingProcessor $customerSavingProcessor
     */
    public function __construct(
        CustomerSavingProcessor $customerSavingProcessor
    ) {
        $this->customerSavingProcessor = $customerSavingProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var CustomerInterface $customer */
        $customer = $event->getData('customer');
        /** @var RequestInterface $request */
        $request = $event->getData('request');
        if ($customer && $customer instanceof CustomerInterface
            && $request && $request instanceof RequestInterface
        ) {
            $this->customerSavingProcessor->processBalanceAdjustment($customer, $request);
        }
    }
}
