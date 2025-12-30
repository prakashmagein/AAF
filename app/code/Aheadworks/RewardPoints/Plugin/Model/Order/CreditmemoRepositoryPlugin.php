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

namespace Aheadworks\RewardPoints\Plugin\Model\Order;

use Aheadworks\RewardPoints\Model\ExtensionAttributes\Builder\Creditmemo as CreditmemoExtensionAttributesBuilder;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\CreditmemoSearchResultInterface;
use Magento\Sales\Api\CreditmemoRepositoryInterface;

/**
 * Class CreditmemoRepositoryPlugin
 */
class CreditmemoRepositoryPlugin
{
    /**
     * @var CreditmemoExtensionAttributesBuilder
     */
    private $creditmemoExtensionAttributesBuilder;

    /**
     * @param CreditmemoExtensionAttributesBuilder $creditmemoExtensionAttributesBuilder
     */
    public function __construct(
        CreditmemoExtensionAttributesBuilder $creditmemoExtensionAttributesBuilder
    ) {
        $this->creditmemoExtensionAttributesBuilder = $creditmemoExtensionAttributesBuilder;
    }

    /**
     * Add data to creditmemo item object
     *
     * @param CreditmemoRepositoryInterface $subject
     * @param CreditmemoSearchResultInterface $result
     * @return CreditmemoSearchResultInterface
     */
    public function afterGetList(CreditmemoRepositoryInterface $subject, CreditmemoSearchResultInterface $result): CreditmemoSearchResultInterface
    {
        foreach ($result->getItems() as $item) {
            $this->creditmemoExtensionAttributesBuilder->attachAttributes($item);
        }

        return $result;
    }

    /**
     * Add data to creditmemo object
     *
     * @param CreditmemoRepositoryInterface $subject
     * @param CreditmemoInterface $item
     * @return CreditmemoInterface
     */
    public function afterGet(CreditmemoRepositoryInterface $subject, CreditmemoInterface $item): CreditmemoInterface
    {
        $this->creditmemoExtensionAttributesBuilder->attachAttributes($item);

        return $item;
    }
}
