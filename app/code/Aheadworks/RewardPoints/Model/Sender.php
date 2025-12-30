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

namespace Aheadworks\RewardPoints\Model;

use Aheadworks\RewardPoints\Model\Comment\CommentDefault;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Area;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\RewardPoints\Model\Source\NotifiedStatus;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Sender
{
    /**
     * @param Config $config
     * @param TransportBuilder $transportBuilder
     * @param StoreRepositoryInterface $storeRepository
     * @param KeyEncryptor $keyEncryptor
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        private readonly Config $config,
        private readonly TransportBuilder $transportBuilder,
        private readonly StoreRepositoryInterface $storeRepository,
        private readonly KeyEncryptor $keyEncryptor,
        private readonly TimezoneInterface $localeDate
    ) {
    }

    /**
     * Send email notification to recipient
     *
     * @param CustomerInterface $customer
     * @param string $comment
     * @param int $points
     * @param int $pointsBalance
     * @param string $moneyBalance
     * @param string $expireDate
     * @param int $storeId
     * @param string $template
     * @return int
     * @throws NoSuchEntityException
     */
    public function sendNotification(
        $customer,
        $comment,
        $points,
        $pointsBalance,
        $moneyBalance,
        $expireDate,
        $storeId,
        $template
    ) {
        /** @var \Magento\Store\Api\Data\StoreInterface $store */
        $store = $this->storeRepository->getById($storeId);
        $sender = $this->config->getEmailSender($store->getWebsiteId());
        $senderName = $this->config->getEmailSenderName($store->getWebsiteId());
        $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
        $label = $this->config->getLabelNameRewardPoints((int)$store->getWebsiteId());
        $comment = $this->replaceComment($comment, $label);

        $notifiedStatus = $this->send(
            $template,
            [
                'area' => Area::AREA_FRONTEND,
                'store' => $store->getId()
            ],
            $this->prepareTemplateVars(
                [
                    'store' => $store,
                    'customer' => $customer,
                    'sender_name' => $senderName,
                    'customer_name' => $customerName,
                    'comment' => $comment,
                    'points' => $points,
                    'expire_date' => $expireDate,
                    'points_balance' => $pointsBalance,
                    'money_balance' => $moneyBalance,
                    'reward_points_label_name' => $label,
                    'points_label_name' => $label !== Config::DEFAULT_LABEL_NAME ?  $label : Config::DEFAULT_POINTS_NAME
                ]
            ),
            $sender,
            $customer->getEmail(),
            $customerName
        );
        return $notifiedStatus;
    }

    /**
     * Send email
     *
     * @param string $templateId
     * @param array $templateOptions
     * @param array $templateVars
     * @param string $from
     * @param string $recipientEmail
     * @param string $recipientName
     * @return int
     */
    private function send(
        $templateId,
        array $templateOptions,
        array $templateVars,
        $from,
        $recipientEmail,
        $recipientName
    ) {
        try {
            $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($recipientEmail, $recipientName);
            $this->transportBuilder->getTransport()->sendMessage();
        } catch (\Exception $e) {
            return NotifiedStatus::NO;
        }

        return NotifiedStatus::YES;
    }

    /**
     * Prepare template vars
     *
     * @param array $data
     * @return array
     * @throws NoSuchEntityException
     */
    private function prepareTemplateVars(array $data): array
    {
        /** @var $store \Magento\Store\Model\Store */
        $store = $data['store'];
        $customer = $data['customer'];
        $unsubscribeKey = $this->keyEncryptor->encrypt(
            $customer->getEmail(),
            (int)$customer->getId(),
            (int)$store->getWebsiteId()
        );
        $templateVars = [
            'rp_program_url' => $store->getBaseUrl() . $this->config->getFrontendExplainerPage($store->getId()),
            'unsubscribe_url' => $store->getBaseUrl() . 'aw_rewardpoints/unsubscribe/index/key/' . $unsubscribeKey,
            'store_name' => $store->getFrontendName(),
            'store_url' => $store->getBaseUrl(),
            'reward_points_label_name' => $data['reward_points_label_name'] ?? Config::DEFAULT_LABEL_NAME,
            'points_label_name' => $data['points_label_name'] ?? Config::DEFAULT_POINTS_NAME,
        ];

        if (isset($data['expire_date'])) {
            $now = new \DateTime('now', new \DateTimeZone('UTC'));
            $expireDate = new \DateTime($data['expire_date'], new \DateTimeZone('UTC'));
            $expireInDays = $now->diff($expireDate);

            $templateVars['expire_date'] = $this->localeDate
                ->scopeDate($store, $data['expire_date'], true)
                ->format('d M Y');

            if (($expireInDays = $expireInDays->format('%a')) > 0) {
                $templateVars['expire_in_days'] = $expireInDays;
            }
        }

        if (isset($data['sender_name'])) {
            $templateVars['sender_name'] = $data['sender_name'];
        }
        if (isset($data['customer_name'])) {
            $templateVars['customer_name'] = $data['customer_name'];
        }
        if (isset($data['comment'])) {
            $templateVars['comment'] = $data['comment'];
        }
        if (isset($data['points'])) {
            $templateVars['points'] = $data['points'];
        }
        if (isset($data['points_balance'])) {
            $templateVars['points_balance'] = $data['points_balance'];
        }
        if (isset($data['money_balance'])) {
            $templateVars['money_balance'] = $data['money_balance'];
        }

        return $templateVars;
    }

    /**
     * Replace comment
     *
     * @param string $comment
     * @param string $label
     * @return string
     */
    public function replaceComment(string $comment, string $label): string
    {
        if ($label !== Config::DEFAULT_LABEL_NAME) {
            $comment = str_ireplace(CommentDefault::REPLACE_POINTS_LABEL,
                $label,
                $comment
            );
        }

        return $comment;
    }
}
