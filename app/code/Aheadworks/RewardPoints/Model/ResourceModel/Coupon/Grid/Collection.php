<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    2.4.0
 * @copyright  Copyright (c) 2024 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RewardPoints\Model\ResourceModel\Coupon\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    /**
     * @var array
     */
    protected $_map = [
        'fields' => [
            'customer_email' => 'customer.email',
            'customer_name' => 'customer.name',
            'coupon_created_at' => 'main_table.created_at',
            'coupon_updated_at' => 'main_table.updated_at'
        ]
    ];

    /**
     * Prepare select for query
     *
     * @return void
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()
            ->joinLeft(
                ['customer' => $this->getTable('customer_grid_flat')],
                'main_table.customer_id = customer.entity_id',
                []
            )
            ->columns($this->_map['fields']);
    }
}
