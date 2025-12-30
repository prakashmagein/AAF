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
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Aheadworks\RewardPoints\Ui\Component\Listing\Columns\Transaction\CommentToCustomer
 */
class CommentToCustomer extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var CommentPoolInterface
     */
    private $commentPool;

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
        CommentPoolInterface $commentPool,
        array $components = [],
        array $data = []
    ) {
        $this->commentPool = $commentPool;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items']) && is_array($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['type'])) {
                    if ($commentInstance = $this->commentPool->get($item['type'])) {
                        $commentLabel = $commentInstance->renderComment(
                            $item['entities'],
                            null,
                            $item['comment_to_customer_placeholder'],
                            true
                        );
                    }

                    if (!empty($commentLabel)) {
                        $item['comment_to_customer'] = $commentLabel;
                    }
                }
            }
        }
        return $dataSource;
    }
}
