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

namespace Aheadworks\RewardPoints\Ui\Component\Listing\Columns\SpendRule;

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Link
 */
class Link extends Column
{
    /**
     * Prepare data source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['link'])) {
                $item['link'] = $this->context->getUrl(
                    'aw_reward_points/spending_rules/edit',
                    ['id' => $item[SpendRuleInterface::ID]]
                );
            }
        }

        return $dataSource;
    }
}
