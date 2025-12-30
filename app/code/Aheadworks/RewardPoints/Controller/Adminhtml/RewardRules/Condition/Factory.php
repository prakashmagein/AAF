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

namespace Aheadworks\RewardPoints\Controller\Adminhtml\RewardRules\Condition;

use Magento\Framework\ObjectManagerInterface;
use Magento\CatalogRule\Model\Rule;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Framework\Exception\ConfigurationMismatchException;

/**
 * Class Factory
 */
class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $type
     * @param int|string $id
     * @param string $prefix
     * @param string|null $attribute
     * @param string|null $jsFormObject
     * @param string|null $formName
     * @return AbstractCondition
     * @throws \Exception
     */
    public function create(
        $type,
        $id,
        $prefix,
        $attribute,
        $jsFormObject,
        $formName
    ): AbstractCondition {
        $conditionModel = $this->objectManager->create($type);

        if (!$conditionModel instanceof AbstractCondition) {
            throw new ConfigurationMismatchException(
                __('Condition must be instance of %1', AbstractCondition::class)
            );
        }

        $conditionModel
            ->setId($id)
            ->setType($type)
            ->setRule($this->objectManager->create(Rule::class))
            ->setPrefix($prefix)
            ->setJsFormObject($jsFormObject)
            ->setFormName($formName);

        if ($attribute) {
            $conditionModel->setAttribute($attribute);
        }

        return $conditionModel;
    }
}
