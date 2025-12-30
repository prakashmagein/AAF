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
namespace Aheadworks\RewardPoints\Model;

use Magento\Framework\Encryption\EncryptorInterface;

class KeyEncryptor
{
    /**
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        private readonly EncryptorInterface $encryptor
    ) {
    }

    /**
     * Encrypt external key
     *
     * @param string $customerEmail
     * @param int $customerId
     * @param int $websiteId
     * @return string
     */
    public function encrypt(string $customerEmail, int $customerId, int $websiteId): string
    {
        return base64_encode($this->encryptor->encrypt($customerEmail . ',' . $customerId . ',' . $websiteId));
    }

    /**
     * Decrypt external key
     *
     * @param string $key
     * @return array
     */
    public function decrypt(string $key): array
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        list($email, $customerId, $websiteId) = explode(',', $this->encryptor->decrypt(base64_decode($key)));
        return ['email' => $email, 'customer_id' => $customerId, 'website_id' => $websiteId];
    }
}
