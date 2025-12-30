<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\OrderEdit\Block\Adminhtml\Order;

use Magento\Backend\Block\Template;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magefan\OrderEdit\Model\ResourceModel\History\CollectionFactory as HistoryCollectionFactory;
use Magefan\OrderEdit\Model\ResourceModel\History\Collection as HistoryCollection;

class EditHistory extends Template
{
    /**
     * @var HistoryCollectionFactory
     */
    protected $historyCollectionFactory;

    protected $historyCollection;

    /**
     * @param Template\Context $context
     * @param HistoryCollectionFactory $historyCollectionFactory
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        Template\Context $context,
        HistoryCollectionFactory $historyCollectionFactory,
        array $data = [],
        ?JsonHelper
        $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        $this->historyCollectionFactory = $historyCollectionFactory;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

    /**
     * @return HistoryCollection
     */
    public function getHistoryCollection(): HistoryCollection
    {
        if (!isset($this->historyCollection)) {
            $this->historyCollection = $this->historyCollectionFactory->create()
                                       ->addFieldToFilter('order_id', ['eq' => (int)$this->getRequest()->getParam('order_id')])
                                       ->addOrder('created_at', 'DESC');
        }

        return $this->historyCollection;
    }
}
