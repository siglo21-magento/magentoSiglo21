# Mage2 Module Aventi UpdateOrderStatus

    aventi/module-updateorderstatus

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)


## Main Functionalities
order status update cron

## Installation

### Type 1: Zip file

 - Unzip the zip file in `app/code/Aventi`
 - Enable the module by running `php bin/magento module:enable Aventi_UpdateOrderStatus`
 - Apply database updates by running `php bin/magento setup:upgrade --keep-generated`
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require aventi/module-updateorderstatus`
 - enable the module by running `php bin/magento module:enable Aventi_UpdateOrderStatus`
 - apply database updates by running `php bin/magento setup:upgrade --keep-generated`
 - Flush the cache by running `php bin/magento cache:flush`



