# Mage2 Module Aventi Gshipping

    ``aventi/module-gshipping``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
Módulo de software para la integración en línea entre su plataforma de comercio electrónico con Gshipping, incluye cotización de envíos, etiquetas para unidades

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Aventi`
 - Enable the module by running `php bin/magento module:enable Aventi_Gshipping`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require aventi/module-gshipping`
 - enable the module by running `php bin/magento module:enable Aventi_Gshipping`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration




## Specifications

 - Controller
	- frontend > index/index/index

 - Helper
	- Aventi\Gshipping\Helper\Data

## Attributes



