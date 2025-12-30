<?php
/**
 * Data
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
namespace Magepow\OnestepCheckout\Plugin\Eav\Model\Validator\Attribute;

use Magento\Eav\Model\AttributeDataFactory;
use Magepow\OnestepCheckout\Helper\Data as HelperData;

class Data extends \Magento\Eav\Model\Validator\Attribute\Data
{
    /**
     * @var HelperData
     */
    protected $dataHelper;

    /**
     * Data constructor.
     * @param AttributeDataFactory $attrDataFactory
     * @param HelperData $dataHelper
     */
    public function __construct(
        AttributeDataFactory $attrDataFactory,
        HelperData $dataHelper
    )
    {
        parent::__construct($attrDataFactory);
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param \Magento\Eav\Model\Validator\Attribute\Data $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsValid(\Magento\Eav\Model\Validator\Attribute\Data $subject, $result)
    {
        if ($this->dataHelper->isFlagMethodRegister()) {
            $subject->_messages = [];

            return count($subject->_messages) == 0;
        }

        return $result;
    }
}
