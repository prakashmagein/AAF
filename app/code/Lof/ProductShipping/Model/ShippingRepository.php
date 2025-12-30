<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\ProductShipping\Model;

use Lof\ProductShipping\Api\Data\ShippingInterface;
use Lof\ProductShipping\Api\Data\ShippingInterfaceFactory;
use Lof\ProductShipping\Api\Data\ShippingSearchResultsInterfaceFactory;
use Lof\ProductShipping\Api\ShippingRepositoryInterface;
use Lof\ProductShipping\Model\ResourceModel\Shipping as ResourceShipping;
use Lof\ProductShipping\Model\ResourceModel\Shippingmethod as ResourceShippingMethod;
use Lof\ProductShipping\Model\ResourceModel\Shipping\CollectionFactory as ShippingCollectionFactory;
use Lof\ProductShipping\Model\ShippingmethodFactory as ShippingMethodFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ShippingRepository implements ShippingRepositoryInterface
{

    /**
     * @var ShippingInterfaceFactory
     */
    protected $shippingFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var ShippingCollectionFactory
     */
    protected $shippingCollectionFactory;

    /**
     * @var ResourceShipping
     */
    protected $resource;

    /**
     * @var Shipping
     */
    protected $searchResultsFactory;

    /**
     * @var ShippingMethodFactory
     */
    protected $shippingMethodFactory;

    /**
     * @var ResourceShippingMethod
     */
    protected $resourceShippingMethod;

    /**
     * @param ResourceShipping $resource
     * @param ShippingInterfaceFactory $shippingFactory
     * @param ShippingCollectionFactory $shippingCollectionFactory
     * @param ShippingSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ShippingMethodFactory $shippingMethodFactory
     * @param ResourceShippingMethod $resourceShippingMethod
     */
    public function __construct(
        ResourceShipping $resource,
        ShippingInterfaceFactory $shippingFactory,
        ShippingCollectionFactory $shippingCollectionFactory,
        ShippingSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        ShippingMethodFactory $shippingMethodFactory,
        ResourceShippingMethod $resourceShippingMethod
    ) {
        $this->resource = $resource;
        $this->shippingFactory = $shippingFactory;
        $this->shippingCollectionFactory = $shippingCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->resourceShippingMethod = $resourceShippingMethod;
    }

    /**
     * @inheritDoc
     */
    public function save(ShippingInterface $shipping)
    {
        try {
            $shippingMethodName = $shipping->getMethodName();
            $shippingMethodId = $shipping->getShippingMethodId();
            if (!$shippingMethodName && !$shippingMethodId) {
                throw new CouldNotSaveException(__(
                    'Could not save the shipping: require method_name or shipping_method_id'
                ));
            }
            if ($shippingMethodName) {
                $shippingMethodId = $this->calculateShippingMethodId($shippingMethodName);
            } else {
                $shippingMethod = $this->getShippingMethod((int)$shippingMethodId);
                $shippingMethodName = $shippingMethod->getMethodName();
            }
            $shipping->setMethodName($shippingMethodName);
            $shipping->setShippingMethodId($shippingMethod);

            $weightFrom = $shipping->getWeightFrom();
            $weightTo = $shipping->getWeightTo();
            if ($weightFrom != "*" && $weightFrom != "") {
                $shipping->setWeightFrom((float)$weightFrom);
            }
            if ($weightTo != "*" && $weightTo != "") {
                $shipping->setWeightTo((float)$weightTo);
            }

            $this->resource->save($shipping);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the shipping: %1',
                $exception->getMessage()
            ));
        }
        return $shipping;
    }

    /**
     * @inheritDoc
     */
    public function get($shippingId)
    {
        $shipping = $this->shippingFactory->create();
        $this->resource->load($shipping, $shippingId);
        if (!$shipping->getId()) {
            throw new NoSuchEntityException(__('Shipping with id "%1" does not exist.', $shippingId));
        }
        return $shipping;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->shippingCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(ShippingInterface $shipping)
    {
        try {
            $shippingModel = $this->shippingFactory->create();
            $this->resource->load($shippingModel, $shipping->getShippingId());
            $this->resource->delete($shippingModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Shipping: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($shippingId)
    {
        return $this->delete($this->get($shippingId));
    }

    /**
     * @inheritDoc
     */
    public function deleteMethodById($methodId)
    {
        try {
            $shippingMethodModel = $this->shippingMethodFactory->create();
            $this->resourceShippingMethod->load($shippingMethodModel, $methodId);
            $this->resourceShippingMethod->delete($shippingMethodModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Shipping Method: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteShippingByMethod($methodId)
    {
        try {
            $collection = $this->shippingCollectionFactory->create();
            $collection->addFieldToFilter("shipping_method_id", $methodId);

            foreach ($collection as $item) {
                $this->deleteById($item->getId());
            }
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Shipping Method: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getShippingMethod($methodId)
    {
        $shippingMethodModel = $this->shippingMethodFactory->create();
        $this->resourceShippingMethod->load($shippingMethodModel, $methodId);
        return $shippingMethodModel;
    }

    /**
     * @inheritdoc
     */
    public function calculateShippingMethodId($methodName)
    {
        $shippingMethodId = $this->getShippingIdByName($methodName);

        if ($shippingMethodId == 0) {
            $shippingMethodModel = $this->shippingMethodFactory->create();
            $shippingMethodModel->setMethodName($methodName);
            $this->resourceShippingMethod->save($shippingMethodModel);
            $shippingMethodId = $shippingMethodModel->getId();
        }

        return $shippingMethodId;
    }

    /**
     * @param $shippingMethodName
     * @return int
     */
    public function getShippingIdByName($shippingMethodName)
    {
        $entityId            = 0;
        $shippingMethodModel = $this->shippingMethodFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('method_name', $shippingMethodName)
                                ->getFirstItem();

        if ($shippingMethodModel && $shippingMethodModel->getId()) {
            $entityId = (int)$shippingMethodModel->getId();
        }

        return $entityId;
    }
}

