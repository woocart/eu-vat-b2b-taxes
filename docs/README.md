# Better Tax Handling Plugin

The plugin adds three sections to the default Tax menu in WooCommerce: 

- Tax Handling for B2B
- EU Tax Handling - Digital Goods (B2C)
- EU Tax Handling - Distance Selling (B2C)

If you haven't yet, you still need to manually enter your country's standard, reduced and zero rates. This is not handled by the plugin.

The default settings should be the correct setting for all EU countries. That said, make sure to confirm the settings with your accountant or tax advisor.

Please keep in mind you or your accountant need to do the VAT reporting to the EU, as this is not done by the plugin.


## Plugin Settings

### Tax Handling for B2B

This section handles taxes for B2B (Business to Business). Let's have a look at the options for B2B tax handling offered by the plugin and how it affects tax calculation for the customer.

<img src="images/tax-b2b.png" alt="Tax Handling for B2B">

- **B2B Sales** - Enable this option (for either EU Store or Non-EU Store) to add a checkbox for businesses to the checkout form. When the visitor clicks the option in the checkout form, an additional Tax ID field opens for entering the Business Tax ID. 

- **Tax ID field required for B2B** - Enabling this option makes it mandatory for all B2B customers to provide a Business Tax ID. By default it is required.

- **B2B sales in the home country** - Whether B2B sales in the home country are taxed or not. By default it is charged.

- **B2B sales in the EU when VIES/VAT ID is provided** - Whether B2B sales in EU are taxed or not. By default it is not charged. This option is disabled if you chose the Non-EU Store in the first dropdown.

- **B2B sales outside the country** - Whether B2B sales outside the home country and EU are taxed or not. For example: selling from Germany to the USA. By default it is not charged.

### Digital Goods (B2C - EU)

This part of the plugin handles taxes for [Digital Goods](https://quaderno.io/resources/eu-vat-guide/) for consumers when selling to the EU. This option has no impact on the B2B transactions.

<img src="images/tax-b2c-digital.png" alt="Digital Goods (B2C)">

For this to work properly, you need to go through two steps.

- **EU Tax Handling for Digital Goods** - Click to enable Digital Goods tax. Use the next option to import tax rates.

- **Import tax rates for all EU countries and create tax class Digital Goods** - Click to import standard VAT rates for all EU countries and create a tax class Digital Goods. You must assign this tax class to all digital goods in your store. Digital goods are charged the customer's country tax rate. 

### Distance Selling (B2C - EU)

This part of the plugin handles taxes for Digital Selling for Consumers. As a store owner, you will need to register for EU VAT ID in countries where you reach [Distance Selling EU Tax thresholds](https://www.vatlive.com/eu-vat-rules/distance-selling/distance-selling-eu-vat-thresholds/). This option has no impact on the B2B transactions.

<img src="images/tax-b2c-distance.png" alt="Distance Selling (B2C)">

For this to work properly, you need to go through three steps.

- **Enable EU VAT Handling for Distance Selling** - Enable to collect local taxes in specific countries. Choose the countries for this category in the next option. 

- **Select countries for which you would like to import tax rates** - Add countries whose tax rates you would like to import.

- **Import tax rates for specific EU countries** - Click to import tax rates for all EU countries selected in the above option. 

## Reports
