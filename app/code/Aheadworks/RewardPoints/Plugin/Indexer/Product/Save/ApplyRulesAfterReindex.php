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
namespace Aheadworks\RewardPoints\Plugin\Indexer\Product\Save;

use Aheadworks\RewardPoints\Model\Indexer\EarnRule\Processor as EarnRuleIndexerProcessor;
use Magento\Catalog\Model\Product;

/**
 * Class ApplyRulesAfterReindex
 * @package Aheadworks\RewardPoints\Plugin\Indexer\Product\Save
 */
class ApplyRulesAfterReindex
{
    /**
     * @var EarnRuleIndexerProcessor
     */
    private $earnRuleIndexerProcessor;

    /**
     * @param EarnRuleIndexerProcessor $earnRuleIndexerProcessor
     */
    public function __construct(
        EarnRuleIndexerProcessor $earnRuleIndexerProcessor
    ) {
        $this->earnRuleIndexerProcessor = $earnRuleIndexerProcessor;
    }

    /**
     * Apply catalog rules after product resource model save
     *
     * @param Product $subject
     * @return void
     */
    public function afterReindex(Product $subject)
    {
        $this->earnRuleIndexerProcessor->reindexRow($subject->getId());
    }
}
