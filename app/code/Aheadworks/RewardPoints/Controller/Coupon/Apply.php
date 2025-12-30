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
declare(strict_types=1);

namespace Aheadworks\RewardPoints\Controller\Coupon;

use Aheadworks\RewardPoints\Api\CouponManagementInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory as ResultRedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

class Apply implements HttpPostActionInterface
{
    /**
     * @param CustomerSession $customerSession
     * @param RequestInterface $request
     * @param ResultRedirectFactory $resultRedirectFactory
     * @param MessageManagerInterface $messageManager
     * @param CouponManagementInterface $couponManagement
     */
    public function __construct(
        private readonly CustomerSession $customerSession,
        private readonly RequestInterface $request,
        private readonly ResultRedirectFactory $resultRedirectFactory,
        private readonly MessageManagerInterface $messageManager,
        private readonly CouponManagementInterface $couponManagement
    ) {}

    /**
     * Apply coupon action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $couponCode = $this->request->getParam('coupon');
        $customerId = (int) $this->customerSession->getCustomerId();

        if ($couponCode && $customerId) {
            try {
                $this->couponManagement->apply($couponCode, $customerId);

                $this->messageManager->addSuccessMessage(
                    __('Coupon "%1" has been successfully activated.', $couponCode)->render()
                );
            } catch (NoSuchEntityException) {
                $this->messageManager->addErrorMessage(
                    __('Coupon "%1" does not exist.', $couponCode)->render()
                );
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage(
                    $exception->getMessage()
                );
            }
        }

        return $this->resultRedirectFactory->create()->setPath('*/info');
    }
}
