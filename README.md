# PHP Component Spider

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT) [![CodeFactor](https://www.codefactor.io/repository/github/marioungui/php-component-spider/badge)](https://www.codefactor.io/repository/github/marioungui/php-component-spider) [![Latest Stable Version](https://poser.pugx.org/marioungui/php-component-spider/v)](https://packagist.org/packages/marioungui/php-component-spider) [![License](https://poser.pugx.org/marioungui/php-component-spider/license)](https://packagist.org/packages/marioungui/php-component-spider) [![PHAR Build](https://github.com/marioungui/PHP-Component-Spider/actions/workflows/php.yml/badge.svg)](https://github.com/marioungui/PHP-Component-Spider/actions/workflows/php.yml)

This PHP Component Spider is designed to scrape websites for specific components or search criteria defined by XPath filters. It uses the PHPScraper library to fetch and process web pages, and the League\Csv library to log the results in CSV files. This tool is easy to extend with custom XPath filters to meet various scraping needs.

## Features

- Scrape websites for specific components or text based on XPath filters.
- Log results into CSV files for further analysis.
- Configurable timeout and maximum redirects.
- Easy to extend with additional filters.

## Requirements

- PHP 8.1 or higher
- Composer

## Build & Run from Source Code

1. Clone the repository:

```bash
git clone https://github.com/marioungui/PHP-Component-Spider.git
```

2. Navigate to the project directory:

```bash
cd PHP-Component-Spider
```

3. Install the dependencies using Composer:

```bash
composer install
```

4. Build the Phar package:

```bash
php -d phar.readonly=0 phar-creator.php
```

5. Run the batch spider.bat
6. Follow the on-screen instructions to select the component to search for and the domain to scrape.

## Filters

The filters are defined in filters.php and use XPath to identify specific components on the web pages. Here are the current filters available:

| Component | Index | Filter |
| --- | --- | --- |
| MVP Block | 1   | `//*[@class='mvp-block']` |
| Smart Question Search Engine Block | 2   | `//*[@class='sqe-block']` |
| Related Articles Block | 3   | `//h2[text()='Artigos relacionados' or text()='Artigos Relacionados' or text()='Articulos Relacionados' or text()='Articulos relacionados' ]` |
| Related Products Block | 4   | `//h2[text()='Produtos Relacionados' or text()='Produtos Relacionados' or text()='Productos relacionados' or text()='Productos Relacionados']` |
| Brands Block | 5   | `//*[starts-with(@id, 'brands_block')]/@id` |
| Stages Block | 6   | `//*[starts-with(@id, 'stages_block')]` |
| String Search | 7   | `//*[contains(text(),'word')]` |
| Action Bar | 8   | `//div[contains(@class, 'action-bar__wrapper')]` |
| Links Containing | 9   | `//a[contains(@href, 'word')]` |
| Stages Block using From Library | 10  | `//div[contains(@class, 'paragraph--type--stages-block')]//div[contains(@class, 'grid-col-10')]` |

## Extending with Custom Filters

Extending the tool with new filters is simple:

1. Open the `filters.php` file.
2. Add a new `case` in the `switch` statement with your component name or index.
3. Define the `$component` and `$filter` variables with your custom XPath.

Example:

```php
case 'new-component':
case 11:
    $component = "New Component";
    $filter = "//*[@class='new-component-class']";
    break;
```

## Contributing

Feel free to submit issues or pull requests if you have any improvements or new features you'd like to add.

## License

This project is licensed under the MIT License.
