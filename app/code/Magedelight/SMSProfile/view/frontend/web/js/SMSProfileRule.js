 /**
 * Magedelight
 * Copyright (C) 2022 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @copyright Copyright (c) 2022 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author    Magedelight <info@magedelight.com>
 */
 
require(
    [
        'jquery',
        'Magento_Ui/js/lib/validation/validator'
    ],
    function ($,validator) {
        "use strict";

        validator.addRule(
            'min_tel_digit_length',
            function (value, params) {
                    return _.isUndefined(value) || value.length === 0 || value.length >= +params;
            },
            $.mage.__('Please enter more than or equal to {0} digits.')
        );
        
        validator.addRule(
            'max_tel_digit_length',
            function (value, params) {
                    return !_.isUndefined(value) && value.length <= +params;
            },
            $.mage.__('Please enter less than or equal to {0} digits.')
        );
        
        return validator;
    }
);