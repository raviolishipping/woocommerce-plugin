=== Ravioli for WooCommerce ===
Contributors: canolcer
Tags: ravioli, ecommerce, shipping
Requires at least: 5.0
Tested up to: 6.0.1
Stable tag: 1.2.0
Requires PHP: 7.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Let your customers choose if they want to get their order shipped in a reusable Ravioli box with this official Ravioli plugin. Requires WooCommerce.

== Description ==

This is the official Ravioli plugin for WooCommerce. If you're a [Ravioli](https://getravioli.de) customer and use WooCommerce
to run your online shop, we recommend you to use this plugin. Once activated, it asks your customers
during checkout if they want to get their order shipped in a reusable Ravioli box.

## Usage and customization

Once activated, the plugin works right away without any setup needed. The Ravioli plugin
asks your customers during checkout if they want to get their order shipped in a reusable Ravioli box.
If a customer chooses Ravioli, a small fee (set by you) is added to their checkout total.

In your WordPress admin area, you can customize the Ravioli experience for your customers.
Simply navigate to WooCommerce -> Settings -> Ravioli to pull up the Ravioli settings.

Currently, you have three settings:
- **Show Ravioli Popup?**: If unchecked, the Ravioli pop-up will not be displayed
- **Ravioli fee**: Decide how much you want to charge extra if your customers choose a Ravioli box. You can also charge 0 €.
- **Maximum weight**: Customer won't see the Ravioli option if the order total weight is above this. Enter 0 for no weight limit and make sure to set a weight for each product.

Additionally, you can exclude single products from triggering the Ravioli pop-up.
To do this, go to the product's edit page, click on "Advanced" in the Product data tab, and then check
"Exclude from Ravioli?". A customer who has only excluded products in their cart won't see the Ravioli pop-up.
However, if they have for example one excluded and one not-excluded product, they will see the Ravioli pop-up
(as long as another setting doesn't prevent it from showing).

## Dependencies
The Ravioli plugins requires WooCommerce > 6.7.0.


## Open-source
This plugin is open-source and you can view the source code on our [GitHub repository](https://github.com/raviolishipping/woocommerce-plugin).

## About Ravioli
Ravioli makes using reusable shipping boxes as convenient as cardboard boxes for ecommerce shops.
After you ship your products in a Ravioli box, we take care of getting back the empty boxes and
preparing it for its next adventure.

By replacing cardboard boxes with reusable boxes, ecommerce shops can cut down their CO2 emissions use of resources.

Ravioli is currently available in Germany. Learn more on [getravioli.de](https://getravioli.de).


== Frequently Asked Questions ==

= What are Ravioli boxes? =

Ravioli boxes are reusable shipping boxes sold by Ravioli Logistik UG (haftungsbeschränkt).
If you have an e-commerce shop, you can use Ravioli boxes to send your products. Your customers then
drop off the empty box at a DHL point after unpacking the products and is send back to us.
We clean and prepare the box for its next trip and make it easy for any e-commerce shop to offer
reusable shipping boxes.

= Do I need to be a Ravioli customer to use the plugin? =

Not strictly, but the plugin doesn't make a lot of sense if you are not our customer.
You need to buy shipping boxes from us in order to be able to package your products in them.

= Where can I learn more about Ravioli? =

Glad you asked! Head over to our website [getravioli.de](https://getravioli.de) to learn more.


== Changelog ==

= 1.2.0 =
* Added remove Ravioli button to checkout page

= 1.1.0 =
* Added setting per product to exclude from Ravioli

= 1.0.4 =
* Fixed bug that wouldn't calculate dimensions for product variants

= 1.0.3 =
* Improved Ravioli fee in modal
* Fixed double display of modal

= 1.0.2 =
* Fixed compatibiliy with themes that don't support the wp_body_open hook

= 1.0.1 =
* Updated some texts

= 1.0.0 =
* Initial release
