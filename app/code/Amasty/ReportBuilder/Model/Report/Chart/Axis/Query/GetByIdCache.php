<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Chart\Axis\Query;

use Amasty\ReportBuilder\Api\Data\AxisInterface;

class GetByIdCache implements GetByIdInterface
{
    /**
     * @var array
     */
    private $cache = [];

    /**
     * @var GetById
     */
    private $getById;

    public function __construct(GetById $getById)
    {
        $this->getById = $getById;
    }

    public function execute(int $id): AxisInterface
    {
        if (!isset($this->cache[$id])) {
            $this->cache[$id] = $this->getById->execute($id);
        }

        return $this->cache[$id];
    }
}
