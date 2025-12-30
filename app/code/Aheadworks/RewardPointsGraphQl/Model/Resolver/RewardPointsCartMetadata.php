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

use Aheadworks\RewardPoints\Api\Data\CustomerCartMetadataInterface;
use Aheadworks\RewardPoints\Api\RewardPointsCartManagementInterface;
use Aheadworks\RewardPointsGraphQl\Model\CartManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class RewardPointsCartMetadata implements ResolverInterface
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
     * Reward points cart metadata
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return CustomerCartMetadataInterface
     * @throws GraphQlInputException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        try {
            $cart = $this->cartManagement->getCart();

            $cartMetadata = $this->rewardPointsCartManagement->getCustomerCartMetadata(
                $cart->getCustomer()->getId(),
                $cart->getId()
            );
        } catch (LocalizedException $exception) {
            throw new GraphQlInputException(__($exception->getMessage()));
        }

        return $cartMetadata;
    }
}
