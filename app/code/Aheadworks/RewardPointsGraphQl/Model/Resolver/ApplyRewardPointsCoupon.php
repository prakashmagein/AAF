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
 * @package    RewardPointsGraphQl
 * @version    1.0.0
 * @copyright  Copyright (c) 2024 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RewardPointsGraphQl\Model\Resolver;

use Aheadworks\RewardPoints\Api\CouponManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\Context;

class ApplyRewardPointsCoupon implements ResolverInterface
{
    /**
     * @param CouponManagementInterface $couponManagement
     */
    public function __construct(
        private readonly CouponManagementInterface $couponManagement,
    ) {}

    /**
     * Apply reward points coupon
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return bool
     * @throws GraphQlInputException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $code = (string) ($args['code'] ?? '');
        if (!$code) {
            throw new GraphQlInputException(__('"%1" value must be specified.', 'code'));
        }

        /** @var Context $context */
        $userId = (int) $context->getUserId();
        if (!$userId) {
            throw new GraphQlInputException(__('Customer has to be logged in to perform the operation.'));
        }

        try {
            $this->couponManagement->apply($code, $userId);
        } catch (LocalizedException $exception) {
            throw new GraphQlInputException(__($exception->getMessage()));
        }

        return true;
    }
}
