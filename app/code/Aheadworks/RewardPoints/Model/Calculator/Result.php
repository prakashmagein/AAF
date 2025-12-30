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

namespace Aheadworks\RewardPoints\Model\Calculator;

use Magento\Framework\Api\AbstractSimpleObject;

class Result extends AbstractSimpleObject implements ResultInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPoints()
    {
        return $this->_get(self::POINTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPoints($points)
    {
        return $this->setData(self::POINTS, $points);
    }

    /**
     * {@inheritdoc}
     */
    public function getAppliedRuleIds()
    {
        return $this->_get(self::APPLIED_RULE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAppliedRuleIds($ruleIds)
    {
        return $this->setData(self::APPLIED_RULE_IDS, $ruleIds);
    }
}
