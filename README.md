# PHP Component Spider

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

 This is a web crawler developed for internal use of Brand Websites. This is not suitable for using in a web server but in a Terminal with the following example:

    php spider.php -cmvp -d{domain}
This is assuming that the PHP 8.1 engine is with global availability and the terminal has the right permissions to use the PHP binary.

The current components that the script can scan for now is

- MVP [-cmvp]
- Smart Question Search Engine [-csearch]

## Setup

Setup is easy! All you have to do is clone this repository with the following command:

    git clone https://github.com/marioungui/PHP-Component-Spider.git

## ¿How it works?

The spider works in the following steps

 1. Checks if all the required parameters is set
 2. Check if the parameters is valid
 3. Check if the domain is valid with DNS solving correctly
 4. If all check passes, then parses the sitemap.xml file
 5. The scan for the components begins, with an **OK** if valid and **Error** if the component is missing from the current scanned page.

## Bugs? Suggestions?

You can fill a [Github Issue here](https://github.com/marioungui/PHP-Component-Spider/issues/new) with suggestions, bug fixes or you could make a GIT clone and make a Pull Request after making changes and testing.
