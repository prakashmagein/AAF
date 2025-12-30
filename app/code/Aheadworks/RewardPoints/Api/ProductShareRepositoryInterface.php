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

use Aheadworks\RewardPoints\Api\Data\ProductShareInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface ProductShareRepositoryInterface
{
    /**
     * Retrieve product share by id
     *
     * @param  int $id
     * @return ProductShareInterface
     */
    public function getById($id);

    /**
     * Retrieve product share
     *
     * @param  int $customerId
     * @param  int $productId
     * @param  string $network
     * @return ProductShareInterface
     */
    public function get($customerId, $productId, $network);

    /**
     * Save product share
     *
     * @param  ProductShareInterface $productShare
     * @return ProductShareInterface
     */
    public function save(ProductShareInterface $productShare);

    /**
     * Delete product share by id
     *
     * @param  int $id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Delete product share
     *
     * @param ProductShareInterface $earnRate
     * @return boolean
     */
    public function delete(ProductShareInterface $productShare);
}
