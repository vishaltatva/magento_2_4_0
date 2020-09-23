##Push Notification Extension

##Support: 
version - 2.3.x, 2.4.x

##How to install Extension

1. Download the archive file.
2. Unzip the file
3. Create a folder [Magento_Root]/app/code/Sparsh/PushNotification
4. Drop/move the unzipped files

#Enable Extension:
- php bin/magento module:enable Sparsh_PushNotification
- php bin/magento setup:upgrade
- php bin/magento setup:di:complile
- php bin/magento cache:clean
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush

#Disable Extension:
- php bin/magento module:disable Sparsh_PushNotification
- php bin/magento setup:upgrade
- php bin/magento setup:di:complile
- php bin/magento cache:clean
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush
