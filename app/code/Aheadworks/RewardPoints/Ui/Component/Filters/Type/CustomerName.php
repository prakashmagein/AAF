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
namespace Aheadworks\RewardPoints\Ui\Component\Filters\Type;

use Magento\Ui\Component\Filters\Type\Select;

/**
 * Class Aheadworks\RewardPoints\Ui\Component\Filters\Type\CustomerName
 */
class CustomerName extends Select
{
    /**
     * @param string $key
     * @return array
     */
    public function getConfig($key = null)
    {
        $config = $this->getData('config');
        if (null != $key && isset($config[$key])) {
            return $config[$key];
        }
        return $config;
    }

    /**
     *  {@inheritDoc}
     */
    protected function applyFilter()
    {
        if (isset($this->filterData[$this->getName()])) {
            $value = $this->filterData[$this->getName()];

            if (!empty($value)) {
                $conditionType = 'like';
                $field = $this->getConfig('index');
                $filter = $this->filterBuilder->setConditionType($conditionType)
                    ->setField($field)
                    ->setValue($value)
                    ->create();

                $this->getContext()->getDataProvider()->addFilter($filter);
            }
        }
    }
}
