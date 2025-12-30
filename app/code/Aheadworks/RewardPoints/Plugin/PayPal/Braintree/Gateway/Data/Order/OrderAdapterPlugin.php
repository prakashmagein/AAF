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

namespace Aheadworks\RewardPoints\Plugin\Paypal\Braintree\Gateway\Data\Order;

use Magento\Framework\Exception\NoSuchEntityException;
use PayPal\Braintree\Gateway\Data\Order\OrderAdapter as Subject;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class OrderAdapterPlugin
 */
class OrderAdapterPlugin
{
    /**
     * OrderAdapterPlugin constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(private CartRepositoryInterface $quoteRepository)
    {
    }

    /**
     * Get base discount amount + aw base reward points amount
     *
     * @return float|null
     */
    public function afterGetBaseDiscountAmount(Subject $subject, $result): ?float
    {
        try {
            $quoteId = $subject->getQuoteId();
            $quote = $this->quoteRepository->get($quoteId);
            if ((bool)$quote->getAwUseRewardPoints()) {
                $result = (float)$result + (float)$quote->getBaseAwRewardPointsAmount();
            }
        } catch (NoSuchEntityException $ex) {
            return $result;
        }

        return $result;
    }
}
