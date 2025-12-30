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

namespace Aheadworks\RewardPoints\Model\SpendRule\ProductMatcher\ProductResolver;

use Aheadworks\RewardPoints\Model\SpendRule\ProductMatcher\ProductResolverInterface;
use Magento\Framework\Exception\ConfigurationMismatchException;

/**
 * Class Pool
 */
class Pool
{
    /**
     * Array key for default resolver
     */
    const DEFAULT_RESOLVER_CODE = 'default';

    /**
     * Pool constructor.
     *
     * @param array $resolvers
     */
    public function __construct(private $resolvers = [])
    {
    }

    /**
     * Get resolvers
     *
     * @return ProductResolverInterface[]
     */
    public function getResolvers(): array
    {
        return $this->resolvers;
    }

    /**
     * Get resolver by product code
     *
     * @param string $code
     * @return ProductResolverInterface
     * @throws \Exception
     */
    public function getResolverByCode(string $code): ProductResolverInterface
    {
        if (!isset($this->resolvers[$code])) {
            $code = self::DEFAULT_RESOLVER_CODE;
        }
        $resolver = $this->resolvers[$code];
        if (!$resolver instanceof ProductResolverInterface) {
            throw new ConfigurationMismatchException(
                __('Product resolver must implements %1', ProductResolverInterface::class)
            );
        }

        return $resolver;
    }
}
