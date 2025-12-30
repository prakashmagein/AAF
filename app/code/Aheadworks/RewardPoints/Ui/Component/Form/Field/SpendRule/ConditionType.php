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

namespace Aheadworks\RewardPoints\Ui\Component\Form\Field\SpendRule;

use Magento\Ui\Component\Form\Field;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ConditionType
 */
class ConditionType extends Field
{
    /**
     * Prepare component configuration
     *
     * @return void
     * @throws LocalizedException
     */
    public function prepare(): void
    {
        if ($this->getContext()->getRequestParam('id')) {
            $config = $this->getConfig();
            $config['disabled'] = true;
            $this->setConfig($config);
        }
        parent::prepare();
    }
}
