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

/**
 * Class CustomerGroup
 */
class CustomerGroup implements ProcessorInterface
{
    /**
     * Process data
     *
     * @param array $data
     * @return array
     */
    public function process($data): array
    {
        $customerGroupData = [];
        if (isset($data[SpendRuleInterface::CUSTOMER_GROUP_IDS])
            && is_array($data[SpendRuleInterface::CUSTOMER_GROUP_IDS])
        ) {
            foreach ($data[SpendRuleInterface::CUSTOMER_GROUP_IDS] as $key => $value) {
                $customerGroupData[$key] = (int)$value;
            }
        }
        $data[SpendRuleInterface::CUSTOMER_GROUP_IDS] = $customerGroupData;

        return $data;
    }
}
