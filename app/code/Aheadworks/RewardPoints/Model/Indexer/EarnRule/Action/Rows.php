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
namespace Aheadworks\RewardPoints\Model\Indexer\EarnRule\Action;

use Aheadworks\RewardPoints\Model\Indexer\EarnRule\AbstractAction;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Rows
 * @package Aheadworks\RewardPoints\Model\Indexer\EarnRule\Action
 */
class Rows extends AbstractAction
{
    /**
     * Execute Rows reindex
     *
     * @param array $ids
     * @return void
     * @throws InputException
     * @throws LocalizedException
     */
    public function execute($ids)
    {
        if (empty($ids)) {
            throw new InputException(__('Bad value was supplied.'));
        }
        try {
            $this->earnRuleProductIndexerResource->reindexRows($ids);
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()), $e);
        }
    }
}
