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

namespace Aheadworks\RewardPointsGraphQl\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\Data\CartInterface;

class CartManagement
{
    /**
     * @param UserContextInterface $userContext
     * @param CartManagementInterface $cartManagement
     */
    public function __construct(
        private readonly UserContextInterface $userContext,
        private readonly CartManagementInterface $cartManagement
    ) {}

    /**
     * Retrieve cart for current customer
     *
     * @return CartInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCart(): CartInterface
    {
        $userId = $this->userContext->getUserId();
        if (!$userId) {
            throw new LocalizedException(__('Customer has to be logged in to perform the operation.'));
        }

        try {
            $cart = $this->cartManagement->getCartForCustomer($userId);
        } catch (NoSuchEntityException) {
            throw new NoSuchEntityException(__('Current customer does not have an active cart.'));
        }

        return $cart;
    }
}
