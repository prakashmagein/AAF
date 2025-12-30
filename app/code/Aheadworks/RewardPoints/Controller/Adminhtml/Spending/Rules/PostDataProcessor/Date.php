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

namespace Aheadworks\RewardPoints\Controller\Adminhtml\Spending\Rules\PostDataProcessor;

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Model\Data\ProcessorInterface;
use Magento\Framework\Stdlib\DateTime\Filter\Date as DateFilter;

/**
 * Class Date
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
     * Process data
     *
     * @param array $data
     * @return array
     */
    public function process($data): array
    {
        $data[SpendRuleInterface::FROM_DATE] = empty($data[SpendRuleInterface::FROM_DATE])
            ? null
            : $this->dateFilter->filter($data[SpendRuleInterface::FROM_DATE]);

        $data[SpendRuleInterface::TO_DATE] = empty($data[SpendRuleInterface::TO_DATE])
            ? null
            : $this->dateFilter->filter($data[SpendRuleInterface::TO_DATE]);

        return $data;
    }
}
