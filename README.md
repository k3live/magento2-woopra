# magento2-woopra
=============

# Magento2 Integration with Woopra Real-Time Analytics

## Installation

### Step 1

#### Using Composer
composer require k3live/magento2-woopra

#### Manually
Download the extension
Unzip the file contents to a local folder
Create a folder on the server {Magento 2 root}/app/code/Woopra/Analytics
Copy the content from the local folder to the newly created folder on server

### Step 2 - Enable Woopra Analytics ("cd" to {Magento root} folder)
  php -f bin/magento module:enable --clear-static-content Woopra_Analytics
  php -f bin/magento setup:upgrade

### Step 3 - Configure Woopra Analytics
Log into your Magento 2 Admin, then goto Stores -> Configuration -> Woopra -> Woopra Analytics