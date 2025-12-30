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
 * @package    Lof_TableRateShipping
 * @copyright  Copyright (c) 2022 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\ProductShipping\Controller\Adminhtml\Shipping;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class MassDelete
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * Field id
     */
    const ID_FIELD = 'lofshipping_id';

    /**
     * Resource collection
     *
     * @var string
     */
    protected $_collection = 'Lof\ProductShipping\Model\ResourceModel\Shipping\Collection';

    /**
     * Page model
     *
     * @var string
     */
    protected $_model = 'Lof\ProductShipping\Model\Shipping';

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam("lofshipping_id");
        if ( ! empty($id)) {
            try {
                $model = $this->_objectManager->get($this->_model);
                $model->load($id);
                $model->delete();
                //$this->messageManager->addMessage("Deletion is completed.");
                $this->messageManager->addSuccess(__('Deletion is completed.'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        } else {
            $this->messageManager->addError(__('Please select item(s).'));
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('lofmpproductshipping/*/index');
    }

    /**
     * Delete selected items
     *
     * @param array $selected
     * @return void
     * @throws \Exception
     */
    protected function selectedDelete(array $selected)
    {
        /** @var AbstractCollection $collection */
        $collection = $this->_objectManager->get($this->_collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['in' => $selected]);
        $this->setSuccessMessage($this->delete($collection));
    }

    /**
     * Delete collection items
     *
     * @param AbstractCollection $collection
     * @return int
     */
    protected function delete(AbstractCollection $collection)
    {
        $count = 0;
        foreach ($collection->getAllIds() as $id) {
            $model = $this->_objectManager->get($this->_model);
            $model->load($id);
            $model->delete();
            ++$count;
        }

        return $count;
    }

    /**
     * Set error messages
     *
     * @param int $count
     * @return void
     */
    protected function setSuccessMessage($count)
    {
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $count));
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_ProductShipping::delete_shipping');
    }
}
