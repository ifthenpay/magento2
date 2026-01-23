# Ifthenpay Magento 2 payment module.

Read in ![Portuguese](https://github.com/ifthenpay/magento2/raw/assets/assets/img/pt.png) [Portuguese](readme.pt.md), and ![English](https://github.com/ifthenpay/magento2/raw/assets/assets/img/en.png) [English](readme.md)

[1. Introduction](#introduction)

[2. Compatibility and Support](#compatibility-and-support)

[3. Installation](#installation)
  * [Installation using composer](#installation-using-composer)
  * [Manual installation](#manual-installation)

[4. Configuration](#configuration)
  * [Backoffice Key](#backoffice-key)
  * [Multibanco](#multibanco)
  * [Multibanco with Dynamic References](#multibanco-with-dynamic-references)
  * [MB WAY](#mb-way)
  * [Credit Card](#credit-card)
  * [Payshop](#payshop)
  * [Cofidis Pay](#cofidis-pay)
  * [Pix](#pix)
  * [Ifthenpay Gateway](#ifthenpay-gateway)

[5. Refund](#refund)

[6. Multistore](#multistore)

[7. Other](#other)
  * [Request creation of aditional account](#request-creation-of-aditional-account)
  * [Reset Configuration](#reset-configuration)
  * [Callback](#callback)
  * [Cronjob](#cronjob)
  * [Logs](#logs)


[8. Consumer User Experience](#consumer-user-experience)
  * [Pay order with Multibanco](#pay-order-with-multibanco)
  * [Pay order with Payshop](#pay-order-with-payshop)
  * [Pay order with MB WAY](#pay-order-with-mb-way)
  * [Pay order with Credit Card](#pay-order-with-credit-card)
  * [Pay order with Cofidis Pay](#pay-order-with-cofidis-pay)
  * [Pay order with Pix](#pay-order-with-pix)
  * [Pay order with Ifthenpay Gateway](#pay-order-with-ifthenpay-gateway)




# Introduction
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/payment_methods_banner.png)

**This is the Ifthenpay plugin for the Magento 2 e-commerce platform.**

**Multibanco** is one Portuguese payment method that allows the customer to pay by bank reference.
This module will allow you to generate a payment Reference that the customer can then use to pay for his order on the ATM or Home Banking service. This plugin uses one of the several gateways/services available in Portugal, IfthenPay.

**MB WAY** is the first inter-bank solution that enables purchases and immediate transfers via smartphones and tablets.

This module will allow you to generate a request payment to the customer mobile phone, and he can authorize the payment for his order on the MB WAY App service. This module uses one of the several gateways/services available in Portugal, IfthenPay.

**Payshop** is one Portuguese payment method that allows the customer to pay by payshop reference.
This module will allow you to generate a payment Reference that the customer can then use to pay for his order on the Payshop agent or CTT. This module uses one of the several gateways/services available in Portugal, IfthenPay.

**Credit Card** This module will allow you to generate a payment by Visa or Master card, that the customer can then use to pay for his order. This module uses one of the several gateways/services available in Portugal, IfthenPay.

**Cofidis Pay** is a payment solution of up to 12 interest-free installments that makes it easier to pay for purchases by splitting them. This module uses one of the several gateways/services available in Portugal, IfthenPay.

**Pix** is an instant payment solution widely used in the Brazilian financial market. It enables quick and secure transactions for purchases, using details such as CPF, email, and phone number to complete the payment.

**Ifthenpay Gateway** is a payment gateway page that provides all the payment methods above in one place. This extension uses ifthenpay, one of the various gateways available in Portugal.

**Contract with Ifthenpay is required.**

See more at [Ifthenpay](https://ifthenpay.com). 

Membership at [Membership Ifthenpay](https://www.ifthenpay.com/aderir/).

**Support**

For support, please create a support ticket at [Support Ifthenpay](https://helpdesk.ifthenpay.com/).









# Compatibility and Support

The table below indicates the compatibility and the support provided for this module.

|                         | Magento 2.4 [2.4.0 - 2.4.6] |
|-------------------------|-----------------------------|
| Compatibility           | Ifthenpay v2.0.0 - v2.3.1   |
| LTS (Long Time Support) | Supported until end of 2026 |


# Installation

It is possible to install the module in two ways: using Composer or manually placing the files in the app/code/ folder.

## Installation using composer

1. Access the root folder of your online store using the terminal. This can be done either by connecting via SSH or using the terminal of your web hosting.
2. Execute the following commands in sequence:


```bash
composer require ifthenpay/magento2
```

```bash
php bin/magento setup:upgrade
```

```bash
php bin/magento setup:di:compile
```

```bash
php bin/magento cache:clean
```

## Manual installation

1. Download the latest version of the module at [Ifthenpay Github](https://github.com/ifthenpay/magento2/releases).

![download github](https://github.com/ifthenpay/magento2/raw/assets/assets/img/githubDownload.png)
</br>

2. If it doesn't exist, create the following folders in the root of your online store: app/code/Ifthenpay/Payment, and place the module files inside the created folder.

![download github](https://github.com/ifthenpay/magento2/raw/assets/assets/img/folderExample.png)
</br>

3. Execute the following commands in sequence:

```bash
php bin/magento setup:upgrade
```

```bash
php bin/magento setup:di:compile
```
 
```bash
php bin/magento cache:clean
```



# Configuration

After installing the module, it will be available in the settings of your online store.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/goConfiguracoes.png)
</br>

Choose Sales, Payment Methods, and when you find the Ifthenpay module, click on Configure.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/goIfthenpay.png)
</br>


## Backoffice Key

The Backoffice Key is provided upon contract completion and consists of sets of four digits separated by a hyphen (-). Enter the Backoffice Key (1) and click Save (2).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/insertBackofficeKey.png)
</br>

## Multibanco
Click on Multibanco (1) to expand the configuration options.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/expandMultibanco.png)
</br>


The Multibanco payment method generates references using an algorithm and is used if you do not wish to assign a time limit (in days) for orders paid with Multibanco.
The Entity and Sub-entity are automatically loaded when entering the Backoffice Key.
Configure the payment method. The image below shows an example of a minimally functional configuration.

1. **Enabled** - When selected as Yes, it activates the payment method, displaying it on the checkout page of your store.
2. **Title** - The title that appears to the consumer at checkout, in case you choose not to display the icon.
3. **Display Icon** - When selected as Yes, it displays the payment method's icon at checkout.
4. **Activate Callback** - When selected as Yes, the order status will be updated when payment is received.
5. **Entity** - Select an Entity. You can only select one of the Entities associated with the Backoffice Key.
6. **Sub-entity** - Select a Sub-entity. You can only select one of the Sub-entities associated with the previously chosen Entity.
7. **Send Invoice Email** - When selected as Yes, the consumer automatically receives an email with the order invoice when payment is received.
8. **Minimum Amount** - (optional) Only displays this payment method for orders with a value higher than the entered amount.
9. **Maximum Amount** - (optional) Only displays this payment method for orders with a value lower than the entered amount.
10. **Restrict Payment to Countries** - (optional) Select all countries or only specific countries. Leave it blank to allow all countries.
11. **Payment from Specific Countries** - (optional) Only displays this payment method for orders with shipping destinations within the selected countries. Leave it blank to allow all countries.
12. **Sort Order** - (optional) Orders the payment methods on the checkout page in ascending order. The lower the number, the higher the priority.

Click on Save to save the changes.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/configurationMultibanco.png)
</br>



## Multibanco with Dynamic References

The Multibanco payment method with Dynamic References generates references per order and is used if you wish to assign a time limit (in days) for orders paid with Multibanco.
The Entity and Multibanco Key are automatically loaded when entering the Backoffice Key.
Configure the payment method. The image below shows an example of a minimally functional configuration.

Follow the steps from Multibanco payment method with the following modifications:

1. **Entity** - Select "Multibanco Dynamic References" as the Entity. This entity will only be available for selection if you have a contract for creating a Multibanco account with Dynamic References.
2. **Multibanco Key** - Select a Multibanco Key. You can only select one of the Multibanco Keys associated with the previously chosen Entity.
3. **Deadline** - Select the number of days for the validity of the Multibanco reference. Selecting 0 will make the Multibanco reference expire at 23:59 on the same day it was generated. Leaving it blank will make the Multibanco reference not expire.
4. Click on Save to save the changes.


![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/configurationMultibancoDynamic.png)
</br>


## MB WAY

The MB WAY payment method uses a mobile phone number provided by the consumer and generates a payment request to the consumer's MB WAY smartphone application, which they can accept or refuse.
The MB WAY Keys are automatically loaded when entering the Backoffice Key.
Configure the payment method. The image below shows an example of a minimally functional configuration.

1. **Enabled** - When selected as Yes, it activates the payment method, displaying it on the checkout page of your store.
2. **Title** - The title that appears to the consumer at checkout, in case you choose not to display the icon.
3. **Display Icon** - When selected as Yes, it displays the payment method's icon at checkout.
4. **Display Countdown** - (optional) When selected as "Yes," it displays a countdown of the payment time limit on the order success page. Select "No" if there are conflicts with one-page-checkout modules.
5. **Activate Callback** - When selected as Yes, the order status will be updated when payment is received.
6. **MB WAY Key** - Select a MB WAY key. You can only select one of the keys associated with the Backoffice Key.
7. **Send Invoice Email** - When selected as Yes, the consumer automatically receives an email with the order invoice when payment is received.
8. **Allow Refunds** - When selected as Yes, it displays a button on the credit note page that allows an administrator of the online store to refund the amount paid by the consumer.
9. **Minimum Amount** - (optional) Only displays this payment method for orders with a value higher than the entered amount.
10. **Maximum Amount** - (optional) Only displays this payment method for orders with a value lower than the entered amount.
11. **Restrict Payment to Countries** - (optional) Select all countries or only specific countries. Leave it blank to allow all countries.
12. **Payment from Specific Countries** - (optional) Only displays this payment method for orders with shipping destinations within the selected countries. Leave it blank to allow all countries.
13. **Sort Order** - (optional) Orders the payment methods on the checkout page in ascending order. The lower the number, the higher the priority.

Click on Save to save the changes.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/configurationMbway.png)
</br>


## Credit Card

The Credit Card payment method allows customers to pay with Visa or Mastercard credit cards through the Ifthenpay gateway.
The Ccard Keys are automatically loaded when entering the Backoffice Key.
Configure the payment method. The image below shows an example of a minimally functional configuration.


1. **Enabled** - When selected as Yes, it activates the payment method, displaying it on the checkout page of your store.
2. **Title** - The title that appears to the consumer at checkout, in case you choose not to display the icon.
3. **Display Icon** - When selected as Yes, it displays the payment method's icon at checkout.
4. **Activate Callback** - When selected as Yes, the order status will be updated when payment is received.
5. **Ccard Key** - Select an Credit Card key. You can only select one of the keys associated with the Backoffice Key.
6. **Send Invoice Email** - When selected as Yes, the consumer automatically receives an email with the order invoice when payment is received.
7. **Allow Refunds** - When selected as Yes, it displays a button on the credit note page that allows an administrator of the online store to refund the amount paid by the consumer.
8. **Minimum Amount** - (optional) Only displays this payment method for orders with a value higher than the entered amount.
9. **Maximum Amount** - (optional) Only displays this payment method for orders with a value lower than the entered amount.
10. **Restrict Payment to Countries** - (optional) Select all countries or only specific countries. Leave it blank to allow all countries.
11. **Payment from Specific Countries** - (optional) Only displays this payment method for orders with shipping destinations within the selected countries. Leave it blank to allow all countries.
12. **Sort Order** - (optional) Orders the payment methods on the checkout page in ascending order. The lower the number, the higher the priority.

Click on Save to save the changes.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/configurationCCard.png)
</br>


## Payshop

The Payshop payment method generates a reference that can be paid at any Payshop agent or affiliated store.
The Payshop keys are automatically loaded upon entering the Backoffice Key.
Configure the payment method. The image below shows an example of a minimally functional configuration.

1. **Enabled** - When selected as Yes, it activates the payment method, displaying it on the checkout page of your store.
2. **Title** - The title that appears to the consumer at checkout, in case you choose not to display the icon.
3. **Display Icon** - When selected as Yes, it displays the payment method's icon at checkout.
4. **Activate Callback** - When selected as Yes, the order status will be updated when payment is received.
5. **Payshop Key** - Select a Payshop key. You can only select one of the keys associated with the Backoffice Key.
6. **Deadline** - Select the number of days for the Payshop reference. Choose a value between 1 and 99 days. Leave it blank if you do not want it to expire.
7. **Send Invoice Email** - When selected as Yes, the consumer automatically receives an email with the order invoice when payment is received.
8. **Minimum Amount** - (optional) Only displays this payment method for orders with a value higher than the entered amount.
9. **Maximum Amount** - (optional) Only displays this payment method for orders with a value lower than the entered amount.
10. **Restrict Payment to Countries** - (optional) Select all countries or only specific countries. Leave it blank to allow all countries.
11. **Payment from Specific Countries** - (optional) Only displays this payment method for orders with shipping destinations within the selected countries. Leave it blank to allow all countries.
12. **Sort Order** - (optional) Orders the payment methods on the checkout page in ascending order. The lower the number, the higher the priority.

Click on Save to save the changes.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/configurationPayshop.png)
</br>


## Cofidis Pay

The Cofidis Pay payment method that allows the consumer to pay in installments.
The Cofidis Pay keys are automatically loaded upon entering the Backoffice Key.
Configure the payment method. The image below shows an example of a minimally functional configuration.

1. **Enabled** - When selected as Yes, it activates the payment method, displaying it on the checkout page of your store.
2. **Title** - The title that appears to the consumer at checkout, in case you choose not to display the icon.
3. **Display Icon** - When selected as Yes, it displays the payment method's icon at checkout.
4. **Activate Callback** - When selected as Yes, the order status will be updated when payment is received.
5. **Cofidis Pay Key** - Select a Cofidis Pay key. You can only select one of the keys associated with the Backoffice Key.
6. **Send Invoice Email** - When selected as Yes, the consumer automatically receives an email with the order invoice when payment is received.
7. **Minimum Amount** - (optional) Only displays this payment method for orders with a value higher than the entered amount. **Important Notice:** On Cofidis Key selection, this input is updated with value configured in ifthenpay's backoffice, and when editing, it can not be less then the value specified in ifthenpay's backoffice.;
8. **Maximum Amount** - (optional) Only displays this payment method for orders with a value lower than the entered amount. **Important Notice:** On Cofidis Key selection, this input is updated with value configured in ifthenpay's backoffice, and when editing, it can not be greater then the value specified in ifthenpay's backoffice.;
9. **Restrict Payment to Countries** - (optional) Select all countries or only specific countries. Leave it blank to allow all countries.
10. **Payment from Specific Countries** - (optional) Only displays this payment method for orders with shipping destinations within the selected countries. Leave it blank to allow all countries.
11. **Sort Order** - (optional) Orders the payment methods on the checkout page in ascending order. The lower the number, the higher the priority.

Click on Save to save the changes.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/configurationCofidis.png)
</br>


## Pix

The Pix payment method that allows the consumer to pay in installments.
The Pix keys are automatically loaded upon entering the Backoffice Key.
Configure the payment method. The image below shows an example of a minimally functional configuration.

1. **Enabled** - When selected as Yes, it activates the payment method, displaying it on the checkout page of your store.
2. **Title** - The title that appears to the consumer at checkout, in case you choose not to display the icon.
3. **Display Icon** - When selected as Yes, it displays the payment method's icon at checkout.
4. **Activate Callback** - When selected as Yes, the order status will be updated when payment is received.
5. **Pix Key** - Select a Pix key. You can only select one of the keys associated with the Backoffice Key.
6. **Send Invoice Email** - When selected as Yes, the consumer automatically receives an email with the order invoice when payment is received.
7. **Minimum Amount** - (optional) Only displays this payment method for orders with a value higher than the entered amount.
8. **Maximum Amount** - (optional) Only displays this payment method for orders with a value lower than the entered amount.
9.  **Restrict Payment to Countries** - (optional) Select all countries or only specific countries. Leave it blank to allow all countries.
10. **Payment from Specific Countries** - (optional) Only displays this payment method for orders with shipping destinations within the selected countries. Leave it blank to allow all countries.
11. **Sort Order** - (optional) Orders the payment methods on the checkout page in ascending order. The lower the number, the higher the priority.

Click on Save to save the changes.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/configurationPix.png)
</br>


## Ifthenpay Gateway

The Ifthenpay Gateway payment method allows the consumer to be redirected to a payment gateway page where it is possible to select any of the above payment methods to pay for the order. 
The Ifthenpay Gateway Keys are automatically loaded upon entering the Backoffice Key. 
Configure the payment method. The image below shows an example of a minimally functional configuration.

1. **Enabled** - When selected as Yes, it activates the payment method, displaying it on the checkout page of your store.
2. **Activate Callback** - When selected as Yes, the order status will be updated when payment is received.
3. **Ifthenpay Gateway Key** - Select a Ifthenpay Gateway key. You can only select one of the keys associated with the Backoffice Key.
4. **Payment Methods** - Select a Payment Method Key per each Method and check the checkbox if you want to display it in the gateway page.
5. **Default Payment Method** - Select a Payment Method that will be selected in the gateway page by default.
6. **Deadline** - Select the number of days to deadline for the gateway page link. From 1 to 99 days, leave empty if you don't want it to expire.
7. **Display Icon** - Display this payment method logo image on checkout, choose from 3 options:
    -  Default: displays ifthenpay gateway logo;
    -  Title: displays Payment Method Title;
    -  Composite: displays a composite image of all the payment method logos you have selected;
8. **Title** - The title that appears to the consumer during checkout.
9. **Gateway Close Button Text** - Text displayed in the "Return to Shop" button in the gateway page;
10. **Send Invoice Email** - When selected as Yes, the consumer automatically receives an email with the order invoice when payment is received.
11. **Minimum Amount** - (optional) Only displays this payment method for orders with a value higher than the entered amount.
12. **Maximum Amount** - (optional) Only displays this payment method for orders with a value lower than the entered amount.
13. **Restrict Payment to Countries** - (optional) Select all countries or only specific countries. Leave it blank to allow all countries.
14. **Payment from Specific Countries** - (optional) Only displays this payment method for orders with shipping destinations within the selected countries. Leave it blank to allow all countries.
15. **Sort Order** - (optional) Orders the payment methods on the checkout page in ascending order. The lower the number, the higher the priority.
Click on Save to save the changes.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/configurationIfthenpaygateway.png)
</br>


## Refund

The MB WAY and Credit Card payment methods allow for the refund of the total or partial amount paid by the consumer through the order credit note page.
To refund the amount paid by the consumer, it is necessary for the payment method to have the "Allow Refunds" option enabled, and order must have an invoice.
To proceed with the refund of the amount paid by the consumer, access the order page.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/goOrders.png)
</br>

Access the order details (1).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/orderDetails.png)
</br>

Click on Invoices (1) and then on View Details (2).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/goInvoice.png)
</br>

Click on Credit Memo (1).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/refundCreditMemo.png)
</br>

It is possible to edit the refund amount (1) and click on Update (2), or proceed with the refund of the total amount paid by the consumer by clicking on Refund (3).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/refundPage.png)
</br>

Confirm the refund amount and click on OK (1).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/confirmRefund.png)
</br>

An email with a security token will be sent to the email of the online store's administrator user who initiated the refund.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/emailRefund.png)
</br>

Enter the security token received in the email (1) and click on OK (2).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/confirmToken.png)
</br>

The selected amount will be refunded to the consumer, and the order status will be updated to closed.

## Multistore


The Ifthenpay module is compatible with the multistore mode of Magento 2, allowing you to configure different payment methods for each store.
This functionality is applied at the website scope, allowing you to configure different payment methods for each website.

To configure different payment methods for each store, access the module's configuration page and select the desired website in the top left corner (1).

IMPORTANT: When implementing multistore, you should not configure the Default Config, as it will override the websites of the sub-stores. It is only possible to configure different payment methods for each website, and it is not possible to configure different payment methods for each Store View.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/multiStoreScope.png)
</br>


# Other
  
 ## Request creation of aditional account

If you already have an Ifthenpay account but have not contracted a payment method that you now need, you can make an automatic request to Ifthenpay.

To request the creation of an additional account, access the module's configuration page and click on "Request New Account" for the payment method you wish to contract.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/requesNewAccount.png)
</br>

In case you need an account for Multibanco with Dynamic References, the "Request New Account" button will be available within the configuration of the Multibanco payment method (1).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/requestMultibancoDynamic.png)
</br>

By clicking on Request New Account, a dialog box will appear where you can confirm the action by clicking on OK (1).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/requestAccountConfirm.png)
</br>

You may also request a method for the Ifthenpay Gateway following the same procedure by clicking any of the request buttons (1). 

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/request_gateway_paymen_method.png)
</br>

As a result, the Ifthenpay team will add the payment method to your account, updating the list of available payment methods in your module.

IMPORTANT: When requesting an account for the Credit Card payment method, the Ifthenpay team will contact you to request more information about your online store and your business before activating the payment method.



## Reset Configuration

If you have acquired a new Backoffice Key and want to assign it to your website but already have one currently assigned, you can reset the module's configuration. In the Ifthenpay module configuration, click on the "Clear Backoffice Key" button (1) and confirm the action by clicking OK.

**Attention, this action will clear the current module settings**;

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/clearBackofficeKey.png)
</br>

After clearing the Backoffice Key, you will be prompted to enter the Backoffice Key again.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/afterClearBackofficeKey.png)
</br>


## Callback

IMPORTANT: The credit card method only uses the callback as a fallback, in case a user fails to be redirected back after completing the payment in the gateway page.

Callback is a feature that, when enabled, allows your store to receive a notification of a successful payment. When enabled, upon receiving a successful payment for an order, the Ifthenpay server communicates with your store, changing the order status to "Processing". You can use Ifthenpay payments without enabling Callback, but your orders will not be automatically updated with the status change.

As mentioned above in Configurations, to enable Callback, access the module's configuration page and enable the "Enable Callback" option. After saving the settings, the process of associating your store and payment method with Ifthenpay servers will be executed, and a new element (for informational purposes only) will be displayed, showing the Callback status (1), the anti-phishing key (2), and the Callback URL (3).

After enabling Callback, you don't need to take any further action. Callback is active and functioning.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/callbackElement.png)
</br>


## Cronjob

A cron job is a scheduled task that is automatically executed at specific intervals in the system. The Ifthenpay module provides a cron job to check the status of payments and cancel orders that have not been paid within the configured time limit. The table below shows the time limit for each payment method, which the cron job checks and cancels orders that have not been paid within the time limit. This time limit can only be configured for the Multibanco with Dynamic References and Payshop payment methods.
Cofidis Pay does not have this functionality because its approval time is not fixed.

| Payment Method     | Payment Deadline           |
|--------------------|----------------------------|
| Multibanco         | no deadline                |
| Multibanco Dynamic | Configurable, 1 to n days  |
| MB WAY             | 30 minutes                 |
| Payshop            | Configurable, 1 to 99 days |
| Credit Card        | 30 minutes                 |
| Cofidis Pay        | 60 minutes                 |
| Pix                | 30 minutes                 |

The order cancellation cronjob runs every minute. The configuration options for the cronjob can be found on the Magento cronjobs settings page under the ifthenpay_payment group.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/cronjobConfiguration.png)
</br>

To start the execution of the cronjob, please access the Magento terminal and run the following command:

```bash
bin/magento cron:run --group ifthenpay_payment
```

## Logs

To facilitate error detection, the Ifthenpay module logs any errors that occur during its execution. The logs are then saved in a text file in the var/log/ directory of Magento. To access the logs, navigate to the var/log/ folder in the root of Magento and open the file ifthenpay.log.

# Consumer User Experience

The following describes the consumer user experience when using Ifthenpay payment methods in a stock installation of Magento, which may change with the addition of one-page checkout extensions.

On the checkout page, after selecting the shipping method, the consumer can choose the payment method.

## Pay order with Multibanco

Select the Multibanco payment method (1) and click on Place Order (2).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/checkoutMultibanco.png)
</br>

The order success page will be displayed, showing the entity, reference, and the amount to be paid.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouMultibanco.png)
</br>

If the Multibanco payment method is configured with dynamic references, the order success page will also display the reference deadline in addition to the entity, reference, and the amount to be paid.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouMultibancoDynamic.png)
</br>


## Pay order with Payshop

Select the Payshop payment method (1) and click on Place Order (2).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/checkoutPayshop.png)
</br>

The order success page will be displayed, showing the reference, validity, and the amount to be paid.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouPayshop.png)
</br>



## Pay order with MB WAY

Select the MB WAY payment method (1), fill in the mobile phone number (2), and click on Place Order (3).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/checkoutMbway.png)
</br>

If the Display Countdown configuration is active, the order success page will display a countdown timer showing the remaining time for payment.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouMbwayCountDown.png)
</br>

The countdown timer will automatically update the payment status in the case of success, rejection (by the MB WAY app user), expiration of the time limit, or error.

In case of success, a success message will be displayed.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouSuccess.png)
</br>

In case of rejection by the user, a rejection message will be displayed.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouRejected.png)
</br>

In case of expiration of the time limit, an expiration message will be displayed.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouTimeOut.png)
</br>

In case of failure to communicate with the MB WAY app or entering an invalid mobile phone number, an error message will be displayed.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouError.png)
</br>

When an error occurs, the time limit is reached, or the payment is declined in the MB WAY app, the consumer can try again by clicking on Resend MB WAY Notification.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouMbwayResend.png)
</br>

If the configuration of the MB WAY payment method has the option to not display the countdown active, the consumer will receive a notification in the MB WAY app, but the countdown and the button to resend the notification will not be displayed on the order success page.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouMbwayNoCountDown.png)
</br>


## Pay order with credit card

Select the Credit Card payment method (1) and click on Place Order (2).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/checkoutCcard.png)
</br>

Fill in the credit card details: card number (1), expiration date (2), security code (3), Cardholder's Name (4), and click on Pay (5).

It is possible to go back (6), canceling the payment.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/ccardGateway.png)
</br>

After the payment is processed, the order success page will be displayed.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouCcard.png)
</br>


## Pay order with credit card

Select the Credit Card payment method (1) and click on Place Order (2).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/checkoutCcard.png)
</br>

Fill in the credit card details: card number (1), expiration date (2), security code (3), Cardholder's Name (4), and click on Pay (5).

It is possible to go back (6), canceling the payment.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/ccardGateway.png)
</br>

After the payment is processed, the order success page will be displayed.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouCcard.png)
</br>


## Pay order with Cofidis Pay

Select the Cofidis Pay payment method (1) and click on Place Order (2).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/checkoutCofidis.png)
</br>

* Login or, if you don't have an account, sign up with Cofidis Pay:
1. Click "Avançar" to sign up with Cofidis Pay;
2. Or if you have a Cofidis Pay account, fill in your access credentials and click enter;
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/cofidis_payment_1.png)
</br>

* Number of installments and billing and personal data:
1. Select the number of installments you wish;
2. Verify the summary of the the payment plan;
3. Fill in your personal and billing data;
4. Click "Avançar" to continue;
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/cofidis_payment_2.png)
</br>

* Terms and Conditions:
1. Select "Li e autorizo" to agree with terms and conditions;
2. Click "Avançar"
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/cofidis_payment_3.png)
</br>

* Agreement formalization:
1. Click "Enviar código";
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/cofidis_payment_4.png)
</br>

* Agreement formalization authentication code:
1. Fill in the code you received on your phone;
2. Click "Confirmar código";
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/cofidis_payment_5.png)
</br>

* Summary and Payment:
1. Fill in your credit card details (number, expiration date and CW), and click "Validar";
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/cofidis_payment_6.png)
</br>

* Success and return to store:
1. Click the return icon to return to the store;
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/cofidis_payment_7.png)
</br>

* After which you will be redirected back to the store;
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouCofidis.png)
</br>


## Pay order with Pix

Select the Pix payment method (1), fill in the name, CPF, and Email (2)(address related fields are optional), and click on Place Order (3).
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/checkoutPix.png)
</br>

* Proceed with payment with one of two options:
1. Reading QR code with mobile phone;
2. Copy the Pix code and pay with online banking;
**Important Note:** In order to be redirected back to the store after paying, this page must be left open. If closed the consumer will still be able to pay, as long as he has already read the Pix code, he will only not be redirected back to the store.
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/pix_payment_1.png)
</br>

After the payment is processed, the order success page will be displayed.
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouPix.png)
</br>


## Pay order with Ifthenpay Gateway

Select the Ifthenpay Gateway payment method (1) and click on Place Order (2).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/checkoutIfthenpaygateway.png)
</br>

Select one of the payment methods available in the gateway page (1). 
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/ifthenpaygateway_payment_1.png)
</br>

In case of Multibanco method, the entity, reference and amount will be displayed.
Here the user can do one of the two:
 - in case of an offline payment method, note down the payment details, click the close gateway button (2) and pay later;
 - pay at that moment and click the confirm payment button (3) to verify the payment.
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/ifthenpaygateway_payment_2.png)
</br>

If the user did not pay at the moment and did not take note of the payment details, it is also possible to access the Ithenpay Gateway link at a later date in the user account order history or order confirmation email.
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/ifthenpaygateway_payment_3.png)
</br>


You made it to the end of the ifthenpay magento 2 module manual. Congratulations!
