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
namespace Aheadworks\RewardPoints\Ui\Component\Listing\Columns\Transaction;

use Aheadworks\RewardPoints\Model\Comment\CommentPoolInterface;
use Aheadworks\RewardPoints\Model\Comment\Admin\AppliedEarningRules;
use Aheadworks\RewardPoints\Model\Comment\Admin\AppliedSpendingRules;
use Aheadworks\RewardPoints\Model\Source\Transaction\EntityType as TransactionEntityType;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Aheadworks\RewardPoints\Ui\Component\Listing\Columns\Transaction\CommentToAdmin
 */
class CommentToAdmin extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CommentPoolInterface $commentPool
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly CommentPoolInterface $commentPool,
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
        if (isset($dataSource['data']['items']) && is_array($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (empty($item['entities'])) {
                    continue;
                }

                $commentType = null;
                foreach ($item['entities'] as $entityType => $entityData) {
                    switch ($entityType) {
                        case TransactionEntityType::EARN_RULE_ID:
                            $commentType = AppliedEarningRules::COMMENT_FOR_APPLIED_EARNING_RULES;
                            break;
                        case TransactionEntityType::SPEND_RULE_ID:
                            $commentType = AppliedSpendingRules::COMMENT_FOR_APPLIED_SPENDING_RULES;
                            break;
                    }
                }

                if ($commentType) {
                    $commentLabel = $this->getComment(
                        $commentType,
                        $item['entities'],
                        $item['comment_to_admin_placeholder']
                    );

                    if ($commentLabel) {
                        $item['comment_to_admin'] = $commentLabel;
                    }
                }
            }
        }

        return $dataSource;
    }

    /**
     * Get comment
     *
     * @param string $type
     * @param array $entities
     * @param string $label
     * @return string|null
     */
    private function getComment(string $type, array $entities, string $label): ?string
    {
        $commentLabel = null;
        $commentInstance = $this->commentPool->get($type);
        if ($commentInstance) {
            $commentLabel = $commentInstance->renderComment(
                $entities,
                null,
                $label,
                true
            );
        }
        return $commentLabel;
    }
}
