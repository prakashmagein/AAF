require(['jquery', 'Magento_Catalog/js/price-utils','Magento_Customer/js/customer-data'], function($, priceUtils, customerData) {
  
    // var arabicPrice = '١٢٣٤٥٦٧٨٩٠.١٢'; // Example Arabic price
    // var englishPrice = priceUtils.formatPrice(arabicPrice, {
    //     decimalSymbol: '.',
    //     groupLength: 3,
    //     integerRequired: true,
    //     precision: 2,
    //     requiredPrecision: 2,
    //     decimalSymbol: '.',
    //     groupSymbol: ',',
    //     pattern: '%s'
    // });
    // console.log(englishPrice); // Output: 123,456,789.12
    /*
    var currencyImage = '<img class="sar-currency" style="direction:ltr;display:inline;height:14px;width:14px;" src="' + require.toUrl('Gwl_Priceconvert/images/sar-symbol.svg') + '" >';

    function replaceCurrencySymbol() {
        $('span.price').each(function () {
            var $this = $(this);
            var priceHtml = $this.html();

            // Prevent multiple replacements
            if (!$this.hasClass('currency-replaced') && priceHtml.includes('SAR')) {
                $this.html(priceHtml.replace(/SAR/g, currencyImage)); // Replace all instances of SAR
                $this.addClass('currency-replaced'); // Prevent duplicate replacement
            }
        });
    }

    $(document).ready(function () {
        replaceCurrencySymbol();

        // **Observe AJAX updates to update prices dynamically**
        $(document).ajaxComplete(function () {
            replaceCurrencySymbol();
        });

        // **Use MutationObserver for KnockoutJS-based updates**
        var observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.type === 'childList') {
                    replaceCurrencySymbol();
                }
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });

        // **Handle Magento's dynamic UI components reloading**
        customerData.get('cart').subscribe(function () {
            replaceCurrencySymbol();
        });

        customerData.get('messages').subscribe(function () {
            replaceCurrencySymbol();
        });
    });*/
    
function convertArabicToEnglish(input) {
    var arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '٬', '٫'];
    var englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ',', '.'];

    for (var i = 0; i < arabicNumbers.length; i++) {
        var regex = new RegExp(arabicNumbers[i], 'g');
        input = input.replace(regex, englishNumbers[i]);
    }

    return input;
}

    $(document).ready(function() {
        // Find and format Arabic prices to English format
        setTimeout( function(){ 
        $('.price').each(function() {
            $(this).attr('dir', 'ltl');
            $(this).css('direction', 'ltr');
            var priceText = $(this).text();
            var englishPrice = convertArabicToEnglish(priceText);
            $(this).text(englishPrice);
        });
    }  , 3000 );

    setTimeout( function(){
        $('.price').each(function() {
            $(this).attr('dir', 'ltl');
            $(this).css('direction', 'ltr');
            var priceText = $(this).text();
            var englishPrice = convertArabicToEnglish(priceText);
            $(this).text(englishPrice);
        });
    }  , 8000 );    

    setTimeout( function(){
        $('.price').each(function() {
            $(this).attr('dir', 'ltl');
            $(this).css('direction', 'ltr');
            var priceText = $(this).text();
            var englishPrice = convertArabicToEnglish(priceText);
            $(this).text(englishPrice);
        });
    }  , 15000 );

    });


    function convertPrices() {
        $('.price').each(function() {
            $(this).attr('dir', 'ltl');
            $(this).css('direction', 'ltr');
            var priceText = $(this).text();
            var englishPrice = convertArabicToEnglish(priceText);
            $(this).text(englishPrice);
        });
    }

    
    function paginationConvertor() {
        $('.toolbar .pages span').each(function() {
            var englishNumber = $(this).text();
            var englishnumbernew = convertArabicToEnglish(englishNumber);
            $(this).text(englishnumbernew);
            });
        }

        function limitorConvertor() {
        $('.toolbar .limiter .limiter-options option').each(function() {
            var englishNumber = $(this).text();
            var englishnumbernew = convertArabicToEnglish(englishNumber);
            $(this).text(englishnumbernew);
            });
        }    
    

    $(document).on('ajaxComplete', function() {
        convertPrices();
        paginationConvertor();
        limitorConvertor();
    });

    $(document).ready(function () {
        convertPrices();
        paginationConvertor();
        limitorConvertor();
    });



    $(document).ready(function() {
    $(document).on('click', '.swatch-option', function() {
        $(document).ready(function() {
      $('.price').each(function() {
        $(this).attr('dir', 'ltl');
        $(this).css('direction', 'ltr');
            var priceText = $(this).text();
            var englishPrice = convertArabicToEnglish(priceText);
            $(this).text(englishPrice);
        });
    });
        });
    });
});
