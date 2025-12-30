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

namespace Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\Result;

use Magento\Framework\Api\AbstractSimpleObject;

class Item extends AbstractSimpleObject
{
    /**#@+
     * Constants for keys.
     */
    public const PRODUCT_ID    = 'product_id';
    public const WEBSITE_IDS   = 'website_ids';
    /**#@-*/

    /**
     * Get product id
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->_get(self::PRODUCT_ID);
    }

    /**
     * Set product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Get website ids
     *
     * @return int[]
     */
    public function getWebsiteIds()
    {
        return $this->_get(self::WEBSITE_IDS);
    }

    /**
     * Set website ids
     *
     * @param int[] $websiteIds
     * @return $this
     */
    public function setWebsiteIds($websiteIds)
    {
        return $this->setData(self::WEBSITE_IDS, $websiteIds);
    }
}
