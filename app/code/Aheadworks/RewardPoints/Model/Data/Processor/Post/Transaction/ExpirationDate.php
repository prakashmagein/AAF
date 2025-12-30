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

namespace Aheadworks\RewardPoints\Model\Data\Processor\Post\Transaction;

use Aheadworks\RewardPoints\Model\Data\ProcessorInterface;
use Aheadworks\RewardPoints\Model\DateTime;
use Aheadworks\RewardPoints\Model\Source\Transaction\Expire;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ExpirationDate
 */
class ExpirationDate implements ProcessorInterface
{
    const POST_DATA_BALANCE = 'balance';
    const POST_DATA_EXPIRATION_DATE = 'expiration_date';
    const POST_DATA_EXPIRE = 'expire';
    const POST_DATA_EXPIRE_IN_DAYS = 'expire_in_days';

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param DateTime $dateTime
     */
    public function __construct(
        DateTime $dateTime
    ) {
        $this->dateTime = $dateTime;
    }

    /**
     *  Process data
     *
     * @param array|null $data
     * @return array|null
     * @throws \Exception
     */
    public function process($data): ?array
    {
        if ((int)$data[self::POST_DATA_BALANCE] > 0) {
            if (isset($data[self::POST_DATA_EXPIRE]) && $data[self::POST_DATA_EXPIRE] === Expire::EXPIRE_IN_X_DAYS
                && !empty($data[self::POST_DATA_EXPIRE_IN_DAYS])
            ) {
                $date = $this->dateTime->getExpirationDate($data[self::POST_DATA_EXPIRE_IN_DAYS], false);
                ($date === '') ? $data[self::POST_DATA_EXPIRATION_DATE] = null : $data[self::POST_DATA_EXPIRATION_DATE] = $date;

            } elseif (isset($data[self::POST_DATA_EXPIRATION_DATE]) && !empty($data[self::POST_DATA_EXPIRATION_DATE])) {
                if ($this->dateTime->getTodayDate() > $this->dateTime->getDate($data[self::POST_DATA_EXPIRATION_DATE])) {
                    throw new LocalizedException(__('Expiration date cannot be in the past'));
                }
                try {
                    $data[self::POST_DATA_EXPIRATION_DATE] = $this->dateTime->getDate($data[self::POST_DATA_EXPIRATION_DATE], true);
                } catch (\Exception $e) {
                    throw new LocalizedException(__('Invalid input date format %1', $data[self::POST_DATA_EXPIRATION_DATE]));
                }
            }
        } else {
            $data[self::POST_DATA_EXPIRATION_DATE] = null;
        }
        if (empty($data[self::POST_DATA_EXPIRATION_DATE])) {
            $data[self::POST_DATA_EXPIRATION_DATE] = null;
        }

        unset($data[self::POST_DATA_EXPIRE], $data[self::POST_DATA_EXPIRE_IN_DAYS]);

        return $data;
    }
}
