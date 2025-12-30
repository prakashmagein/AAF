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

namespace Aheadworks\RewardPoints\Block\Adminhtml\SpendRule\Edit\Conditions;

/**
 * Class TypeResolver
 */
class TypeResolver
{
    /**
     * @var array
     */
    private $typeMapping;

    /**
     * @param array $typeMapping
     */
    public function __construct(array $typeMapping)
    {
        $this->typeMapping = $typeMapping;
    }

    /**
     * Return rule type by condition type
     *
     * @param string $conditionPrefix
     * @return array
     */
    public function resolve(string $conditionPrefix): array
    {
        return $this->typeMapping[$conditionPrefix] ?? [];
    }
}
