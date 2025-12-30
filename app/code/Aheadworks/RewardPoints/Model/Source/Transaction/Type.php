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

namespace Aheadworks\RewardPoints\Model\Source\Transaction;

use Aheadworks\RewardPoints\Model\ThirdPartyModule\Manager as ThirdPartyModuleManager;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 *
 * @package Aheadworks\RewardPoints\Model\Source
 */
class Type implements OptionSourceInterface
{
    /**#@+
     * Action values
     */
    const BALANCE_ADJUSTED_BY_ADMIN = 1;
    const ORDER_CANCELED = 2;
    const REFUND_BY_REWARD_POINTS = 3;
    const REIMBURSE_OF_SPENT_REWARD_POINTS = 4;
    const POINTS_REWARDED_FOR_REGISTRATION = 5;
    const POINTS_REWARDED_FOR_REVIEW_APPROVED_BY_ADMIN = 6;
    const POINTS_REWARDED_FOR_NEWSLETTER_SIGNUP = 7;
    const POINTS_SPENT_ON_ORDER = 8;
    const POINTS_REWARDED_FOR_ORDER = 9;
    const POINTS_REWARDED_FOR_SHARES = 10;
    const POINTS_EXPIRED = 11;
    const CANCEL_EARNED_POINTS_FOR_REFUND_ORDER = 12;
    const BALANCE_IMPORTED_BY_ADMIN = 13;
    const POINTS_REWARDED_FOR_BIRTHDAY = 14;
    const BALANCE_ADJUSTED_BY_REFER_A_FRIEND = 15;
    const POINTS_REWARDED_FOR_REGISTRATION_REFER_A_FRIEND = 16;
    const POINTS_REWARDED_FOR_COUPON = 17;
    /**#@-*/

    /**
     * @var ThirdPartyModuleManager
     */
    private $thirdPartyModuleManager;

    /**
     * @param ThirdPartyModuleManager $thirdPartyModuleManager
     */
    public function __construct(
        ThirdPartyModuleManager $thirdPartyModuleManager
    ) {
        $this->thirdPartyModuleManager = $thirdPartyModuleManager;
    }

    /**
     * {@inheritDoc}
     */
    public function toOptionArray()
    {
        return array_merge(
            $this->getBalanceUpdateActions(),
            []
        );
    }

    /**
     * Retrieve balance update actions
     *
     * @return array
     */
    public function getBalanceUpdateActions(): array
    {
        $transactionTypes =
        [
            [
                'value' => self::BALANCE_ADJUSTED_BY_ADMIN,
                'label' => __('Balance adjusted by admin')
            ],
            [
                'value' => self::ORDER_CANCELED,
                'label' => __('Order canceled')
            ],
            [
                'value' => self::REFUND_BY_REWARD_POINTS,
                'label' => __('Refund by Reward Points')
            ],
            [
                'value' => self::REIMBURSE_OF_SPENT_REWARD_POINTS,
                'label' => __('Reimburse of spent Reward Points')
            ],
            [
                'value' => self::POINTS_REWARDED_FOR_REGISTRATION,
                'label' => __('Registration')
            ],
            [
                'value' => self::POINTS_REWARDED_FOR_BIRTHDAY,
                'label' => __('Customer birthday')
            ],
            [
                'value' => self::POINTS_REWARDED_FOR_REVIEW_APPROVED_BY_ADMIN,
                'label' => __('Review approved by admin')
            ],
            [
                'value' => self::POINTS_REWARDED_FOR_NEWSLETTER_SIGNUP,
                'label' => __('Newsletter signup')
            ],
            [
                'value' => self::POINTS_SPENT_ON_ORDER,
                'label' => __('Points spent on an order')
            ],
            [
                'value' => self::POINTS_REWARDED_FOR_ORDER,
                'label' => __('Points rewarded for an order')
            ],
            [
                'value' => self::POINTS_REWARDED_FOR_SHARES,
                'label' => __('Points rewarded for product share')
            ],
            [
                'value' => self::POINTS_REWARDED_FOR_COUPON,
                'label' => __('Points rewarded for a coupon')
            ],
            [
                'value' => self::CANCEL_EARNED_POINTS_FOR_REFUND_ORDER,
                'label' => __('Cancel earned points for refund order')
            ],
            [
                'value' => self::POINTS_EXPIRED,
                'label' => __('Points expired')
            ],
            [
                'value' => self::BALANCE_IMPORTED_BY_ADMIN,
                'label' => __('Balance adjusted while import')
            ]
        ];
        if ($this->thirdPartyModuleManager->isRafModuleEnabled()) {
            $rafTransactionTypes =
                [
                    [
                        'value' => self::BALANCE_ADJUSTED_BY_REFER_A_FRIEND,
                        'label' => __('Balance adjusted by Refer a Friend')
                    ],
                    [
                        'value' => self::POINTS_REWARDED_FOR_REGISTRATION_REFER_A_FRIEND,
                        'label' => __('Registration Refer a Friend')
                    ]
                ];
            $transactionTypes = array_merge($transactionTypes, $rafTransactionTypes);
        }

        return $transactionTypes;
    }
}
