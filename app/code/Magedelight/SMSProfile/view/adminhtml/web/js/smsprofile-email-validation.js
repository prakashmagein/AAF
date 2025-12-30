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
    'mage/translate',
    'jquery/validate'],
    function ($) {
        $.validator.addMethod(
            'validate-comma-separated-emails-profile',
            function (emaillist) {
                emaillist = emaillist.trim();
                if (emaillist.charAt(0) == ',' || emaillist.charAt(emaillist.length - 1) == ',') {
                    return false; }
                var emails = emaillist.split(',');
                var invalidEmails = [];
                for (i = 0; i < emails.length; i++) {
                    var v = emails[i].trim();
                    console.log(v);
                    if (!Validation.get('validate-email').test(v)) {
                        invalidEmails.push(v);
                    }
                }
                if (invalidEmails.length) {
                    return false; }
                return true;
            },
            $.mage.__('Please enter a valid comma seprated email address.')
        );
    }
);
