<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Model;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    /**
     * @var string
     */
    const XML_PATH_ORDER_EDIT_ENABLED = 'mforderedit/general/enabled';

    /**
     * @var string
     */
    const XML_PATH_ALLOW_EDIT_ORDERS_WITH_STATUS = 'mforderedit/general/allow_edit_orders_with_status';

    /**
     * @var string
     */
    const XML_PATH_ALLOW_EDIT_INVOICED_ORDERS = 'mforderedit/general/allow_edit_invoiced_orders';

    /**
     * @var string
     */
    const XML_PATH_ALLOW_EDIT_SHIPPED_ORDERS = 'mforderedit/general/allow_edit_shipped_orders';

    /**
     * @var string
     */
    const XML_PATH_ALLOW_EDIT_REFUNDED_ORDERS = 'mforderedit/general/allow_edit_refunded_orders';

    /**
     * @var string
     */
    const XML_PATH_RESTRICT_BY_SUPER_USERS = 'mforderedit/general/restrict_by_super_users';

    const XML_PATH_DISPLAY_PRODUCT_PRICES = 'tax/display/type';

    /**
     * @var string
     */
    const XML_PATH_SUPER_USERS = 'mforderedit/general/super_users';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve true if module is enabled
     *
     * @return bool
     */
    public function isEnabled($storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_ORDER_EDIT_ENABLED, $storeId);
    }

    /**
     * Retrieve true if module is enabled
     *
     * @return bool
     */
    public function displayPricesInCatalogInclTax($storeId = null): bool
    {
        return
            (\Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX == $this->getConfig(self::XML_PATH_DISPLAY_PRODUCT_PRICES, $storeId));
    }

    /**
     * @param $storeId
     * @return string
     */
    public function allowEditOrdersWithStatuses($storeId = null): string
    {
        return (string)$this->getConfig(self::XML_PATH_ALLOW_EDIT_ORDERS_WITH_STATUS, $storeId);
    }

    /**
     * @param $storeId
     * @return bool
     */
    public function allowInvoicedOrders($storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_ALLOW_EDIT_INVOICED_ORDERS, $storeId);
    }

    /**
     * @param $storeId
     * @return bool
     */
    public function allowShippedOrders($storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_ALLOW_EDIT_SHIPPED_ORDERS, $storeId);
    }

    /**
     * @param $storeId
     * @return bool
     */
    public function allowRefundedOrders($storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_ALLOW_EDIT_REFUNDED_ORDERS, $storeId);
    }

    /**
     * @param $storeId
     * @return bool
     */
    public function isRestrictionBySuperUsersEnabled($storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_RESTRICT_BY_SUPER_USERS, $storeId);
    }

    /**
     * @param $storeId
     * @return string
     */
    public function getSuperUserIdsAllowedToEditOrder($storeId = null): string
    {
        return (string)$this->getConfig(self::XML_PATH_SUPER_USERS, $storeId);
    }

    /**
     * Retrieve store config value
     *
     * @param  string $path
     * @param  null   $storeId
     * @return mixed
     */
    public function getConfig(string $path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
