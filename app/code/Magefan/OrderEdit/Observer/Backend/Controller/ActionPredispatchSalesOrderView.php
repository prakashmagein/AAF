<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Observer\Backend\Controller;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Backend\Model\Session\Quote as SessionQuote;
use Magento\Store\Model\StoreManagerInterface;
use Magefan\OrderEdit\Model\Config;

class ActionPredispatchSalesOrderView implements ObserverInterface
{
    /**
     * @var SessionQuote
     */
    private $sessionQuote;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param SessionQuote $sessionQuote
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     */
    public function __construct(
        SessionQuote $sessionQuote,
        StoreManagerInterface $storeManager,
        Config $config
    ) {
        $this->sessionQuote = $sessionQuote;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->config->isEnabled()) {
            $this->sessionQuote->clearStorage();
        }
    }
}
