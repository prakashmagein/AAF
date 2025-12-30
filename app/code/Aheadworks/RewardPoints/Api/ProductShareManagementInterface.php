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
namespace Aheadworks\RewardPoints\Api;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * @api
 */
interface ProductShareManagementInterface
{
    /**
     * Adds a product share.
     *
     * @param  int $customerId
     * @param  int $productId
     * @param  string $network
     * @return boolean
     * @throws AlreadyExistsException The specified product share already exists.
     * @throws CouldNotSaveException The specified product share not be added.
     */
    public function add($customerId, $productId, $network);
}
