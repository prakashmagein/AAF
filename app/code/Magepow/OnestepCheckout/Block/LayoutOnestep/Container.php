<?php
/**
 * Container
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */

namespace Magepow\OnestepCheckout\Block\LayoutOnestep;

use Magento\Framework\View\Element\Template;
use Magepow\OnestepCheckout\Helper\Data;

class Container extends Template
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * Container constructor.
     * @param Template\Context $context
     * @param Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $dataHelper,
        array $data = []
    )
    {
        $this->dataHelper = $dataHelper;

        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getCheckoutDescription()
    {
        return $this->dataHelper->getConfigGeneral('description');
    }
}
