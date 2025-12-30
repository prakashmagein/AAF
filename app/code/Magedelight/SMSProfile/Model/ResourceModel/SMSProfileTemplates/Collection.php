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

namespace Magedelight\SMSProfile\Model\ResourceModel\SMSProfileTemplates;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'smsprofiletemplates_collection';
    protected $_eventObject = 'smsprofiletemplates_collection';

    protected function _construct()
    {
        $this->_init(
            '\Magedelight\SMSProfile\Model\SMSProfileTemplates',
            '\Magedelight\SMSProfile\Model\ResourceModel\SMSProfileTemplates'
        );
    }

    protected function _initSelect()
    {
        parent::_initSelect();
    }
}
