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
namespace Aheadworks\RewardPoints\Controller\Adminhtml\Earning\Rules\PostDataProcessor;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Model\Data\ProcessorInterface;
use Magento\Framework\Stdlib\DateTime\Filter\Date as DateFilter;

/**
 * Class Date
 * @package Aheadworks\RewardPoints\Controller\Adminhtml\Earning\Rules\PostDataProcessor
 */
class Date implements ProcessorInterface
{
    /**
     * @var DateFilter
     */
    private $dateFilter;

    /**
     * @param DateFilter $dateFilter
     */
    public function __construct(
        DateFilter $dateFilter
    ) {
        $this->dateFilter = $dateFilter;
    }

    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        $data[EarnRuleInterface::FROM_DATE] = empty($data[EarnRuleInterface::FROM_DATE])
            ? null
            : $this->dateFilter->filter($data[EarnRuleInterface::FROM_DATE]);

        $data[EarnRuleInterface::TO_DATE] = empty($data[EarnRuleInterface::TO_DATE])
            ? null
            : $this->dateFilter->filter($data[EarnRuleInterface::TO_DATE]);

        return $data;
    }
}
