<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Observer\Backend\Adminhtml;

use Magento\Framework\Message\Manager as MessageManager;
use Magento\Framework\ObjectManager\ObjectManager;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Exception\NoSuchEntityException;

class SalesOrderCreateProcessItemBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ProductHelper
     */
    private $productHelper;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @param MessageManager $messageManager
     */
    public function __construct(
        MessageManager $messageManager,
        ObjectManager $objectManager,
        ProductHelper $productHelper,
        OrderRepository $orderRepository
    ) {
        $this->messageManager = $messageManager;
        $this->objectManager = $objectManager;
        $this->productHelper = $productHelper;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $request = $observer->getRequestModel();

        if ($request->getFullActionName() === 'mforderedit_order_loadBlock') {
            $order = $observer->getSession()->getOrder();

            if ($request->getPost('update_items')) {
                $items = $request->getPost('item', []);

                $itemsPrepared = $this->_processFiles($items);
                $blockedItemsByTypes = ['shipping' => '', 'invoiced'=> ''];

                foreach ($itemsPrepared as $itemId => $info) {
                    $qtyToSet = (int)$info['qty'];
                    $orderItem = $order->getItemByQuoteItemId($itemId);

                    if ($orderItem) {
                        $qtyShipped = (int)$orderItem->getQtyShipped();
                        $qtyInvoiced = (int)$orderItem->getQtyInvoiced();

                        if ($qtyInvoiced < $qtyToSet) {
                            $order->setState('new');
                        }

                        if ($qtyToSet < $qtyShipped) {
                            unset($items[$itemId]);
                            $blockedItemsByTypes['shipping'] .= $orderItem->getSku() . ',';
                        } elseif ($qtyToSet < $qtyInvoiced) {
                            unset($items[$itemId]);
                            $blockedItemsByTypes['invoiced'] .= $orderItem->getSku() . ',';
                        }
                    }
                }

                $error = '';

                if ($blockedItemsByTypes['shipping']) {
                    $error .= __('The qty of items: %1 is less than shipped.', $blockedItemsByTypes['shipping']);
                }

                if ($blockedItemsByTypes['invoiced']) {
                    $error .= __('The qty of items: %1 is less than invoiced.', $blockedItemsByTypes['invoiced']);
                }

                if ($error) {
                    $this->messageManager->addErrorMessage(
                        __(
                            $error
                        )
                    );
                    $request->setPostValue('item', $items);
                }
            }

            try {
                $this->orderRepository->save($order);
            } catch (NoSuchEntityException $e) {
                return ;
            }
        }
    }

    /**
     * Process buyRequest file options of items
     *
     * @param array $items
     * @return array
     */
    protected function _processFiles($items)
    {
        foreach ($items as $id => $item) {
            $buyRequest = new \Magento\Framework\DataObject($item);
            $params = ['files_prefix' => 'item_' . $id . '_'];
            $buyRequest = $this->productHelper->addParamsToBuyRequest($buyRequest, $params);
            if ($buyRequest->hasData()) {
                $items[$id] = $buyRequest->toArray();
            }
        }
        return $items;
    }
}
