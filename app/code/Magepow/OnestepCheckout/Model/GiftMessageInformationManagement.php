<?php
/**
 * GiftMessageInformationManagement
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */

namespace Magepow\OnestepCheckout\Model;

use Magepow\OnestepCheckout\Api\GiftMessageInformationManagementInterface;
class GiftMessageInformationManagement implements GiftMessageInformationManagementInterface
{
    /**
     * @var \Magento\GiftMessage\Api\CartRepositoryInterface
     */
    protected $cartRepository;
    /**
     * @var \Magento\GiftMessage\Api\ItemRepositoryInterface
     */
    protected $itemRepository;
    /**
     * @var \Magento\GiftMessage\Model\MessageFactory
     */
    protected $messageFactory;

    public function __construct(
        \Magento\GiftMessage\Api\CartRepositoryInterface $cartRepository,
        \Magento\GiftMessage\Api\ItemRepositoryInterface $itemRepository,
        \Magento\GiftMessage\Model\MessageFactory $messageFactory
    ) {
        $this->cartRepository = $cartRepository;
        $this->itemRepository = $itemRepository;
        $this->messageFactory = $messageFactory;
    }

    public function update($cartId, $giftMessage)
    {
        foreach ($giftMessage as $messageData) {

            /** @var \Magento\GiftMessage\Model\Message $message */
            $message = $this->messageFactory->create();

            $message->setData([
                'message' => $messageData['message'],
                'sender' => $messageData['sender'],
                'recipient' => $messageData['recipient'],
            ]);

            if ($messageData['item_id'] == \Magepow\OnestepCheckout\Model\Gift\Messages::QUOTE_MESSAGE_INDEX) {
                $this->cartRepository->save($cartId, $message);
            }
            else {
                $this->itemRepository->save($cartId, $message, $messageData['item_id']);
            }
        }

        return true;
    }
}
