define([
    'mage/translate',
    'Amasty_ReportBuilder/js/charts/types/linear'
], function ($t, linear) {
    'use strict';

    var description = [
        {
            'value': 'linear',
            'label': $t('Linear Chart'),
            'axises': [
                {
                    id: '',
                    type: 'x',
                    label: 'X-axis'
                },
                {
                    id: '',
                    type: 'y',
                    label: 'Y-axis'
                }
            ]
        },
        {
            'value': 'simplePie',
            'label': $t('Simple Pie Chart')
        },
        {
            'value': 'semiCirclePie',
            'label': $t('Semi-circle Pie Chart')
        },
        {
            'value': 'simpleColumn',
            'label': $t('Simple Column Chart')
        },
        {
            'value': 'sortedBar',
            'label': $t('Sorted Bar Chart')
        }
    ];

    return {
        'description': description,
        'linear': linear
    };
});
