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

namespace Aheadworks\RewardPoints\Model\Indexer\SpendRule;

/**
 * Interface ProductInterface
 */
interface ProductInterface
{
    /**#@+
     * Constants for keys of indexer fields.
     */
    const ID                        = 'id';
    const RULE_ID                   = 'rule_id';
    const FROM_DATE                 = 'from_date';
    const TO_DATE                   = 'to_date';
    const CUSTOMER_GROUP_ID         = 'customer_group_id';
    const WEBSITE_ID                = 'website_id';
    const PRODUCT_ID                = 'product_id';
    const PRIORITY                  = 'priority';
    /**#@-*/
}
