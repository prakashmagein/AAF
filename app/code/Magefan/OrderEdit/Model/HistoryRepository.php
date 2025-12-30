<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Model;

use Magefan\OrderEdit\Model\ResourceModel\History as ResourceHistory;
use Magefan\OrderEdit\Model\History;
use Magefan\OrderEdit\Model\ResourceModel\History\CollectionFactory as HistoryCollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class HistoryRepository
{

    /**
     * @var ResourceHistory
     */
    protected $resource;

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var HistoryCollectionFactory
     */
    protected $historyCollectionFactory;

    /**
     * @param ResourceHistory $resource
     * @param HistoryFactory $historyFactory
     * @param HistoryCollectionFactory $historyCollectionFactory
     */
    public function __construct(
        ResourceHistory $resource,
        HistoryFactory $historyFactory,
        HistoryCollectionFactory $historyCollectionFactory
    ) {
        $this->resource = $resource;
        $this->historyFactory = $historyFactory;
        $this->historyCollectionFactory = $historyCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(History $history)
    {
        try {
            $this->resource->save($history);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the history: %1',
                $exception->getMessage()
            ));
        }
        return $history;
    }

    /**
     * @inheritDoc
     */
    public function get($historyId)
    {
        $history = $this->historyFactory->create();
        $this->resource->load($history, $historyId);
        if (!$history->getId()) {
            throw new NoSuchEntityException(__('History with id "%1" does not exist.', $historyId));
        }
        return $history;
    }

    /**
     * @inheritDoc
     */
    public function delete(History $history)
    {
        try {
            $historyModel = $this->historyFactory->create();
            $this->resource->load($historyModel, $history->getHistoryId());
            $this->resource->delete($historyModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the History: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($historyId)
    {
        return $this->delete($this->get($historyId));
    }
}
