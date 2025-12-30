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

namespace Aheadworks\RewardPoints\Controller\Adminhtml\Coupons\Generate;

use Aheadworks\RewardPoints\Api\Data\CouponGenerateInfoInterfaceFactory;
use Aheadworks\RewardPoints\Model\Coupon\Generate\Validator\ValidatorInterface;
use Aheadworks\RewardPoints\Model\CouponGenerateInfo;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\MessageQueue\PublisherInterface;

class Generate extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Aheadworks_RewardPoints::aw_reward_points_coupons';

    /**
     * Topic name for queue publisher
     */
    private const TOPIC_NAME = 'aw_reward_points.coupons_generate';

    /**
     * @param Context $context
     * @param PublisherInterface $publisher
     * @param CouponGenerateInfoInterfaceFactory $couponGenerateInfoFactory
     * @param ValidatorInterface[] $validators
     */
    public function __construct(
        Context $context,
        private readonly PublisherInterface $publisher,
        private readonly CouponGenerateInfoInterfaceFactory $couponGenerateInfoFactory,
        private readonly array $validators
    ) {
        parent::__construct($context);
    }

    /**
     * Generate coupons
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var CouponGenerateInfo $couponGenerateInfo */
        $couponGenerateInfo = $this->couponGenerateInfoFactory->create();
        $couponGenerateInfo->setData($this->getRequest()->getParams());

        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            foreach ($this->validators as $validator) {
                $validator->validate($couponGenerateInfo);
            }

            $this->publisher->publish(self::TOPIC_NAME, $couponGenerateInfo);
        } catch (LocalizedException $exception) {
            $this->getMessageManager()->addErrorMessage(
                $exception->getMessage()
            );

            return $resultRedirect->setPath('*/*');
        }

        $this->getMessageManager()->addSuccessMessage(
            __('The coupon generation has been added to queue and will be performed soon.')->render()
        );

        return $resultRedirect->setPath('*/coupons');
    }
}
