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

namespace Aheadworks\RewardPoints\Model\Comment\Admin;

use Aheadworks\RewardPoints\Model\Comment\CommentInterface;
use Aheadworks\RewardPoints\Model\Source\Transaction\EntityType as TransactionEntityType;
use Magento\Framework\Phrase\Renderer\Placeholder;
use Magento\Framework\UrlInterface;

/**
 * Class AppliedSpendingRules
 */
class AppliedSpendingRules implements CommentInterface
{
    /**
     * Comment type name
     */
    const COMMENT_FOR_APPLIED_SPENDING_RULES = 'comment_for_applied_spending_rules';

    /**
     * @param UrlInterface $urlBuilder
     * @param Placeholder $placeholder
     * @param int|null $type
     * @param string|array|null $label
     */
    public function __construct(
        private UrlInterface $urlBuilder,
        private Placeholder  $placeholder,
        private $type = null,
        private $label = null
    ) {
    }

    /**
     * Retrieve comment type
     *
     * @return string|int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Retrieve comment label
     *
     * @param string $key
     * @param array $arguments
     * @return string
     */
    public function getLabel($key = null, $arguments = []): string
    {
        $label = $this->label;
        if (is_array($this->label)) {
            $label = ($key && isset($this->label[$key]))
                ? $this->label[$key]
                : $label = $this->label['default'];
        }
        return (string)__($label, $arguments);
    }

    /**
     * Render comment key to comment label
     *
     * @param array $arguments
     * @param string $key
     * @param string $label
     * @param bool $renderingUrl
     * @param bool $frontend
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function renderComment(
        $arguments = [],
        $key = null,
        $label = null,
        $renderingUrl = false,
        $frontend = false
    ): string {
        if (!$label) {
            $label = $this->getLabel();
        }
        $labelArguments = [];
        $rulePlaceholders = [];
        foreach ($arguments as $entityType => $entity) {
            if ($entityType == TransactionEntityType::SPEND_RULE_ID) {
                if (isset($entity['entity_id'])) {
                    $ruleLabelData = $this->getRuleLabelData($entity, $renderingUrl);
                    $rulePlaceholders[] = $ruleLabelData['placeholder'];
                    $labelArguments = $ruleLabelData['arguments'];
                } else {
                    foreach ($entity as $item) {
                        $ruleLabelData = $this->getRuleLabelData($item, $renderingUrl);
                        $rulePlaceholders[] = $ruleLabelData['placeholder'];
                        $labelArguments = $this->mergeArrays($labelArguments, $ruleLabelData['arguments']);
                    }
                }
            }
        }

        $label = str_replace(
            '%rule_ids',
            implode(', ', $rulePlaceholders),
            $label
        );

        return $this->placeholder->render([$label], $labelArguments);
    }

    /**
     * Merge two arrays
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    private function mergeArrays(array $array1, array $array2): array
    {
        return array_merge($array1, $array2);
    }

    /**
     * Retrieve placeholder
     *
     * @param array $entity
     * @param bool $renderingUrl
     * @return string
     */
    private function retrievePlaceholder(array $entity, bool $renderingUrl): string
    {
        $ruleLabelData = $this->getRuleLabelData($entity, $renderingUrl);
        return $ruleLabelData['placeholder'];
    }

    /**
     * Render comment key to comment label with translation
     *
     * @param array $arguments
     * @param string $key
     * @param string $label
     * @param bool $renderingUrl
     * @param bool $frontend
     * @return string
     */
    public function renderTranslatedComment(
        $arguments = [],
        $key = null,
        $label = null,
        $renderingUrl = false,
        $frontend = false
    ): string {
        return $this->renderComment($arguments, $key, $label, $renderingUrl, $frontend);
    }

    /**
     * Get rule label data
     *
     * @param array $entity
     * @param bool $renderingUrl
     * @return array
     */
    private function getRuleLabelData(array $entity, bool $renderingUrl): array
    {
        $arguments = [];
        $entityId = $entity['entity_id'];
        $idName = 'rule_id_' . $entityId;
        $arguments[$idName] = '#' . $entity['entity_label'];
        $placeholder = '%' . $idName;
        if ($renderingUrl) {
            $urlName = 'rule_url_' . $entityId;
            $arguments[$urlName] = $this->getSpendRuleUrl((int)$entityId);
            $placeholder = '<a href="%' . $urlName . '">%' . $idName . '</a>';
        }

        return [
            'placeholder' => $placeholder,
            'arguments' => $arguments,
        ];
    }

    /**
     * Retrieve spend rule url
     *
     * @param int $ruleId
     * @return string
     */
    private function getSpendRuleUrl(int $ruleId): string
    {
        return $this->urlBuilder->getUrl('aw_reward_points/spending_rules/edit', ['id' => $ruleId]);
    }
}
