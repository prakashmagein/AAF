<?php
/**
 * Magedelight
 * Copyright (C) 2022 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @copyright Copyright (c) 2022 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author    Magedelight <info@magedelight.com>
 */
 
namespace Magedelight\SMSProfile\Model;

use Magento\Framework\Model\AbstractModel;

class SMSProfileOtp extends AbstractModel
{
    const CACHE_TAG = 'smsprofileotp';

    protected $_cacheTag = 'smsprofileotp';
    
    protected $_eventPrefix = 'smsprofileotp';

    protected function _construct()
    {
        $this->_init('Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtp');
    }
    
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }
}
