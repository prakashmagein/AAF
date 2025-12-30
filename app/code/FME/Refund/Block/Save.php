<?php
/**
* FME Extensions
*
* NOTICE OF LICENSE
*
* This source file is subject to the fmeextensions.com license that is
* available through the world-wide-web at this URL:
* https://www.fmeextensions.com/LICENSE.txt
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this extension to newer
* version in the future.
*
* @category  FME
* @author    Hassan <support@fmeextensions.com>
* @package   FME_Refund
* @copyright Copyright (c) 2021 FME (http://fmeextensions.com/)
* @license   https://fmeextensions.com/LICENSE.txt
*/

namespace FME\Refund\Block;

use Magento\Framework\View\Element\Template;

class Save extends Template
{
    protected $customer;
    protected $orderRepository;
    protected $refundFactory;
    protected $_helper;

    public function __construct(
        Template\Context $context,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \FME\Refund\Model\RefundFactory $refundFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        $this->refundFactory = $refundFactory;
        $this->date = $date;
        parent::__construct($context, $data);
    }

    public function getFormAction()
    {
        return $this->getUrl('refund/index/index', ['_secure' => true]);
    }

    //function to get the date at which oderr was placed
    public function orderPlacedDate($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        $createdDate = $order->getCreatedAt();

        return $createdDate;
    }

    //function to get the currect date
    public function getCurrentDate()
    {
        $date = $this->date->gmtDate();

        return $date;
    }

    public function getDetails($orderId): object
    {
        $model = $this->refundFactory->create()->getCollection()
            ->addFieldToFilter('order_id', $orderId);

        return $model;
    }

    public function getRequestStatus($orderId)
    {
        $model = $this->refundFactory->create()->getCollection()
        ->addFieldToFilter('order_id', $orderId);
        $modelData = $model->getData();
        if ($modelData) {
            return $modelData[0]['status'];
        }
    }
}
