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

namespace FME\Refund\Model\Order\Processor;


class Processor 
{
    protected $creditMemoFactory;
    protected $creditMemoService;
    protected $invoice;
    protected $_messageManager;

    public function __construct(
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory,
        \Magento\Sales\Model\Order\Invoice $invoice,
        \Magento\Sales\Model\Service\CreditmemoService $creditmemoService,
        \Magento\Framework\Message\ManagerInterface $messageManager
            ) 
    {
        $this->order = $order;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->creditmemoService = $creditmemoService;
        $this->_messageManager = $messageManager;
        $this->invoice = $invoice; 
    }
    
    public function refundCode($orderid)
    {
        
        $check = null;
        $invoiceincrementid = null;
        $order = $this->order;
        $order->load($orderid);
    
        $invoices = $order->getInvoiceCollection();
        foreach ($invoices as $invoice) {
            $invoiceincrementid = $invoice->getIncrementId();
        }

        if($invoiceincrementid != null)
        {

            $invoiceobj = $this->invoice->loadByIncrementId($invoiceincrementid);
            $creditmemo = $this->creditmemoFactory->createByOrder($order);
            $creditmemo->setInvoice($invoiceobj);
            $this->creditmemoService->refund($creditmemo); 
            $order->setStatus('closed');
            $order->save();
           $check =1; 

        }
        else
        {
            $message = 'No Invoice found against this ordrer please create an invoice for the selected order, After Creating invoice go to the refund listing and try again';
            $this->_messageManager->addWarning($message);   
            $check =0;

        }

        return $check;
    
    }
}