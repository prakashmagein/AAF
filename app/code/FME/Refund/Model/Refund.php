<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Hassan <support@fmeextensions.com>
 * @package   FME_Refund
 * @copyright Copyright (c) 2021 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */


namespace FME\Refund\Model;

use Magento\Framework\Model\AbstractModel;

class Refund extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\FME\Refund\Model\ResourceModel\Refund::class);
    }
}
