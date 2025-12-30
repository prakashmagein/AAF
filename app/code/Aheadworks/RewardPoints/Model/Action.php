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

namespace Aheadworks\RewardPoints\Model;

use Aheadworks\RewardPoints\Api\Data\ActionExtensionInterface;
use Aheadworks\RewardPoints\Api\Data\ActionInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class Action extends AbstractExtensibleObject implements ActionInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const TYPE          = 'type';
    public const ATTRIBUTES    = 'attributes';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->_get(self::ATTRIBUTES);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes($attributes)
    {
        return $this->setData(self::ATTRIBUTES, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object
     *
     * @param ActionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        ActionExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
