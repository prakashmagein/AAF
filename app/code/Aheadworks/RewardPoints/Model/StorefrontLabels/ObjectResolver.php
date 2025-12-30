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
namespace Aheadworks\RewardPoints\Model\StorefrontLabels;

use Aheadworks\RewardPoints\Api\Data\StorefrontLabelsInterface;
use Aheadworks\RewardPoints\Api\Data\StorefrontLabelsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class ObjectResolver
 *
 * @package Aheadworks\RewardPoints\Model\StorefrontLabels
 */
class ObjectResolver
{
    /**
     * @var StorefrontLabelsInterfaceFactory
     */
    private $storefrontLabelsFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param StorefrontLabelsInterfaceFactory $storefrontLabelsFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        StorefrontLabelsInterfaceFactory $storefrontLabelsFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->storefrontLabelsFactory = $storefrontLabelsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Resolve row label
     *
     * @param StorefrontLabelsInterface[]|array $label
     * @return StorefrontLabelsInterface
     */
    public function resolve($label)
    {
        if ($label instanceof StorefrontLabelsInterface) {
            $labelObject = $label;
        } else {
            /** @var StorefrontLabelsInterface $labelObject */
            $labelObject = $this->storefrontLabelsFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $labelObject,
                $label,
                StorefrontLabelsInterface::class
            );
        }
        return $labelObject;
    }
}
