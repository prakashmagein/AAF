<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Ui\Component\Listing\View\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;

class ColumnFactory
{
    public const CONFIG_DATE_TYPE = 'date_type';

    /**
     * @var array
     */
    private const JS_COMPONENT_MAP = [
        'text' => 'Magento_Ui/js/grid/columns/column',
        'select' => 'Magento_Ui/js/grid/columns/select',
        'multiselect' => 'Magento_Ui/js/grid/columns/select',
        'date' => 'Magento_Ui/js/grid/columns/date',
    ];

    /**
     * @var UiComponentFactory
     */
    private $componentFactory;

    public function __construct(
        UiComponentFactory $componentFactory
    ) {
        $this->componentFactory = $componentFactory;
    }

    /**
     * @param string $identifier
     * @param ContextInterface $context
     * @param array $config
     *
     * @return UiComponentInterface
     */
    public function create(
        string $identifier,
        ContextInterface $context,
        array $config = []
    ): UiComponentInterface {
        $renderConfig = [];
        if (!isset($config['add_field'])) {
            $config['add_field'] = true;
        }
        if (!isset($config['visible'])) {
            $config['visible'] = true;
        }
        if (!isset($config['headerTmpl'])) {
            $config['headerTmpl'] = 'Amasty_ReportBuilder/grid/columns/header';
        }

        if (!isset($config['component'])) {
            if (!empty($config['options'])) {
                $config['component'] = self::JS_COMPONENT_MAP['select'];
            } else {
                $config['component'] =
                    self::JS_COMPONENT_MAP[$config['dataType']] ?? 'Magento_Ui/js/grid/columns/column';
            }
        }
        
        if (isset($config['class'])) {
            $renderConfig['class'] = $config['class'];
            unset($config['class']);
        }

        $arguments = [
            'data' => [
                'config' => $config,
            ],
            'config' => $renderConfig,
            'context' => $context
        ];

        return $this->componentFactory->create(
            $identifier,
            'column',
            $arguments
        );
    }
}
