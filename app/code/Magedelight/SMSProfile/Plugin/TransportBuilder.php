<?php

/**
 * Magedelight
 * Copyright (C) 2022 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_SMSProfile
 * @copyright Copyright (c) 2022 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\SMSProfile\Plugin;

use Magento\Framework\Registry;

class TransportBuilder
{

     /** @var Registry */
    private $registry;
    
    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    public function beforeSetTemplateVars(\Magento\Framework\Mail\Template\TransportBuilder $subject, $templateVars)
    {
        if ($this->registry->registry("smsvar")!='') {
            $this->registry->unregister("smsvar");
        }
        $this->registry->register("smsvar", $templateVars);
        return [$templateVars];
    }

    public function beforeAddTo(\Magento\Framework\Mail\Template\TransportBuilder $subject, $address, $name = '')
    {
        if ($this->registry->registry("emailaddress")!='') {
            $this->registry->unregister("emailaddress");
        }
        $this->registry->register("emailaddress", $address);
        return [$address,$name];
    }
}
