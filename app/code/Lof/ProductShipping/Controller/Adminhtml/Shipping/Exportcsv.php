<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ProductShipping
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\ProductShipping\Controller\Adminhtml\Shipping;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Exportcsv extends \Lof\ProductShipping\Controller\Adminhtml\Shipping
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $directory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry         $coreRegistry
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param Filesystem $filesystem
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\LayoutInterface $layout,
        Filesystem $filesystem,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    )
    {
        parent::__construct($context, $coreRegistry);
        $this->_layout     = $layout;
        $this->directory   = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->fileFactory = $fileFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            // init model and delete
            $collection = $this->_objectManager->create('Lof\ProductShipping\Model\Shipping')->getCollection();
            $params     = [];
            foreach ($collection as $key => $model) {
                $params[] = $model->getData();
            }
            $name = 'adminShippingInfo';
            $file = 'export/productShipping/' . $name . '.csv';

            $this->directory->create('export');
            $stream = $this->directory->openFile($file, 'w+');
            $stream->lock();
            $headers = $fields = [];
            $headers = [
                'dest_country_id',
                'dest_region_id',
                'dest_zip',
                'dest_zip_to',
                'price',
                'weight_from',
                'weight_to',
                'quantity_from',
                'quantity_to',
                'priority',
                'shipping_method',
                'partner_id',
                "allow_second_price",
                "second_price",
                "cost",
                "allow_free_shipping",
                "free_shipping",
                "price_for_unit",
                "description",
                'products',
            ];
            $stream->writeCsv($headers);
            foreach ($params as $row) {
                $rowData = $fields;
                foreach ($row as $v) {
                    $rowData['dest_country_id'] = $row['dest_country_id'];
                    $rowData['dest_region_id']  = $row['dest_region_id'];
                    $rowData['dest_zip']        = strip_tags($row['dest_zip']);
                    $rowData['dest_zip_to']     = $row['dest_zip_to'];
                    $rowData['price']           = $row['price'];
                    $rowData['weight_from']     = $row['weight_from'];
                    $rowData['weight_to']       = $row['weight_to'];

                    $rowData['quantity_from'] = $row['quantity_from'];
                    $rowData['quantity_to']   = $row['quantity_to'];
                    $rowData['priority']      = $row['priority'];

                    $rowData['shipping_method_id'] = strip_tags($row['shipping_method_id']);
                    $rowData['partner_id']         = $row['partner_id'];
                    $rowData['allow_second_price']         = $row['allow_second_price'];
                    $rowData['second_price']         = $row['second_price'];
                    $rowData['cost']         = $row['cost'];
                    $rowData['allow_free_shipping']         = $row['allow_free_shipping'];
                    $rowData['free_shipping']         = $row['free_shipping'];
                    $rowData['price_for_unit']         = isset($row['price_for_unit']) ? (int)$row['price_for_unit'] : 1;
                    $rowData['description']         = isset($row['description']) ? $row['description'] : "";
                }


                $p = [];
                foreach ($row["products"] as $product) {
                    $p[] = $product["product_id"] . "-" . $product["position"];
                }
                $s                   = join('|', $p);
                $rowData["products"] = $s;
                $stream->writeCsv($rowData);

            }
            $stream->unlock();
            $stream->close();
            $file = [
                'type'  => 'filename',
                'value' => $file,
                'rm'    => true  // can delete file after use
            ];
            // display success message
            $this->messageManager->addSuccess(__('You export sms to csv success.'));

            return $this->fileFactory->create($name . '.csv', $file, 'var');

        } catch (\Exception $e) {
            // display error message
            $this->messageManager->addError($e->getMessage());

            // go back to edit form
            return $resultRedirect->setPath('*/*/index');
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a smslog to exportcsv.'));

        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_ProductShipping::export_tocsv');
    }
}
