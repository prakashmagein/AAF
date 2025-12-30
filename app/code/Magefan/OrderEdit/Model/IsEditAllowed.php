<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Model;

use Magento\Sales\Model\Order;
use Magento\Backend\Model\Auth\Session as AuthSession;

class IsEditAllowed
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var AuthSession
     */
    protected $authSession;

    /**
     * @param Config $config
     * @param AuthSession $authSession
     */
    public function __construct(
        Config $config,
        AuthSession $authSession
    ) {
        $this->config = $config;
        $this->authSession = $authSession;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function execute(Order $order): array
    {
        $restrictedBy = [];
        $isAllowed = true;
        $message = '';

        if (!$this->allowEditOrdersWithStatus($order)) {
            $restrictedBy[] = __('status');
        }

        if (!$this->allowEditInvoicedOrders($order)) {
            $restrictedBy[] = __('invoiced');
        }

        if (!$this->allowEditShippedOrders($order)) {
            $restrictedBy[] = __('shipped');
        }

        if (!$this->allowEditRefundedOrders($order)) {
            $restrictedBy[] = __('refunded');
        }

        if (!$this->isAdminUserAllowedToEditOrder($order)) {
            $restrictedBy[] = 'AdminUserAllowed';
        }

        if ($restrictedBy) {
            $isAllowed = false;
            $message = __('This order is under edit restriction(s): [%1]', implode(', ', $restrictedBy));
        }

        return [$isAllowed, $message];
    }

    /**
     * @param Order $order
     * @return bool
     */
    protected function allowEditOrdersWithStatus(Order $order): bool
    {
        $allowStatuses = explode(',', $this->config->allowEditOrdersWithStatuses($order->getStoreId()));

        return (bool)array_intersect([$order->getStatus(), 'any'], $allowStatuses);
    }

    /**
     * @param Order $order
     * @return bool
     */
    protected function allowEditInvoicedOrders(Order $order): bool
    {
        if (!$this->config->allowInvoicedOrders($order->getStoreId()) && $order->hasInvoices()) {
            return false;
        }

        return true;
    }

    /**
     * @param Order $order
     * @return bool
     */
    protected function allowEditShippedOrders(Order $order): bool
    {
        if (!$this->config->allowShippedOrders($order->getStoreId()) && $order->hasShipments()) {
            return false;
        }

        return true;
    }

    /**
     * @param Order $order
     * @return bool
     */
    protected function allowEditRefundedOrders(Order $order): bool
    {
        if (!$this->config->allowRefundedOrders($order->getStoreId()) && $order->getTotalRefunded()) {
            return false;
        }

        return true;
    }

    /**
     * @param Order $order
     * @return bool
     */
    protected function isAdminUserAllowedToEditOrder(Order $order): bool
    {
        if (!$this->config->isRestrictionBySuperUsersEnabled($order->getStoreId())) {
            return true;
        }

        $superUserIdsAllowedToEditOrder = explode(',', $this->config->getSuperUserIdsAllowedToEditOrder($order->getStoreId()));
        return in_array($this->authSession->getUser()->getId(), $superUserIdsAllowedToEditOrder);
    }
}
