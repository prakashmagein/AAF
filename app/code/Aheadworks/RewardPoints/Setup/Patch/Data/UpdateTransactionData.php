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
namespace Aheadworks\RewardPoints\Setup\Patch\Data;

use Aheadworks\RewardPoints\Model\Comment\CommentPoolInterface;
use Aheadworks\RewardPoints\Model\Config;
use Aheadworks\RewardPoints\Model\DateTime;
use Aheadworks\RewardPoints\Model\Source\NotifiedStatus;
use Aheadworks\RewardPoints\Model\Source\Transaction\EntityType;
use Aheadworks\RewardPoints\Model\Source\Transaction\Status;
use Aheadworks\RewardPoints\Model\Source\Transaction\Type;
use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class UpdateTransactionData
 */
class UpdateTransactionData implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var SchemaSetupInterface $schemaSetup
     */
    private $schemaSetup;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CommentPoolInterface
     */
    private $commentPool;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param State $appState
     * @param SchemaSetupInterface $schemaSetup
     * @param OrderRepositoryInterface $orderRepository
     * @param CommentPoolInterface $commentPool
     * @param DateTime $dateTime
     * @param Config $config
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        State $appState,
        SchemaSetupInterface $schemaSetup,
        OrderRepositoryInterface $orderRepository,
        CommentPoolInterface $commentPool,
        DateTime $dateTime,
        Config $config
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->appState = $appState;
        $this->schemaSetup = $schemaSetup;
        $this->orderRepository = $orderRepository;
        $this->commentPool = $commentPool;
        $this->dateTime = $dateTime;
        $this->config = $config;
    }

    /**
     * Apply patch
     *
     * @return $this
     * @throws Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->appState->emulateAreaCode(
            Area::AREA_ADMINHTML,
            [$this, 'updateTransactionData'],
            [$this->schemaSetup]
        );
        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    /**
     * Update transaction data
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws Exception
     */
    public function updateTransactionData(SchemaSetupInterface $installer): self
    {
        $connection = $installer->getConnection();
        $this->processTransactions($installer);

        $select = $connection->select()
            ->from($installer->getTable('aw_rp_points_summary'));
        $pointsSummary = $connection->fetchAssoc($select);

        foreach ($pointsSummary as $summary) {
            $select = $connection->select()
                ->from($installer->getTable('aw_rp_transaction'), ['transaction_id'])
                ->where('customer_id = ?', $summary['customer_id']);
            $transactionIds = $connection->fetchCol($select);
            if (count($transactionIds)) {
                $connection->update(
                    $installer->getTable('aw_rp_transaction'),
                    ['status' => Status::USED, 'expiration_notified' => NotifiedStatus::CANCELLED],
                    'transaction_id IN(' . implode(',', array_values($transactionIds)) . ')'
                );
                if ($summary['points'] > 0) {
                    $customerSelect = $connection->select()
                        ->from(
                            $installer->getTable('customer_entity'),
                            [
                                'email',
                                'firstname',
                                'lastname'
                            ]
                        )
                        ->where('entity_id = ?', $summary['customer_id']);
                    $customer = $connection->fetchRow($customerSelect);
                    $connection->insert(
                        $installer->getTable('aw_rp_transaction'),
                        [
                            'customer_id' => $summary['customer_id'],
                            'customer_name' => $customer['firstname'] . ' ' . $customer['lastname'],
                            'customer_email' => $customer['email'],
                            'comment_to_customer' => null,
                            'comment_to_customer_placeholder' => null,
                            'comment_to_admin' =>
                                'Transaction has been created after update points from 1.0.0 to 1.1.0',
                            'balance' => $summary['points'],
                            'current_balance' => $summary['points'],
                            'transaction_date' => $this->dateTime->getTodayDate(true),
                            'expiration_date' => $this->getExpirationDate($summary['website_id']),
                            'website_id' => $summary['website_id'],
                            'type' => Type::BALANCE_ADJUSTED_BY_ADMIN,
                            'status' => Status::ACTIVE,
                            'balance_update_notified' => NotifiedStatus::NO,
                            'expiration_notified' => NotifiedStatus::WAITING,
                            'created_by' => null
                        ]
                    );
                }
            }
        }
        return $this;
    }

    /**
     * Process transactions
     *
     * @param SchemaSetupInterface $installer
     * @return void
     * @throws Exception
     */
    private function processTransactions(SchemaSetupInterface $installer): void
    {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $connection = $installer->getConnection();
        $select = $connection->select()
            ->from($installer->getTable('aw_rp_transaction'), ['transaction_id'])
            ->where('expiration_date <= ?', $now)
            ->where('expiration_date IS NOT NULL');
        $expiredTransactionIds = $connection->fetchCol($select);
        if (count($expiredTransactionIds)) {
            $connection->update(
                $installer->getTable('aw_rp_transaction'),
                ['status' => Status::EXPIRED],
                'transaction_id IN(' . implode(',', array_values($expiredTransactionIds)) . ')'
            );
        }

        $oldComments = [
            'reward_for_order' => ['parse' => true, 'type' => Type::POINTS_REWARDED_FOR_ORDER],
            'reward_for_registration' => ['type' => Type::POINTS_REWARDED_FOR_REGISTRATION],
            'reward_for_review' => ['type' => Type::POINTS_REWARDED_FOR_REVIEW_APPROVED_BY_ADMIN],
            'reward_for_share' => ['type' => Type::POINTS_REWARDED_FOR_SHARES],
            'reward_for_newsletter_signup' => ['type' => Type::POINTS_REWARDED_FOR_NEWSLETTER_SIGNUP],
            'spent_for_order' => ['parse' => true, 'type' => Type::POINTS_SPENT_ON_ORDER],
            'expired_points' => ['parse' => true, 'type' => Type::POINTS_EXPIRED]
        ];
        $select = $connection->select()
            ->from($installer->getTable('aw_rp_transaction'));
        $transactions = $connection->fetchAssoc($select);

        // Convert comment
        foreach ($transactions as $transaction) {
            $updateParams = ['type' => Type::BALANCE_ADJUSTED_BY_ADMIN];
            foreach ($oldComments as $oldComment => $param) {
                if (!(strrpos($transaction['comment_to_customer'], $oldComment) !== false)) {
                    continue;
                }
                $commentArguments = [];
                $updateParams = ['type' => $param['type']];
                if (isset($param['parse']) && $param['parse']) {
                    if ($param['type'] == Type::POINTS_EXPIRED) {
                        $bind = [
                            'transaction_id' => $transaction['transaction_id'],
                            'entity_id'    => 0,
                            'entity_type'  => EntityType::TRANSACTION_ID,
                            'entity_label' => ''
                        ];
                        $commentArguments = [
                            EntityType::TRANSACTION_ID => [
                                'entity_id' => 0,
                                'entity_label' => ''
                            ]
                        ];
                    } else {
                        $orderId = str_replace($oldComment . '_', '', $transaction['comment_to_customer']);
                        try {
                            $order = $this->orderRepository->get($orderId);
                        } catch (NoSuchEntityException $e) {
                            continue;
                        }

                        $bind = [
                            'transaction_id' => $transaction['transaction_id'],
                            'entity_id'    => $order->getEntityId(),
                            'entity_type'  => EntityType::ORDER_ID,
                            'entity_label' => $order->getIncrementId()
                        ];
                        $commentArguments = [
                            EntityType::ORDER_ID => [
                                'entity_id' => $order->getEntityId(),
                                'entity_label' => $order->getIncrementId()
                            ]
                        ];
                    }
                    $connection->insert($installer->getTable('aw_rp_transaction_entity'), $bind);
                }

                $commentInstance = $this->commentPool->get($param['type']);
                $updateParams['comment_to_customer'] = $commentInstance->renderComment($commentArguments);
                $updateParams['comment_to_customer_placeholder'] = $commentInstance->getLabel();
            }

            $connection->update(
                $installer->getTable('aw_rp_transaction'),
                $updateParams,
                'transaction_id = ' . $transaction['transaction_id']
            );
        }
    }

    /**
     * Retrieve expiration date
     *
     * @param  int|null $websiteId
     * @return string
     */
    private function getExpirationDate($websiteId = null)
    {
        $expireInDays = $this->config->getCalculationExpireRewardPoints($websiteId);

        if ($expireInDays == 0) {
            return null;
        }
        return $this->dateTime->getExpirationDate($expireInDays, false);
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getVersion()
    {
        return '1.1.0';
    }
}
