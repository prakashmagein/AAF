<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_GoogleMapPinAddress
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\GoogleMapPinAddress\Plugin\Magento\Sales\Model;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
  
class OrderRepository
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
        
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $coreSession;

    /** @var \Magento\Sales\Api\Data\OrderExtensionFactory */
    protected $extensionFactory;

    /**
     * Constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     * @param \Magento\Sales\Api\Data\OrderExtensionFactory $extensionFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Sales\Api\Data\OrderExtensionFactory $extensionFactory
    ) {
        $this->logger = $logger;
        $this->coreSession = $coreSession;
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * Get
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $entity
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $entity
    ) {
        /** @var \Magento\Sales\Api\Data\OrderExtension $extensionAttributes */
        $extensionAttributes = $entity->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->extensionFactory->create();
        }
        if (!$entity->getProductType() == "downlodable") {
            return $entity;
        }
        if ($entity->getShippingAddress()->getLatitude()) {
            $extensionAttributes->setLatitude($entity->getShippingAddress()->getLatitude());
        }
        if ($entity->getShippingAddress()->getLongitude()) {
            $extensionAttributes->setLongitude($entity->getShippingAddress()->getLongitude());
        }
        $entity->setExtensionAttributes($extensionAttributes);
        return $entity;
    }

    /**
     * GetList
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderSearchResultInterface $entities
     *
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    public function afterGetList(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderSearchResultInterface $entities
    ) {
        foreach ($entities->getItems() as $entity) {
            $this->afterGet($subject, $entity);
        }
        return $entities;
    }
}
