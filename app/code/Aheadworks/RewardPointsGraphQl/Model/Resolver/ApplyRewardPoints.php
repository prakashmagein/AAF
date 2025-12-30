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

use Aheadworks\RewardPoints\Api\RewardPointsCartManagementInterface;
use Aheadworks\RewardPointsGraphQl\Model\CartManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ApplyRewardPoints implements ResolverInterface
{
    /**
     * @param RewardPointsCartManagementInterface $rewardPointsCartManagement
     * @param CartManagement $cartManagement
     */
    public function __construct(
        private readonly RewardPointsCartManagementInterface $rewardPointsCartManagement,
        private readonly CartManagement $cartManagement
    ) {}

    /**
     * Apply reward points
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
        $quantity = (int) ($args['quantity'] ?? 0);
        if (!$quantity) {
            throw new GraphQlInputException(__('"%1" value must be specified.', 'quantity'));
        }

        try {
            $cart = $this->cartManagement->getCart();
            $this->rewardPointsCartManagement->set($cart->getId(), $quantity);
        } catch (LocalizedException $exception) {
            throw new GraphQlInputException(__($exception->getMessage()));
        }

        return true;
    }
}
