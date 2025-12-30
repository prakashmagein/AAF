# Version 1.0.2 - 08/20/2021
- Improve coding standard
- Upgrade database shipping rate table with new columns: allow_second_price, second_price, cost, allow_free_shipping, free_shipping
- Support apply second price when add more than 1 product to cart
- Support apply free shipping when cart subtotal equal or greater than free_shipping value
- Fix issue with calculate shipping fee on checkout cart, checkout page
- Compatible with magento 2.4.2, 2.4.2-p1, 2.4.3
- Fix issue for task: UFBME-141

# Version 1.0.2.1 - 11/18/2021
- Fix issue with condition rules get product shipping
- New option "Disable Free shipping" allow disable Free shipping, Table rate shipping Free ship when use available Product Shipping

# Version 1.0.3 - 02/25/2022
- Upgrade database table, add new columns for table
- Support REST API
- Added sample import CSV files
- Support new option split shipment base on shipping method type
- Support new option price for unit. Default price_for_unit = yes
