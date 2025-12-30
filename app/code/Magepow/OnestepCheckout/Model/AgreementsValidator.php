<?php
/**
 * AgreementsValidator
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
namespace Magepow\OnestepCheckout\Model;

use Magepow\OnestepCheckout\Helper\Data;

class AgreementsValidator extends \Magento\CheckoutAgreements\Model\AgreementsValidator
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * AgreementsValidator constructor.
     * @param Data $dataHelper
     * @param null $list
     */
    public function __construct(
        Data $dataHelper,
        $list = null
    )
    {
        parent::__construct($list);
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param array $agreementIds
     * @return bool
     */
    public function isValid($agreementIds = [])
    {
        if (!$this->dataHelper->isEnabledTOC()) {
            return true;
        }

        return parent::isValid($agreementIds);
    }
}
