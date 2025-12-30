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
namespace Aheadworks\RewardPoints\Model\Comment;

use Aheadworks\RewardPoints\Model\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Aheadworks\RewardPoints\Model\Source\Transaction\EntityType as TransactionEntityType;
use Magento\Framework\Phrase\Renderer\Placeholder;
use Magento\Store\Model\StoreManagerInterface;

class CommentDefault implements CommentInterface
{
    /**
     * Points label name for replace
     */
    public const REPLACE_POINTS_LABEL = ['Reward Points', 'reward points', 'Reward points', 'Points'];

    /**
     * @param UrlInterface $urlBuilder
     * @param Placeholder $placeholder
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param null $type
     * @param null $label
     */
    public function __construct(
        private readonly UrlInterface $urlBuilder,
        private readonly Placeholder $placeholder,
        private readonly Config $config,
        private readonly StoreManagerInterface $storeManager,
        private $type = null,
        private $label = null
    ) {
    }

    /**
     *  {@inheritDoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *  {@inheritDoc}
     */
    public function getLabel($key = null, $arguments = [])
    {
        $label = $this->label;
        if (is_array($this->label)) {
            $label = ($key && isset($this->label[$key]))
                ? $this->label[$key]
                : $label = $this->label['default'];
        }

        return __($label, $arguments);
    }

    /**
     * Render comment key to comment label
     *
     * @param array $arguments
     * @param null $key
     * @param null $label
     * @param bool $renderingUrl
     * @param bool $frontend
     * @return Phrase|string
     * @throws LocalizedException
     */
    public function renderComment($arguments = [], $key = null, $label = null, $renderingUrl = false, $frontend = false)
    {
        $labelArguments = [];
        foreach ($arguments as $entityType => $entity) {
            switch ($entityType) {
                case TransactionEntityType::ORDER_ID:
                    $labelArguments['order_id'] = '#' . $entity['entity_label'];
                    if ($renderingUrl) {
                        $labelArguments['order_url'] = $this->getOrderUrl($entity['entity_id']);
                        $label = str_replace(
                            '%order_id',
                            '<a href="%order_url">%order_id</a>',
                            $label
                        );
                    }
                    break;
                case TransactionEntityType::CREDIT_MEMO_ID:
                    $labelArguments['creditmemo_id'] = '#' . $entity['entity_label'];
                    if ($renderingUrl) {
                        $labelArguments['creditmemo_url'] = $this->getCreditMemoUrl(
                            $entity['entity_id'],
                            $arguments[TransactionEntityType::ORDER_ID]['entity_id'],
                            $frontend
                        );
                        $label = str_replace(
                            '%creditmemo_id',
                            '<a href="%creditmemo_url">%creditmemo_id</a>',
                            $label
                        );
                    }
                    break;
                case TransactionEntityType::TRANSACTION_ID:
                    $labelArguments['transaction_id'] = $entity['entity_id'];
                    break;
            }
        }
        if ($frontend) {
            $label = $this->replaceLabel($label, (int)$this->storeManager->getWebsite()->getId());
        }

        return $renderingUrl
            ? $this->placeholder->render([$label], $labelArguments)
            : $this->getLabel($key, $labelArguments);
    }

    /**
     * Retrieve order url
     *
     * @param int $orderId
     * @return string
     */
    private function getOrderUrl($orderId)
    {
        return $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $orderId]);
    }

    /**
     * Retrieve credit memo url
     *
     * @param int $creditMemoId
     * @param int $orderId
     * @param bool $frontend
     * @return string
     */
    private function getCreditMemoUrl($creditMemoId, $orderId, $frontend = false)
    {
        if ($frontend) {
            $url = $this->urlBuilder->getUrl('sales/order/creditmemo', ['order_id' => $orderId]);
        } else {
            $url = $this->urlBuilder->getUrl('sales/order_creditmemo/view', ['creditmemo_id' => $creditMemoId]);
        }
        return $url;
    }

    /**
     * {@inheritDoc}
     */
    public function renderTranslatedComment(
        $arguments = [],
        $key = null,
        $label = null,
        $renderingUrl = false,
        $frontend = false
    ) {
        return $this->renderComment($arguments, $key, __($label), $renderingUrl, $frontend);
    }

    /**
     * Replace Label
     *
     * @param array|string $label
     * @param int $websiteId
     * @return array|string|string[]
     */
    public function replaceLabel(array|string $label, int $websiteId): array|string
    {
        if ($this->config->getLabelNameRewardPoints($websiteId) !== Config::DEFAULT_LABEL_NAME) {
            $label = str_ireplace(self::REPLACE_POINTS_LABEL,
                $this->config->getLabelNameRewardPoints($websiteId),
                $label
            );
        }

        return $label;
    }
}
