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

namespace Aheadworks\RewardPoints\Model\ResourceModel\ProductShare;

use Aheadworks\RewardPoints\Model\ProductShare;
use Aheadworks\RewardPoints\Model\ResourceModel\ProductShare as ProductShareResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Initialization here
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ProductShare::class, ProductShareResource::class);
    }

    /**
     * Add customer filter
     *
     * @param int|string $customerId
     * @return $this
     */
    public function addCustomerFilter(int|string $customerId): self
    {
        $this->addFieldToFilter('customer_id', ['eq' => $customerId]);
        return $this;
    }
}
