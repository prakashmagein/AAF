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

namespace Aheadworks\RewardPoints\Model\Calculator\Quote\Address;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Aheadworks\RewardPoints\Model\Calculator\Quote\Address\Processor\Composite as Processor;

/**
 * Class Modifier
 */
class Modifier
{
    /**
     * @var Processor
     */
    private $addressProcessor;

    /**
     * @param Processor $addressProcessor
     */
    public function __construct(
        Processor $addressProcessor
    ) {
        $this->addressProcessor = $addressProcessor;
    }

    /**
     * Modify address
     *
     * @param Address $address
     * @param Quote $quote
     * @return Address
     */
    public function modify(Address $address, Quote $quote): Address
    {
        $clonedAddress = clone $address;
        $clonedQuote = clone $clonedAddress->getQuote();
        $clonedAddress->setQuote($clonedQuote);
        $clonedAddress->setCouponCode($quote->getCouponCode());
        $clonedAddress->setTotalQty($quote->getItemsQty());

        $processedData = $this->addressProcessor->process($clonedAddress);
        foreach ($processedData as $key => $value) {
            $clonedAddress->setData($key, $value);
        }
        $clonedAddress->getQuote()->setData('items_collection', $clonedQuote->getItemsCollection());

        return $clonedAddress;
    }
}
