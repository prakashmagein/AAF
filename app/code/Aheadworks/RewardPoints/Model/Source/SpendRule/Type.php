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

namespace Aheadworks\RewardPoints\Model\Source\SpendRule;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 */
class Type implements OptionSourceInterface
{
    /**#@+
     * Type condition
     */
    const CATALOG = 'catalog';
    const CART = 'cart';

    /**
     * @var array
     */
    private $options;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray(): array
    {
        if (!$this->options) {
            $this->options = [
                [
                    'value' => self::CATALOG,
                    'label' => __('Сatalog')
                ],
                [
                    'value' => self::CART,
                    'label' => __('Cart')
                ]
            ];
        }
        return $this->options;
    }
}
