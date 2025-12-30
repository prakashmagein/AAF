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

namespace Aheadworks\RewardPoints\Model\ExtensionAttributes\Builder;

use Magento\Sales\Api\Data\CreditmemoExtension;
use Magento\Sales\Api\Data\CreditmemoExtensionFactory;
use Magento\Sales\Api\Data\CreditmemoItemExtension;
use Magento\Sales\Api\Data\CreditmemoItemExtensionFactory;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\CreditmemoItemInterface;

/**
 * Class Creditmemo
 */
class Creditmemo
{
    /**
     * @var CreditmemoExtensionFactory
     */
    private $creditmemoExtensionFactory;

    /**
     * @var CreditmemoItemExtensionFactory
     */
    private $creditmemoItemExtensionFactory;

    /**
     * @param CreditmemoExtensionFactory $creditmemoExtensionFactory
     * @param CreditmemoItemExtensionFactory $creditmemoItemExtensionFactory
     */
    public function __construct(
        CreditmemoExtensionFactory $creditmemoExtensionFactory,
        CreditmemoItemExtensionFactory $creditmemoItemExtensionFactory
    ) {
        $this->creditmemoExtensionFactory = $creditmemoExtensionFactory;
        $this->creditmemoItemExtensionFactory = $creditmemoItemExtensionFactory;
    }

    /**
     * Attach Reward Points attributes
     *
     * @param CreditmemoInterface $creditmemo
     * @return CreditmemoInterface
     */
    public function attachAttributes(CreditmemoInterface $creditmemo): CreditmemoInterface
    {
        /** @var CreditmemoExtension $creditmemoExtension */
        $creditmemoExtension = $creditmemo->getExtensionAttributes()
            ?: $this->creditmemoExtensionFactory->create();

        $creditmemoExtension
            ->setAwUseRewardPoints($creditmemo->getAwUseRewardPoints())
            ->setAwRewardPointsAmount($creditmemo->getAwRewardPointsAmount())
            ->setBaseAwRewardPointsAmount($creditmemo->getBaseAwRewardPointsAmount())
            ->setAwRewardPoints($creditmemo->getAwRewardPoints())
            ->setAwRewardPointsDescription($creditmemo->getAwRewardPointsDescription())
            ->setBaseAwRewardPointsRefund($creditmemo->getBaseAwRewardPointsRefund())
            ->setAwRewardPointsRefund($creditmemo->getAwRewardPointsRefund())
            ->setAwRewardPointsBlnceRefund($creditmemo->getAwRewardPointsBlnceRefund())
            ->setBaseAwRewardPointsReimbursed($creditmemo->getBaseAwRewardPointsReimbursed())
            ->setAwRewardPointsReimbursed($creditmemo->getAwRewardPointsReimbursed())
            ->setAwRewardPointsBlnceReimbursed($creditmemo->getAwRewardPointsBlnceReimbursed());

        $creditmemo->setExtensionAttributes($creditmemoExtension);

        $items = $creditmemo->getItems() ?: [];
        foreach ($items as $item) {
            $this->attachAttributesToItem($item);
        }

        return $creditmemo;
    }

    /**
     * Attach Reward Points attributes to item
     *
     * @param CreditmemoItemInterface $item
     * @return CreditmemoItemInterface
     */
    public function attachAttributesToItem(CreditmemoItemInterface $item): CreditmemoItemInterface
    {
        /** @var CreditmemoItemExtension $creditmemoItemExtension */
        $creditmemoItemExtension = $item->getExtensionAttributes()
            ?: $this->creditmemoItemExtensionFactory->create();

        $creditmemoItemExtension
            ->setBaseAwRewardPointsAmount($item->getBaseAwRewardPointsAmount())
            ->setAwRewardPointsAmount($item->getAwRewardPointsAmount())
            ->setAwRewardPoints($item->getAwRewardPoints())
            ->setBaseAwRewardPointsRefunded($item->getBaseAwRewardPointsRefunded())
            ->setAwRewardPointsRefunded($item->getAwRewardPointsRefunded())
            ->setAwRewardPointsBlnceRefunded($item->getAwRewardPointsBlnceRefunded())
            ->setBaseAwRewardPointsReimbursed($item->getBaseAwRewardPointsReimbursed())
            ->setAwRewardPointsReimbursed($item->getAwRewardPointsReimbursed())
            ->setAwRewardPointsBlnceReimbursed($item->getAwRewardPointsBlnceReimbursed());

        $item->setExtensionAttributes($creditmemoItemExtension);

        return $item;
    }
}
