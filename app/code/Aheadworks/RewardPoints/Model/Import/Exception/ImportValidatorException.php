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
namespace Aheadworks\RewardPoints\Model\Import\Exception;

use Aheadworks\RewardPoints\Api\Exception\ImportValidatorExceptionInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ImportValidatorException
 *
 * @package Aheadworks\RewardPoints\Model\Import\Exception
 */
class ImportValidatorException extends LocalizedException implements ImportValidatorExceptionInterface
{
}
