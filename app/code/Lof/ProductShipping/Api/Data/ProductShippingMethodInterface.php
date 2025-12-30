<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ProductShipping
 * @copyright  Copyright (c) 2022 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\ProductShipping\Api\Data;

interface ProductShippingMethodInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENTITY_ID = 'entity_id';

    const METHOD_NAME = 'method_name';

    const PARTNER_ID = 'partner_id';

    /**#@-*/

    /**
     * Get Entity ID
     *
     * @return int|null
     */
    public function getEntityId();

    /**
     * Set Entity ID
     *
     * @param int $id
     * @return \Lof\ProductShipping\Api\Data\ProductShippingMethodInterface
     */
    public function setEntityId($id);

    /**
     * Get partner_id ID
     *
     * @return int|null
     */
    public function getPartnerId();

    /**
     * Set partner_id ID
     *
     * @param int $partner_id
     * @return \Lof\ProductShipping\Api\Data\ProductShippingMethodInterface
     */
    public function setPartnerId($partner_id);

    /**
     * Get method_name
     *
     * @return string
     */
    public function getMethodName();

    /**
     * Set method_name
     *
     * @param string $method_name
     * @return \Lof\ProductShipping\Api\Data\ProductShippingMethodInterface
     */
    public function setMethodName($method_name);
}
