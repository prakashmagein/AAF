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

namespace Aheadworks\RewardPoints\Model\Total\Invoice;

use Aheadworks\RewardPoints\Model\Config;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Aheadworks\RewardPoints\Model\Calculator\RateCalculator;
use Magento\Sales\Model\Order;

/**
 * Class RewardPoints
 */
class RewardPoints extends AbstractTotal
{
    /**
     * @param RateCalculator $rateCalculator
     * @param Config $config
     */
    public function __construct(
        private readonly RateCalculator $rateCalculator,
        private readonly Config $config
    ) {
    }

    /**
     * Collect invoice subtotal
     *
     * @param Invoice $invoice
     * @return $this
     */
    public function collect(Invoice $invoice)
    {
        $invoice->setAwUseRewardPoints(false);
        $invoice->setAwRewardPointsAmount(0);
        $invoice->setBaseAwRewardPointsAmount(0);
        $invoice->setAwRewardPoints(0);
        $invoice->setAwRewardPointsDescription('');

        $totalPointsAmount = 0;
        $baseTotalPointsAmount = 0;

        $order = $invoice->getOrder();
        $customerId = $order->getCustomerId();
        $websiteId = (int)$order->getStore()->getWebsiteId();
        if ($order->getBaseAwRewardPointsAmount()
            && $order->getBaseAwRewardPointsInvoiced() != $order->getBaseAwRewardPointsAmount()
        ) {
            // Checking if Reward Points shipping amount was added in previous invoices
            $addRewardPointsShippingAmount = true;
            foreach ($order->getInvoiceCollection() as $previousInvoice) {
                if ($previousInvoice->getAwRewardPointsAmount()) {
                    $addRewardPointsShippingAmount = false;
                }
            }

            if ($addRewardPointsShippingAmount) {
                $totalPointsAmount = $totalPointsAmount + $order->getAwRewardPointsShippingAmount();
                $baseTotalPointsAmount = $baseTotalPointsAmount + $order->getBaseAwRewardPointsShippingAmount();
            }

            $isTaxPointsApplied = $this->isTaxPointsApplied($order, $websiteId);

            /** @var $item \Magento\Sales\Model\Order\Invoice\Item */
            foreach ($invoice->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                    continue;
                }

                $orderItemPointsAmount = (double)$orderItem->getAwRewardPointsAmount();
                $baseOrderItemPointsAmount = (double)$orderItem->getBaseAwRewardPointsAmount();
                $orderItemQty = $orderItem->getQtyOrdered();

                if ($orderItemPointsAmount && $orderItemQty) {
                    // Resolve rounding problems
                    $pointsAmount = $orderItemPointsAmount - $orderItem->getAwRewardPointsInvoiced();
                    $basePointsAmount = $baseOrderItemPointsAmount - $orderItem->getBaseAwRewardPointsInvoiced();

                    if ($isTaxPointsApplied && $this->config->isApplyingPointsToTax($websiteId)) {
                        $pointsAmount += $item->getTaxAmount();
                        $basePointsAmount += $item->getBaseTaxAmount();
                    }

                    if (!$item->isLast()) {
                        $activeQty = $orderItemQty - $orderItem->getQtyInvoiced();
                        $pointsAmount = $invoice->roundPrice(
                            $pointsAmount / $activeQty * $item->getQty(),
                            'regular',
                            true
                        );
                        $basePointsAmount = $invoice->roundPrice(
                            $basePointsAmount / $activeQty * $item->getQty(),
                            'base',
                            true
                        );
                    }

                    $item->setAwRewardPointsAmount($pointsAmount);
                    $item->setBaseAwRewardPointsAmount($basePointsAmount);

                    $orderItem->setAwRewardPointsInvoiced(
                        $orderItem->getAwRewardPointsInvoiced() + $item->getAwRewardPointsAmount()
                    );
                    $orderItem->setBaseAwRewardPointsInvoiced(
                        $orderItem->getBaseAwRewardPointsInvoiced() + $item->getBaseAwRewardPointsAmount()
                    );

                    $totalPointsAmount += $pointsAmount;
                    $baseTotalPointsAmount += $basePointsAmount;
                }
            }

            if ($isTaxPointsApplied && $this->config->isApplyingPointsToShipping($websiteId) &&
                $this->config->isApplyingPointsToTax($websiteId)) {
                $totalPointsAmount += $order->getShippingTaxAmount();
                $baseTotalPointsAmount += $order->getBaseShippingTaxAmount();
            }

            $usedPoints = $this->rateCalculator->calculateSpendPoints(
                $customerId,
                $baseTotalPointsAmount,
                $websiteId,
                $order->getAwRewardPoints() - $order->getAwRewardPointsBlnceInvoiced()
            );

            $usedPoints = $usedPoints > $order->getAwRewardPoints()
                ? $order->getAwRewardPoints()
                : $usedPoints;

            if ($usedPoints > 0) {
                $invoice->setAwUseRewardPoints($order->getAwUseRewardPoints());
                $invoice->setAwRewardPoints($usedPoints);
                $invoice->setAwRewardPointsDescription(__('%1 %2',
                        $usedPoints,
                        $this->config->getLabelNameRewardPoints($websiteId)
                    )
                );
                $invoice->setBaseAwRewardPointsAmount(-$baseTotalPointsAmount);
                $invoice->setAwRewardPointsAmount(-$totalPointsAmount);
                $order->setAwRewardPointsBlnceInvoiced($order->getAwRewardPointsBlnceInvoiced() + $usedPoints);

                $availablePoints = $usedPoints;
                /** @var $item \Magento\Sales\Model\Order\Invoice\Item */
                foreach ($invoice->getAllItems() as $item) {
                    $orderItem = $item->getOrderItem();
                    if ($orderItem->isDummy()) {
                        continue;
                    }

                    $rewardPoints = $this->rateCalculator->calculateSpendPoints(
                        $customerId,
                        $item->getBaseAwRewardPointsAmount(),
                        $websiteId,
                        $orderItem->getAwRewardPoints() - $orderItem->getAwRewardPointsBlnceInvoiced()
                    );

                    $rewardPoints = $rewardPoints > $availablePoints
                        ? $availablePoints
                        : $rewardPoints;

                    $item->setAwRewardPoints($rewardPoints);
                    $orderItem->setAwRewardPointsBlnceInvoiced(
                        $orderItem->getAwRewardPointsBlnceInvoiced() + $item->getAwRewardPoints()
                    );
                    $availablePoints -= $rewardPoints;
                }
            }

            $invoice->setGrandTotal($invoice->getGrandTotal() - $totalPointsAmount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalPointsAmount);
        }
        return $this;
    }

    /**
     * Is tax points applied for order (need to check if added tax amount points = 0)
     *
     * @param Order $order
     * @param int $websiteId
     * @return bool
     */
    private function isTaxPointsApplied(Order $order, int $websiteId): bool
    {
        $result = true;
        $totalPointsAmount = (float)$order->getBaseAwRewardPointsShippingAmount();
        if ($this->config->isApplyingPointsToShipping($websiteId) && $this->config->isApplyingPointsToTax($websiteId)) {
            $totalPointsAmount += (float)$order->getBaseShippingTaxAmount();
        }

        foreach ($order->getItems() as $orderItem) {
            if ($orderItem->isDummy()) {
                continue;
            }

            $orderItemPointsAmount = (float)$orderItem->getBaseAwRewardPointsAmount();
            $orderItemQty = $orderItem->getQtyOrdered();

            if ($orderItemPointsAmount && $orderItemQty) {
                if ($this->config->isApplyingPointsToTax($websiteId)) {
                    $orderItemPointsAmount += $orderItem->getBaseTaxAmount();
                }
                $totalPointsAmount += $orderItemPointsAmount;
            }
        }

        $orderPointsAmount = abs((float)$order->getBaseAwRewardPointsAmount());
        if ($orderPointsAmount < $totalPointsAmount) {
            $result = false;
        }

        return $result;
    }
}
