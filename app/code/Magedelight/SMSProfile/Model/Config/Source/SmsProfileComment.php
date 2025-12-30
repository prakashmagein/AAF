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

namespace Magedelight\SmsProfile\Model\Config\Source;

use Magento\Config\Model\Config\CommentInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class SmsProfileComment implements CommentInterface
{
    public $_storeManager;

    /**
     * SmsProfileComment constructor.
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->_storeManager=$storeManager;
    }

    /**
     * Retrieve element comment by element value
     * @param string $elementValue
     * @return string
     */
    public function getCommentText($elementValue)
    {
        //do some calculations here
        $storeurl = $this->_storeManager->getStore()
           ->getBaseUrl(UrlInterface::URL_TYPE_LINK);

        return __('If your service provider sends delivery report through the webhook for SMS which are processed and above 4 fields are not mandatory then specify url (eg.: "http://yourdomain.com/smsprofile/pushurl/index") in your service provider\'s account.');
    }
}
