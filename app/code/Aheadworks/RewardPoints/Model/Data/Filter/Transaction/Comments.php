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

namespace Aheadworks\RewardPoints\Model\Data\Filter\Transaction;

use Magento\Framework\Filter\StripTags;

/**
 * Class Comments
 */
class Comments implements FilterInterface
{
    /**
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    const POST_DATA_COMMENT_TO_CUSTOMER = 'comment_to_customer';
    const POST_DATA_COMMENT_TO_ADMIN = 'comment_to_admin';
    /**#@-*/

    /**
     * @var StripTags
     */
    private $tagFilter;

    /**
     * @param  StripTags $tagFilter
     */
    public function __construct(
        StripTags $tagFilter
    ) {
        $this->tagFilter = $tagFilter;
    }

    /**
     * Filter post data
     *
     * @param array $data
     * @return array|null
     */
    public function filter(array $data): ?array
    {
        if ($data[self::POST_DATA_COMMENT_TO_CUSTOMER]) {
            $data[self::POST_DATA_COMMENT_TO_CUSTOMER] = $this->tagFilter->filter($data[self::POST_DATA_COMMENT_TO_CUSTOMER]);
        }
        if ($data[self::POST_DATA_COMMENT_TO_ADMIN]) {
            $data[self::POST_DATA_COMMENT_TO_ADMIN] = $this->tagFilter->filter($data[self::POST_DATA_COMMENT_TO_ADMIN]);
        }

        return $data;
    }
}
