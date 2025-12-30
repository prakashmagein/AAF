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

namespace Aheadworks\RewardPoints\Ui\Component\Form\Field\EarnRule;

use Magento\Ui\Component\Container;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class LabelsDynamicRows extends Container
{
    /**
     * @param ContextInterface $context
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        private readonly StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare(): void
    {
        if ($this->isNeedToDisableAddingRows()) {
            $this->disableAddingRows();
        }
        parent::prepare();
    }

    /**
     * Check if need to disable adding new rows
     *
     * @return bool
     */
    private function isNeedToDisableAddingRows(): bool
    {
        return $this->storeManager->isSingleStoreMode();
    }

    /**
     * Disable adding new rows
     */
    private function disableAddingRows()
    {
        $config = $this->getConfig();
        $config['addButton'] = false;
        $this->setConfig($config);
    }
}
