<?php
/**
 * FeeInterface
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */

namespace Magepow\OnestepCheckout\Api\Data;


interface FeeInterface
{
    const ENTITY_ID = 'id';
    const ORDER_ID = 'order_id';
    const QUOTE_ID = 'quote_id';
    const AMOUNT = 'amount';
    const BASE_AMOUNT = 'base_amount';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return int|null
     */
    public function getOrderId();

    /**
     * @return int|null
     */
    public function getQuoteId();

    /**
     * @return int
     */
    public function getAmount();

    /**
     * @return int
     */
    public function getBaseAmount();

    /**
     * @param int $id
     * @return \Magepow\OnestepCheckout\Api\Data\FeeInterface
     */
    public function setOrderId($id);

    /**
     * @param int $id
     * @return \Magepow\OnestepCheckout\Api\Data\FeeInterface
     */
    public function setQuoteId($id);

    /**
     * @param int $amount
     * @return \Magepow\OnestepCheckout\Api\Data\FeeInterface
     */
    public function setAmount($amount);

    /**
     * @param int $amount
     * @return \Magepow\OnestepCheckout\Api\Data\FeeInterface
     */
    public function setBaseAmount($amount);
}
