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
 * @copyright  Copyright (c) 2022 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\ProductShipping\Controller\Adminhtml\Shipping;

use Magento\Backend\App\Action;
use Lof\ProductShipping\Model\ShippingmethodFactory;
use Lof\ProductShipping\Model\ShippingFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var Lof\ProductShipping\Model\ShippingmethodFactory
     */
    protected $_mpshippingMethod;
    /**
     * @var Lof\ProductShipping\Model\Shipping
     */
    protected $_mpshipping;
    /**
     * @var Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploader;
    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $_csvReader;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        ShippingmethodFactory $shippingmethodFactory,
        ShippingFactory $mpshipping,
        UploaderFactory $fileUploader,
        \Magento\Framework\File\Csv $csvReader,
        \Magento\Backend\Helper\Js $jsHelper,
        ProductRepositoryInterface $productRepository
    )
    {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_mpshippingMethod  = $shippingmethodFactory;
        $this->_mpshipping        = $mpshipping;
        $this->_fileUploader      = $fileUploader;
        $this->_csvReader         = $csvReader;
        $this->jsHelper           = $jsHelper;
        $this->productRepository = $productRepository;
    }

    /**
     * Check for is allowed.
     *
     * @return bool
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data           = $this->getRequest()->getPostValue();

        if ($this->getRequest()->isPost()) {

            try {

                if (isset($_FILES['import_file'])) {

                    if ( ! $this->_formKeyValidator->validate($this->getRequest())) {
                        return $this->resultRedirectFactory->create()->setPath('*/*/index');
                    }

                    $uploader = $this->_fileUploader->create(
                        ['fileId' => 'import_file']
                    );

                    $result = $uploader->validateFile();

                    $file          = $result['tmp_name'];
                    $fileNameArray = explode('.', $result['name']);

                    $ext = end($fileNameArray);
                    if ($file != '' && $ext == 'csv') {
                        $csvFileData = $this->_csvReader->getData($file);
                        $partnerid   = 0;

                        //Count number of error row
                        $errorCount = 0;
                        $i          = 0;
                        $count = 0;
                        $headingArray = [];
                        foreach ($csvFileData as $key => $rowData) {
                            if ($count == 0) {
                                foreach ($rowData as $i=>$label) {
                                    if (!isset($headingArray[$label])) {
                                        $headingArray[$label] = $i;
                                    }
                                }
                                ++$count;
                                continue;
                            }

                            $shipping_method = $this->getRowValue($rowData, $headingArray, "shipping_method");
                            $shipping_method = !empty($shipping_method) ? $shipping_method : $this->getRowValue($rowData, $headingArray, "carrier");
                            $shippingMethodId = $shipping_method?$this->calculateShippingMethodId($shipping_method):0;
                            $shippingMethodId = $shippingMethodId?$shippingMethodId:$this->getRowValue($rowData, $headingArray, "shipping_method_id");

                            $temp                    = [];
                            $temp['dest_country_id'] = $this->getRowValue($rowData, $headingArray, "dest_country_id");
                            $temp['dest_region_id']  = $this->getRowValue($rowData, $headingArray, "dest_region_id");
                            $temp['dest_zip']        = $this->getRowValue($rowData, $headingArray, "dest_zip");
                            $temp['dest_zip_to']     = $this->getRowValue($rowData, $headingArray, "dest_zip_to");
                            $temp['price']           = (float)$this->getRowValue($rowData, $headingArray, "price");
                            $temp['second_price']    = (float)$this->getRowValue($rowData, $headingArray, "second_price");
                            $temp['cost']            = (float)$this->getRowValue($rowData, $headingArray, "cost");
                            $temp['free_shipping']   = (float)$this->getRowValue($rowData, $headingArray, "free_shipping");
                            $temp['allow_free_shipping']  = (int)$this->getRowValue($rowData, $headingArray, "allow_free_shipping");
                            $temp['allow_second_price']   = (int)$this->getRowValue($rowData, $headingArray, "allow_second_price");
                            $temp['weight_from']     = $this->getRowValue($rowData, $headingArray, "weight_from");
                            $temp['weight_to']       = $this->getRowValue($rowData, $headingArray, "weight_to");
                            $temp['quantity_from']   = $this->getRowValue($rowData, $headingArray, "quantity_from");
                            $temp['quantity_to']     = $this->getRowValue($rowData, $headingArray, "quantity_to");
                            $temp['priority']        = $this->getRowValue($rowData, $headingArray, "priority");
                            $temp['description']        = $this->getRowValue($rowData, $headingArray, "description");
                            $temp['price_for_unit']        = $this->getRowValue($rowData, $headingArray, "price_for_unit");
                            $temp['price_for_unit'] = (int)$temp['price_for_unit'];
                            if ($temp['price_for_unit'] > 1 || $temp['price_for_unit'] < 0) {
                                $temp['price_for_unit']  = 1;
                            }
                            $temp['description'] = !empty($temp['description']) ? strip_tags($temp['description']) : $temp['description'];
                            $temp['shipping_method_id'] = $shippingMethodId;
                            $temp['partner_id']         = (int)$this->getRowValue($rowData, $headingArray, "partner_id");

                            $temp['products'] = [];
                            $products = $this->getRowValue($rowData, $headingArray, "products");
                            $skus = $this->getRowValue($rowData, $headingArray, "skus");

                            if ( ! empty($products)) {
                                $tmpArr = explode("|", $products);
                                foreach ($tmpArr as $index => $item) {
                                    $item                         = explode("-", $item);
                                    $productId                    = isset($item[0]) ? (int)$item[0] : 0;
                                    $position                     = isset($item[1]) ? (int)$item[1] : $index;
                                    if ($productId) {
                                        $temp['products'][$productId] = ["product_position" => $position];
                                    }
                                }
                            } else {
                                $temp['products'] = [];
                            }

                            if ( ! empty($skus)) {
                                $tmpProducts = $this->getProductIdsBySkus($skus);
                                if (!empty($tmpProducts)) {
                                    foreach ($tmpProducts as $_id => $item) {
                                        $temp['products'][$_id] = $item;
                                    }
                                }
                            }

                            //If any required data left blank then skip this row and increase error counter by 1
                            if ($temp['dest_country_id'] == ''
                                || ['dest_region_id'] == ''
                                || ['dest_zip'] == ''
                                || ['dest_zip_to'] == ''
                                || ['price'] == ''
                                || ['weight_from'] == ''
                                || ['weight_to'] == ''
                                || ['quantity_from'] == ''
                                || ['quantity_to'] == ''
                            ) {
                                $errorCount++;
                                continue;
                            }

                            //Check if any exsitance then update the price
                            if ($temp['weight_from'] != "*" && $temp['weight_from'] != "") {
                                $temp['weight_from'] = (float)$temp['weight_from'];
                            }
                            if ($temp['weight_to'] != "*" && $temp['weight_to'] != "") {
                                $temp['weight_to'] = (float)$temp['weight_to'];
                            }
                            $collection = $this->_mpshipping->create()
                                                            ->getCollection()
                                                            ->addFieldToFilter('dest_country_id', $temp['dest_country_id'])
                                                            ->addFieldToFilter('dest_region_id', $temp['dest_region_id'])
                                                            ->addFieldToFilter('dest_zip', $temp['dest_zip'])
                                                            ->addFieldToFilter('dest_zip_to', $temp['dest_zip_to'])
                                                            ->addFieldToFilter('weight_from', $temp['weight_from'])
                                                            ->addFieldToFilter('weight_to', $temp['weight_to'])
                                                            ->addFieldToFilter('quantity_from', $temp['quantity_from'])
                                                            ->addFieldToFilter('quantity_to', $temp['quantity_to'])
                                                            ->addFieldToFilter('priority', $temp['priority'])
                                                            ->addFieldToFilter('allow_second_price', $temp['allow_second_price'])
                                                            ->addFieldToFilter('second_price', $temp['second_price'])
                                                            ->addFieldToFilter('cost', $temp['cost'])
                                                            ->addFieldToFilter('allow_free_shipping', $temp['allow_free_shipping'])
                                                            ->addFieldToFilter('free_shipping', $temp['free_shipping'])
                                                            ->addFieldToFilter('shipping_method_id', $temp['shipping_method_id'])
                                                            ->addFieldToFilter('partner_id', $temp['partner_id']);
                            if ($collection->getSize() > 0) {
                                foreach ($collection as $data) {
                                    $rowId         = $data->getLofshippingId();
                                    $dataArray     = ['price' => $temp['price']];
                                    $model         = $this->_mpshipping->create();
                                    $shippingModel = $model->load($rowId)->addData($dataArray);
                                    $shippingModel->setLofshippingId($rowId)->save();
                                }
                                //If no exsitance then insert new row to database
                            } else {
                                $shippingModel = $this->_mpshipping->create();
                                $shippingModel->setData($temp)->save();
                            }
                        }

                        if ($errorCount > 1) {
                            $this->messageManager->addNotice(__('Some rows are not valid!'));
                        } else {
                            $this->messageManager->addSuccess(__('Csv file has been successfully uploaded and imported'));
                        }

                        return $this->resultRedirectFactory->create()->setPath('*/*/index');

                    } else {
                        $this->messageManager->addError(__('Please upload Csv file'));
                    }
                } else {
                    $params = $data;

                    $id               = 0;
                    $partnerid        = 0;
                    $shippingMethodId = 0;
                    $products         = [];


                    //Get list of product related|attached to this shipping
                    $links = $this->getRequest()->getPost('links');
                    $links = is_array($links) ? $links : [];
                    if ( ! empty($links) && isset($links['related'])) {
                        $products = $this->jsHelper->decodeGridSerializedInput($links['related']);
                    }

                    //if user chose an existing shipping method => get the $shippingMethodId from the shipping method table
                    $shippingMethodId = $this->calculateShippingMethodId($params['shipping_method']);

                    //Prepare a model to save
                    $shippingModel = $this->_mpshipping->create();

                    //In case of update an existing shipping get the corresponding shipping Id and also get partner_id
                    if (isset($params['lofshipping_id'])) {
                        $id = $params['lofshipping_id'];
                        $shippingModel->load($id);
                        $partnerid = $shippingModel->getData('partner_id');
                    }

                    //Prepare data for both case Adding new shipping and Modifying existing shipping
                    //adding new: lofshipping_id -> not set yet
                    //Modifying existing: lofshipping_id is just loaded in the pevious paragrap
                    if ($params['weight_from'] != "*" && $params['weight_from'] != "") {
                        $params['weight_from'] = (float)$params['weight_from'];
                    }
                    if ($params['weight_to'] != "*" && $params['weight_to'] != "") {
                        $params['weight_to'] = (float)$params['weight_to'];
                    }
                    $tempData = [
                        'shipping_method_id' => $shippingMethodId,
                        'partner_id'         => $partnerid,
                        'dest_country_id'    => $params['dest_country_id'],
                        'dest_region_id'     => $params['dest_region_id'],
                        'dest_zip'           => $params['dest_zip'],
                        'dest_zip_to'        => $params['dest_zip_to'],
                        'price'              => $params['price'],
                        'weight_from'        => $params['weight_from'],
                        'weight_to'          => $params['weight_to'],
                        'quantity_from'      => $params['quantity_from'],
                        'quantity_to'        => $params['quantity_to'],
                        'priority'           => $params['priority'],
                        'cost'               => isset($params['cost'])?(float)$params['cost']:0.0000,
                        'second_price'       => isset($params['second_price'])?(float)$params['second_price']:0.0000,
                        'free_shipping'      => isset($params['free_shipping'])?(float)$params['free_shipping']:0.0000,
                        'allow_second_price'           => isset($params['allow_second_price'])?(int)$params['allow_second_price']:0,
                        'allow_free_shipping'           => isset($params['allow_free_shipping'])?(int)$params['allow_free_shipping']:0,
                        'price_for_unit'           => isset($params['price_for_unit'])?(int)$params['price_for_unit']:1,
                        'description'           => isset($params['description']) && !empty($params['description'])? strip_tags($params['description']):"",
                        'products'           => $products,
                    ];

                    try {
                        $shippingModel->addData($tempData);
                        $shippingModel->save();
                        $this->messageManager->addSuccess(__('You saved this shipping.'));
                        $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                        if ($this->getRequest()->getParam('back')) {
                            return $resultRedirect->setPath('*/*/edit', ['lofshipping_id' => $shippingModel->getId(), '_current' => true]);
                        }

                        return $resultRedirect->setPath('*/*/');
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $this->messageManager->addError($e->getMessage());
                    } catch (\RuntimeException $e) {
                        $this->messageManager->addError($e->getMessage());
                    } catch (\Exception $e) {
                        $this->messageManager->addException($e, __('Something went wrong while saving the shipping.'));
                    }
                    $this->_getSession()->setFormData($data);

                    return $resultRedirect->setPath('*/*/edit', ['lofshipping_id' => $this->getRequest()->getParam('lofshipping_id')]);
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }

    /**
     * Get row value
     *
     * @param mixed $rowData
     * @param mixed|array $headingArray
     * @param string|int $columnName
     * @return mixed|string
     */
    public function getRowValue($rowData, $headingArray, $columnName)
    {
        $rowIndex = isset($headingArray[$columnName]) ? $headingArray[$columnName] : -1;
        return isset($rowData[$rowIndex]) ? $rowData[$rowIndex] : "";
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_ProductShipping::save_shipping');
    }

    /**
     * @param string $shippingMethodName
     * @return int
     */
    public function getShippingIdByName($shippingMethodName)
    {
        $entityId            = 0;
        $shippingMethodModel = $this->_mpshippingMethod->create()
                                                       ->getCollection()
                                                       ->addFieldToFilter('method_name', $shippingMethodName)
                                                       ->getFirstItem();

        if ($shippingMethodModel && $shippingMethodModel->getId()) {
            $entityId = (int)$shippingMethodModel->getId();
        }

        return $entityId;
    }

    /**
     * @param string $methodName
     * @return int
     */
    public function calculateShippingMethodId($methodName)
    {
        $shippingMethodId = $this->getShippingIdByName($methodName);
        if ($shippingMethodId == 0) {
            $mpshippingMethod = $this->_mpshippingMethod->create();
            $mpshippingMethod->setMethodName($methodName);
            $savedMethod      = $mpshippingMethod->save();
            $shippingMethodId = $savedMethod->getId();
        }

        return $shippingMethodId;
    }

    /**
     * Get product ids by skus
     *
     * @param string $skus
     * @return mixed
     */
    public function getProductIdsBySkus($skus = "")
    {
        $productIds = [];
        $tmpArr = explode("|", $skus);
        foreach ($tmpArr as $index => $item) {
            $item                         = explode(":", $item);
            $sku                    = isset($item[0]) && $item[0] ? trim($item[0]) : "" ;
            $position                     = isset($item[1]) ? (int)$item[1] : $index ;
            if ($sku) {
                $product = $this->productRepository->get($sku);
                if ($product && ($productId = $product->getId())) {
                    $productIds[(int)$productId] = ["product_position" => $position];
                }
            }
        }
        return $productIds;
    }
}
