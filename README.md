# utils

Some scripts which may be useful for other developers.

## Installation

Update `/config.php` with your database configuration.

## Script list

### Prestashop (v1.7)

    /prestashop/copylanguage.php
This script copy product information from a language to another: so, if you're in french, you will have english name, description etc... in products.

    /prestashop/updatelanguages.php
This script load CSV files with product informations to insert in database.

How to: Insert your `.csv` files in `/prestashop/csv/` directory and update `$files` in `updatelanguages.php`
