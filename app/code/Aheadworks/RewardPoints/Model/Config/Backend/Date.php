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
namespace Aheadworks\RewardPoints\Model\Config\Backend;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Stdlib\DateTime;

/**
 * Class Date
 * @package Aheadworks\RewardPoints\Model\Config\Backend
 */
class Date extends Value
{
    /**
     * @inheritDoc
     */
    public function beforeSave()
    {
        $value = $this->getValue();

        if (!empty($value) && !$this->isDate($value)) {
            throw new LocalizedException(__('Lifetime Sales Start Date is invalid. Enter valid date.'));
        }
        return parent::beforeSave();
    }

    /**
     * Check is date
     *
     * @param string $date
     * @return bool
     */
    private function isDate($date)
    {
        $dateNew = date(DateTime::DATE_PHP_FORMAT, strtotime($date));

        return $date === $dateNew;
    }
}
