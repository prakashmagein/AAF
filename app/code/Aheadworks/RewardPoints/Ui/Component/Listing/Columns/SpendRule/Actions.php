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

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Actions
 */
class Actions extends Column
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = [
                    'enable' => $this->getActionItem(
                        __('Edit'),
                        'aw_reward_points/spending_rules/edit',
                        [
                            'id' => $item['id']
                        ]
                    ),
                    'delete' => $this->getActionItem(
                        __('Delete'),
                        'aw_reward_points/spending_rules/delete',
                        [
                            'id' => $item['id']
                        ],
                        [
                            'confirm' => [
                                'title' => __('Delete'),
                                'message' => __("Are you sure you want to delete selected item?")
                            ]
                        ]
                    )
                ];
            }
        }
        return $dataSource;
    }

    /**
     * Get action item
     *
     * @param string $label
     * @param string $path
     * @param array $params
     * @param array $additionalParams
     * @return array
     */
    private function getActionItem($label, $path, $params, $additionalParams = []): array
    {
        $actionItem = [
            'href' => $this->urlBuilder->getUrl(
                $path,
                $params
            ),
            'label' => $label,
        ];
        $actionItem = array_merge($actionItem, $additionalParams);

        return $actionItem;
    }
}
