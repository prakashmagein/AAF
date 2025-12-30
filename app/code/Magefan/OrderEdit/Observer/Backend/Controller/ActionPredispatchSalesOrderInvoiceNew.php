<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Observer\Backend\Controller;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magefan\OrderEdit\Model\Config;
use Magefan\OrderEdit\Model\Invoice\NormalizeOrderItems;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Exception\NoSuchEntityException;

class ActionPredispatchSalesOrderInvoiceNew implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var NormalizeOrderItems
     */
    protected $normalizeOrderItems;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @param Config $config
     * @param NormalizeOrderItems $normalizeOrderItems
     * @param RequestInterface $request
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        Config $config,
        NormalizeOrderItems $normalizeOrderItems,
        RequestInterface $request,
        OrderRepository $orderRepository
    ) {
        $this->config = $config;
        $this->normalizeOrderItems = $normalizeOrderItems;
        $this->request = $request;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(
        Observer $observer
    ) {
        if ($this->config->isEnabled()) {
            $orderId = (int)$this->request->getParam('order_id');
            if ($orderId) {
                try {
                    $order = $this->orderRepository->get($orderId);
                    $this->normalizeOrderItems->execute($order);
                } catch (NoSuchEntityException $e) {
                    return;
                }
            }
        }
    }
}
