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

namespace Aheadworks\RewardPoints\Controller\Adminhtml\Coupons;

use Aheadworks\RewardPoints\Model\Coupon\MassAction\MassActionInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\Coupon\CollectionFactory as CouponCollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter as MassActionFilter;

class MassAction extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Aheadworks_RewardPoints::aw_reward_points_coupons';

    /**
     * @param Context $context
     * @param MassActionFilter $massActionFilter
     * @param CouponCollectionFactory $couponCollectionFactory
     * @param MassActionInterface[] $massActions
     */
    public function __construct(
        Context $context,
        private readonly MassActionFilter $massActionFilter,
        private readonly CouponCollectionFactory $couponCollectionFactory,
        private readonly array $massActions
    ) {
        parent::__construct($context);
    }

    /**
     * Perform massive action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $actionType = $this->getRequest()->getParam('type');
        $massAction = $this->massActions[$actionType] ?? null;

        if ($massAction) {
            try {
                $couponCollection = $this->couponCollectionFactory->create();
                $this->massActionFilter->getCollection($couponCollection);

                $this->getMessageManager()->addSuccessMessage(
                    $massAction->execute($couponCollection)
                );
            } catch (LocalizedException $exception) {
                $this->getMessageManager()->addErrorMessage(
                    $exception->getMessage()
                );
            }
        }

        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
