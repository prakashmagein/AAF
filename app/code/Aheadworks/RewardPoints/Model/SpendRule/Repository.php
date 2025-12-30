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

namespace Aheadworks\RewardPoints\Model\SpendRule;

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Api\Data\SpendRuleInterfaceFactory;
use Aheadworks\RewardPoints\Api\Data\SpendRuleSearchResultsInterface;
use Aheadworks\RewardPoints\Api\Data\SpendRuleSearchResultsInterfaceFactory;
use Aheadworks\RewardPoints\Model\Indexer\SpendRule\Processor as SpendRuleIndexerProcessor;
use Aheadworks\RewardPoints\Model\Repository\CollectionProcessorInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\SpendRule\Collection as SpendRuleCollection;
use Aheadworks\RewardPoints\Model\ResourceModel\SpendRule\CollectionFactory as SpendRuleCollectionFactory;
use Aheadworks\RewardPoints\Api\SpendRuleRepositoryInterface;
use Aheadworks\RewardPoints\Model\SpendRule as SpendRuleModel;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Repository
 */
class Repository implements SpendRuleRepositoryInterface
{
    /**
     * @var SpendRuleInterface[]
     */
    private $instances = [];

    /**
     * @param EntityManager $entityManager
     * @param SpendRuleInterfaceFactory $spendRuleFactory
     * @param SpendRuleSearchResultsInterfaceFactory $spendRuleSearchResultsFactory
     * @param SpendRuleCollectionFactory $spendRuleCollectionFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SpendRuleIndexerProcessor $indexerProcessor
     */
    public function __construct(
        private EntityManager $entityManager,
        private SpendRuleInterfaceFactory $spendRuleFactory,
        private SpendRuleSearchResultsInterfaceFactory $spendRuleSearchResultsFactory,
        private SpendRuleCollectionFactory $spendRuleCollectionFactory,
        private JoinProcessorInterface $extensionAttributesJoinProcessor,
        private CollectionProcessorInterface $collectionProcessor,
        private SpendRuleIndexerProcessor $indexerProcessor
    ) {
    }

    /**
     * Save rule
     *
     * @param SpendRuleInterface $rule
     * @return SpendRuleInterface
     * @throws CouldNotSaveException If validation fails
     */
    public function save(SpendRuleInterface $rule): SpendRuleInterface
    {
        try {
            $rule->validate();
            $this->entityManager->save($rule);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        $this->indexerProcessor->markIndexerAsInvalid();
        unset($this->instances[$rule->getId()]);
        return $this->get($rule->getId());
    }

    /**
     * Retrieve rule with storefront labels for specified store view
     *
     * @param int $ruleId
     * @param int|null $storeId
     * @return SpendRuleInterface
     * @throws NoSuchEntityException If the rule does not exist
     */
    public function get($ruleId, $storeId = null): SpendRuleInterface
    {
        if (!isset($this->instances[$ruleId])) {
            /** @var SpendRuleInterface $rule */
            $rule = $this->spendRuleFactory->create();
            $arguments = [
                'store_id' => $storeId
            ];
            $this->entityManager->load($rule, $ruleId, $arguments);
            if (!$rule->getId()) {
                throw NoSuchEntityException::singleField('id', $ruleId);
            }
            $this->instances[$ruleId] = $rule;
        }
        return $this->instances[$ruleId];
    }

    /**
     * Retrieve rules matching the specified criteria with storefront labels for specified store view
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param int|null $storeId
     * @return SpendRuleSearchResultsInterface
     * @throws LocalizedException if an error occurs
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        try {
            /** @var SpendRuleCollection $collection */
            $collection = $this->spendRuleCollectionFactory->create();

            $this->extensionAttributesJoinProcessor->process($collection, SpendRuleInterface::class);
            $this->collectionProcessor->process($searchCriteria, $collection);

            /** @var SpendRuleSearchResultsInterface $searchResults */
            $searchResults = $this->spendRuleSearchResultsFactory->create();
            $searchResults->setSearchCriteria($searchCriteria);
            $searchResults->setTotalCount($collection->getSize());

            $objects = [];
            /** @var SpendRuleModel $item */
            foreach ($collection->getItems() as $item) {
                $objects[] = $this->get($item->getId(), $storeId);
            }
            $searchResults->setItems($objects);
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }

        return $searchResults;
    }

    /**
     * Delete rule
     *
     * @param SpendRuleInterface $rule
     * @return bool true on success
     * @throws NoSuchEntityException If the rule does not exist
     */
    public function delete(SpendRuleInterface $rule): bool
    {
        return $this->deleteById($rule->getId());
    }

    /**
     * Delete rule by id
     *
     * @param int $ruleId
     * @return bool true on success
     * @throws NoSuchEntityException If the rule does not exist
     */
    public function deleteById($ruleId): bool
    {
        /** @var SpendRuleInterface $rule */
        $rule = $this->spendRuleFactory->create();
        $this->entityManager->load($rule, $ruleId);
        if (!$rule->getId()) {
            throw NoSuchEntityException::singleField('id', $ruleId);
        }
        $this->entityManager->delete($rule);
        unset($this->instances[$ruleId]);
        return true;
    }
}
