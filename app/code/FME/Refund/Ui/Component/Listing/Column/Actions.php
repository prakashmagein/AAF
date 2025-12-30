<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Hassan <support@fmeextensions.com>
 * @package   FME_Refund
 * @copyright Copyright (c) 2021 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */

namespace FME\Refund\Ui\Component\Listing\Column;

    use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;
    use Magento\Framework\UrlInterface;
    use Magento\Framework\View\Element\UiComponent\ContextInterface;
    use Magento\Framework\View\Element\UiComponentFactory;
    use Magento\Ui\Component\Listing\Columns\Column;

    class Actions extends Column
    {
        /** Url path */
        public const URL_PATH_ACCEPT = 'refund/index/accept';
        public const URL_PATH_REJECT = 'refund/index/reject';

        /** @var UrlBuilder */
        protected $actionUrlBuilder;

        /** @var UrlInterface */
        protected $urlBuilder;

        /**
         * @var string
         */
        private $editUrl;

        public function __construct(
            ContextInterface $context,
            UiComponentFactory $uiComponentFactory,
            UrlBuilder $actionUrlBuilder,
            UrlInterface $urlBuilder,
            array $components = [],
            array $data = []
        ) {
            $this->urlBuilder = $urlBuilder;
            $this->actionUrlBuilder = $actionUrlBuilder;
            parent::__construct($context, $uiComponentFactory, $components, $data);
        }

        /**
         * Prepare Data Source.
         *
         * @return array
         */
        public function prepareDataSource(array $dataSource)
        {
            if (isset($dataSource['data']['items'])) {
                foreach ($dataSource['data']['items'] as &$item) {
                    $name = $this->getData('name');
                    if (isset($item['refund_id'])) {
                        $item[$name]['accept'] = [
                            'href' => $this->urlBuilder->getUrl(self::URL_PATH_ACCEPT, ['refund_id' => $item['refund_id']]),
                            'label' => __('Accept'),
                            'confirm' => [
                                'title' => __('Accept Request '),
                                'message' => __('Are you sure you wan\'t to Accept a Request having ID '.$item['refund_id'].' ?'),
                            ],
                        ];

                        $item[$name]['reject'] = [
                            'href' => $this->urlBuilder->getUrl(self::URL_PATH_REJECT, ['refund_id' => $item['refund_id']]),
                            'label' => __('Reject'),
                            'confirm' => [
                                'title' => __('Reject Request'),
                                'message' => __('Are you sure you wan\'t to Reject a Request having ID '.$item['refund_id'].' ?'),
                            ],
                        ];
                    }
                }
            }

            return $dataSource;
        }
    }
