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
namespace Aheadworks\RewardPoints\Model\Data;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * Interface CommandInterface
 *
 * @package Aheadworks\RewardPoints\Model\Data
 */
interface CommandInterface
{
    /**
     * Execute command
     *
     * @param array $data
     * @return DataObject|bool
     * @throws LocalizedException
     */
    public function execute($data);
}
