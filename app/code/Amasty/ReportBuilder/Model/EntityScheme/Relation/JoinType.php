<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\EntityScheme\Relation;

use Magento\Framework\DB\Select;

class JoinType
{
    const INNER_JOIN = 'inner';
    const LEFT_JOIN = 'left';
    const RIGHT_JOIN = 'right';

    /**
     * Convert join type to following join call in Db Select.
     *
     * @param string $joinType
     * @return string
     */
    public function getJoinForSelect(string $joinType): string
    {
        switch ($joinType) {
            case self::LEFT_JOIN:
                $result = Select::LEFT_JOIN;
                break;
            case self::RIGHT_JOIN:
                $result = Select::RIGHT_JOIN;
                break;
            case self::INNER_JOIN:
            default:
                $result = Select::INNER_JOIN;
                break;
        }

        return $result;
    }
}
