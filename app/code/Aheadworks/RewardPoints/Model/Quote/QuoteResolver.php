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

namespace Aheadworks\RewardPoints\Model\Quote;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteFactory;

/**
 * Class QuoteResolver
 */
class QuoteResolver
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param QuoteFactory $quoteFactory
     */
    public function __construct(CartRepositoryInterface $cartRepository, QuoteFactory $quoteFactory)
    {
        $this->cartRepository = $cartRepository;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Get quote
     *
     * @param int|null $quoteId
     * @return CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuote(?int $quoteId): CartInterface
    {
        if ($quoteId) {
            try {
                return $this->cartRepository->get($quoteId);
            } catch (\Exception $e) {
                return $this->quoteFactory->create();
            }
        }

        return $this->quoteFactory->create();
    }
}
