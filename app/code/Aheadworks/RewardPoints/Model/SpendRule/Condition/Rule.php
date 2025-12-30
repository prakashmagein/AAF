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

namespace Aheadworks\RewardPoints\Model\SpendRule\Condition;

use Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\ProductResolver;
use Magento\Rule\Model\AbstractModel as AbstractRuleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\CombineFactory as RuleCombineFactory;
use Magento\CatalogRule\Model\Rule\Action\CollectionFactory as RuleCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Model\ResourceModel\Iterator as ResourceIterator;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Combine;
use Magento\Rule\Model\Action\Collection;

/**
 * Class Rule
 */
class Rule extends AbstractRuleModel
{
    /**
     * Default condition id value
     */
    const CONDITION_ID = 1;

    /**
     * Condition prefix value
     */
    const CONDITIONS_PREFIX = 'conditions';

    /**
     * @var RuleCombineFactory
     */
    private $ruleCombineFactory;

    /**
     * @var ResourceIterator
     */
    private $resourceIterator;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var ProductResolver
     */
    private $productResolver;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Store matched product Ids
     *
     * @var array
     */
    private $productIds;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param RuleCombineFactory $ruleCombineFactory
     * @param ResourceIterator $resourceIterator
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ProductFactory $productFactory
     * @param ProductResolver $productResolver
     * @param StoreManagerInterface $storeManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        RuleCombineFactory $ruleCombineFactory,
        ResourceIterator $resourceIterator,
        ProductCollectionFactory $productCollectionFactory,
        ProductFactory $productFactory,
        ProductResolver $productResolver,
        StoreManagerInterface $storeManager,
        private RuleCollectionFactory $ruleCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->ruleCombineFactory = $ruleCombineFactory;
        $this->resourceIterator = $resourceIterator;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productFactory = $productFactory;
        $this->productResolver = $productResolver;
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    /**
     * Getter for rule combine conditions instance
     *
     * @return Combine
     */
    public function getConditionsInstance()
    {
        return $this->ruleCombineFactory->create();
    }

    /**
     * Getter for rule actions collection instance
     *
     * @return Collection
     */
    public function getActionsInstance()
    {
        return $this->ruleCollectionFactory->create();
    }

    /**
     * Reset rule combine conditions
     *
     * @param null|Combine $conditions
     * @return $this
     */
    protected function _resetConditions($conditions = null)
    {
        parent::_resetConditions($conditions);

        $this->getConditions($conditions)
            ->setId(self::CONDITION_ID)
            ->setPrefix(self::CONDITIONS_PREFIX);

        return $this;
    }

    /**
     * Get array of product ids which are matched by rule
     *
     * @param array $websiteIds
     * @return array [productId => [websiteId => website_validation, ...], ...]
     */
    public function getMatchingProductIds($websiteIds)
    {
        if ($this->productIds === null) {
            $this->productIds = [];
            $this->setCollectedAttributes([]);

            /** @var $productCollection ProductCollection */
            $productCollection = $this->productCollectionFactory->create();
            $productCollection->addWebsiteFilter($websiteIds);
            $this->getConditions()->collectValidatedAttributes($productCollection);

            $this->resourceIterator->walk(
                $productCollection->getSelect(),
                [[$this, 'callbackValidateProduct']],
                [
                    'attributes' => $this->getCollectedAttributes(),
                    'product' => $this->productFactory->create(),
                    'website_ids' => $websiteIds,
                ]
            );
        }

        return $this->productIds;
    }

    /**
     * Callback function for product matching
     *
     * @param array $args
     * @return void
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        $websiteIds = $args['website_ids'];
        $results = [];

        $productsForValidation = $this->productResolver->getProductsForValidation($product);
        $isValid = false;
        foreach ($productsForValidation as $productForValidation) {
            if ($this->validate($productForValidation)) {
                $isValid = true;
                break;
            }
        }
        if ($isValid) {
            foreach ($websiteIds as $websiteId) {
                $results[$websiteId] = in_array($websiteId, $product->getWebsiteIds());
            }
            $this->productIds[$product->getId()] = $results;
        }
    }
}
