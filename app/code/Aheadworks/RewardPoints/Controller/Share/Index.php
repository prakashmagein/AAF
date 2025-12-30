<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    2.4.0
 * @copyright  Copyright (c) 2024 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RewardPoints\Controller\Share;

use Aheadworks\RewardPoints\Api\ProductShareManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class Aheadworks\RewardPoints\Controller\Share\Index
 */
class Index extends Action
{
    /**
     * Customer session model
     *
     * @var Session
     */
    private $customerSession;

    /**
     * Product share service
     *
     * @var ProductShareManagementInterface
     */
    private $productShareService;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param ProductShareManagementInterface $productShareService
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ProductShareManagementInterface $productShareService
    ) {
        $this->customerSession = $customerSession;
        $this->productShareService = $productShareService;
        parent::__construct($context);
    }

    /**
     * Execute the controller
     *
     * @return ResultInterface
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     */
    public function execute()
    {
        $customerId = $this->customerSession->getId();
        $productId = $this->getRequest()->getParam('productId');
        $network = $this->getRequest()->getParam('network');

        if (!$this->getRequest()->isAjax() || !$customerId || !$productId) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        }

        if ($this->productShareService->add($customerId, $productId, $network)) {
            $result = ['result' => 'ok'];
        } else {
            $result = ['result' => 'error'];
        }

        /** @var ResultJson $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        return $resultJson->setData($result);
    }
}
