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
namespace Aheadworks\RewardPoints\Model\ResourceModel\Transaction\Relation\Entity;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Aheadworks\RewardPoints\Model\Source\Transaction\EntityType as TransactionEntityType;
use Magento\Framework\EntityManager\MetadataPool;
use Aheadworks\RewardPoints\Api\Data\TransactionInterface;

/**
 * Class Aheadworks\RewardPoints\Model\ResourceModel\Transaction\Relation\Entity\SaveHandler
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var string
     */
    const TRANSACTION_ENTITY_TYPE = 'transaction_entity_type';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     *  {@inheritDoc}
     */
    public function execute($entity, $arguments = [])
    {
        if (isset($arguments[self::TRANSACTION_ENTITY_TYPE])) {
            $connection = $this->getConnection();
            $tableName = $this->resourceConnection->getTableName('aw_rp_transaction_entity');

            foreach ($arguments[self::TRANSACTION_ENTITY_TYPE] as $transactionEntity) {
                $transactionEntity['transaction_id'] = $entity->getTransactionId();
                $connection->insert(
                    $tableName,
                    $transactionEntity
                );
            }
        }
        return $entity;
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(TransactionInterface::class)->getEntityConnectionName()
        );
    }
}
