# WooCommerce Payment Gateway Boilerplate [![Built with Grunt](https://cdn.gruntjs.com/builtwith.png)](http://gruntjs.com/)

The best payment gateway boilerplate you will ever need. Start developing your payment gateway straight away for WooCommerce. All the basics are already covered for you.

## What does the payment gateway boilerplate offer?

The boilerplate offers you the ability to start implementing the payment gateway's api of your choice without coding the base from scratch. The main functions, actions and filter hooks are already in place for you to not only develop quicker but for a smoother process in developing the payment gateway. It is ready to support credit cards, custom payment fields, custom instructions and more.

## What about support?

If the documentation provided doesn’t help you then you can use the [forum topic](http://www.sebastiendumont.com/support/forum/woocommerce-payment-gateway-boilerplate/) to ask any questions about the boilerplate and either I or the community will respond.

## What still needs to be worked on?

* Support for WooCommerce Pre-orders
* Support for WooCommerce Subscriptions

> I have started writing an extension class to support payments for the subscriptions and pre-orders but please note that it is incomplete and has not yet been tested out.

## Contents

The WooCommerce Payment Gateway Boilerplate includes the following files:

* This README.md
* CHANGELOG.md
* CONTRIBUTING.md
* license.txt file
* `.editorconfig` file.
* `.gitattributes` file.
* `.gitignore` file.
* `.composer.json` file.
* `Gruntfile.js` file.
* `package.json` file
* A subdirectory called `woocommerce-payment-gateway-boilerplate` that represents the core of the payment gateway files.

## Installation

1. Copy the `woocommerce-payment-gateway-boilerplate` directory into your `wp-content/plugins` directory
2. Navigate to the *Plugins* dashboard page
3. Locate 'WooCommerce Payment Gateway Boilerplate'
4. Click on *Activate*

> This will activate the WooCommerce Payment Gateway Boilerplate and I recommend that you install it on a development site not a live site.

## Recommended Tools

### Localization Tools

The WooCommerce Payment Gateway Boilerplate uses a variable to store the text domain used when internationalizing strings throughout the Boilerplate. To take advantage of this method, there are tools that are recommended for providing correct, translatable files:

* [Poedit](http://www.poedit.net/)
* [makepot](http://i18n.svn.wordpress.org/tools/trunk/)
* [i18n](https://github.com/grappler/i18n)
* [grunt-wp-i18n](https://github.com/blazersix/grunt-wp-i18n)

Any of the above tools should provide you with the proper tooling to localize the plugin.

### GitHub Updater

The WooCommerce Payment Gateway Boilerplate includes native support for the [GitHub Updater](https://github.com/afragen/github-updater) which allows you to provide updates to your payment gateway from GitHub.

This uses a new tag in the plugin header:

>  `* GitHub Plugin URI: https://github.com/<owner>/<repo>`

Here's how to take advantage of this feature:

1. Install the [GitHub Updater](https://github.com/afragen/github-updater)
2. Replace the url of the repository for your plugin
3. Push commits to the master branch
4. Enjoy your plugin being updated in the WordPress dashboard

The current version of the GitHub Updater supports tags/branches - whichever has the highest number.

To specify a branch that you would like to use for updating, just add a `GitHub Branch:` header. GitHub Updater will preferentially use a tag over a branch having the same or lesser version number. If the version number of the specified branch is greater then the update will pull from the branch and not from the tag.

The default state is either `GitHub Branch: master` or nothing at all. They are equivalent.

All that info is in [the project](https://github.com/afragen/github-updater).

## Documentation

> Documentation will be provided via the GitHub wiki pages. -- Coming Soon --

## Support
This repository is not suitable for support. Please don't use our issue tracker for support requests, but for core WooCommerce Payment Gateway Boilerplate issues only. Support can take place in the appropriate channel:

* The [public support forum](http://www.sebastiendumont.com/support/forum/woocommerce-payment-gateway-boilerplate/) at SebastienDumont.com, where the community can help each other out.

Support requests in issues on this repository will be closed on sight.

## Contributing to WooCommerce Payment Gateway Boilerplate

If you have a patch, or stumbled upon an issue with WooCommerce Payment Gateway Boilerplate core, you can contribute this back to the code. Please read the [contributor guidelines](https://github.com/seb86/WooCommerce-Payment-Gateway-Boilerplate/blob/master/CONTRIBUTING.md) for more information how you can do this.

## License

The WooCommerce Payment Gateway Boilerplate is licensed under the GPL v2 or later.

> This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

> This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

> You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

## Important Notes

### Licensing

The WooCommerce Payment Gateway Boilerplate is licensed under the GPL v2 or later; however, if you opt to use third-party code that is not compatible with v2, then you may need to switch to using code that is GPL v3 compatible.

For reference, [here's a discussion](http://make.wordpress.org/themes/2013/03/04/licensing-note-apache-and-gpl/) that covers the Apache 2.0 License used by [Bootstrap](http://twitter.github.io/bootstrap/).
