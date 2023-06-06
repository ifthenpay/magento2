# Ifthenpay Magento 2 payment module.

Ler em ![Português](https://github.com/ifthenpay/magento2/raw/assets/assets/img/pt.png) [Português](readme.pt.md), e ![Inglês](https://github.com/ifthenpay/magento2/raw/assets/assets/img/en.png) [Inglês](readme.md)

[1. Introduction](#Introduction)

[2. Compatibility](#Compatibility)

[3. Installation](#Installation)
  * [Installation using composer](#Installation-using-composer)
  * [Manual instalation](#Manual-instalation)

[4. Configuration](#Configuration)
  * [Backoffice Key](#Backoffice-Key)
  * [Multibanco](#Multibanco)
  * [Multibanco with Dynamic References](#Multibanco-with-Dynamic-References)
  * [MB WAY](#MB-WAY)
  * [Credit Card](#Credit-Card)
  * [Payshop](#Payshop)

[5. Refund](#Refund)

[6. Multistore](#Multistore)

[7. Other](#Other)
  * [Request creation of aditional account](#Request-creation-of-aditional-account)
  * [Reset Configuration](#Reset-Configuration)
  * [Callback](#Callback)
  * [Cronjob](#Cronjob)
  * [Logs](#Logs)


[8. Consumer User Experience](#Consumer-User-Experience)
  * [Pay order with Multibanco](#Pay-order-with-Multibanco)
  * [Pay order with Payshop](#Pay-order-with-Payshop)
  * [Pay order with MB WAY](#Pay-order-with-MB-WAY)
  * [Pay order with Credit Card](#Pay-order-with-credit-card)




# Introduction
![Ifthenpay](https://ifthenpay.com/images/all_payments_logo_final.png)

**This is the Ifthenpay plugin for the Magento 2 e-commerce platform.**

**Multibanco** is one Portuguese payment method that allows the customer to pay by bank reference.
This module will allow you to generate a payment Reference that the customer can then use to pay for his order on the ATM or Home Banking service. This plugin uses one of the several gateways/services available in Portugal, IfthenPay.

**MB WAY** is the first inter-bank solution that enables purchases and immediate transfers via smartphones and tablets.

This module will allow you to generate a request payment to the customer mobile phone, and he can authorize the payment for his order on the MB WAY App service. This module uses one of the several gateways/services available in Portugal, IfthenPay.

**Payshop** is one Portuguese payment method that allows the customer to pay by payshop reference.
This module will allow you to generate a payment Reference that the customer can then use to pay for his order on the Payshop agent or CTT. This module uses one of the several gateways/services available in Portugal, IfthenPay.

**Credit Card** 
This module will allow you to generate a payment by Visa or Master card, that the customer can then use to pay for his order. This module uses one of the several gateways/services available in Portugal, IfthenPay.

**Contract with Ifthenpay is required.**

See more at [Ifthenpay](https://ifthenpay.com). 

Membership at [Membership Ifthenpay](https://www.ifthenpay.com/aderir/).

**Support**

For support, please create a support ticket at [Support Ifthenpay](https://helpdesk.ifthenpay.com/).









# Compatibility

Use the table below to check the compatibility of the Ifthenpay module with your online store:
|                            | magento 2.3    | magento 2.4 [2.4.0 - 2.4.6] |
|----------------------------|----------------|-----------------------------|
| Ifthenpay v1.0.0 - v1.2.13 | Não compatível | Compatível até 2.4.5        |
| Ifthenpay v2.0.0           | Não compatível | Compatível                  |







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

## Manual instalation

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
4. **Ccard Key** - Select an Credit Card key. You can only select one of the keys associated with the Backoffice Key.
5. **Send Invoice Email** - When selected as Yes, the consumer automatically receives an email with the order invoice when payment is received.
6. **Allow Refunds** - When selected as Yes, it displays a button on the credit note page that allows an administrator of the online store to refund the amount paid by the consumer.
7. **Minimum Amount** - (optional) Only displays this payment method for orders with a value higher than the entered amount.
8. **Maximum Amount** - (optional) Only displays this payment method for orders with a value lower than the entered amount.
9. **Restrict Payment to Countries** - (optional) Select all countries or only specific countries. Leave it blank to allow all countries.
10. **Payment from Specific Countries** - (optional) Only displays this payment method for orders with shipping destinations within the selected countries. Leave it blank to allow all countries.
11. **Sort Order** - (optional) Orders the payment methods on the checkout page in ascending order. The lower the number, the higher the priority.

Click on Save to save the changes.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/configurationCCard.png)
</br>


## Payshop

O método de pagamento Payshop, gera uma referência que pode ser paga em qualquer agente payshop ou loja aderente.
As Chaves Payshop  são carregadas automáticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

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

As a result, the Ifthenpay team will add the payment method to your account, updating the list of available payment methods in your module.

IMPORTANT: When requesting an account for the Credit Card payment method, the Ifthenpay team will contact you to request more information about your online store and your business before activating the payment method.



## Reset Configuration

If you have acquired a new Backoffice Key and want to assign it to your website but already have one currently assigned, you can reset the module's configuration. In the Ifthenpay module configuration, click on the "Clear Backoffice Key" button (1) and confirm the action by clicking OK.

**Atenção, esta ação irá limpar as atuais configurações do módulo**;

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/clearBackofficeKey.png)
</br>

After clearing the Backoffice Key, you will be prompted to enter the Backoffice Key again.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/afterClearBackofficeKey.png)
</br>


## Callback

IMPORTANT: Only the Multibanco, MB WAY, and Payshop payment methods allow enabling Callback. The credit card method automatically changes the order status.

Callback is a feature that, when enabled, allows your store to receive a notification of a successful payment. When enabled, upon receiving a successful payment for an order, the Ifthenpay server communicates with your store, changing the order status to "Processing". You can use Ifthenpay payments without enabling Callback, but your orders will not be automatically updated with the status change.

As mentioned above in Configurations, to enable Callback, access the module's configuration page and enable the "Enable Callback" option. After saving the settings, the process of associating your store and payment method with Ifthenpay servers will be executed, and a new element (for informational purposes only) will be displayed, showing the Callback status (1), the anti-phishing key (2), and the Callback URL (3).

After enabling Callback, you don't need to take any further action. Callback is active and functioning.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/callbackElement.png)
</br>


## Cronjob

Um cronjob é uma tarefa programada que é executada automaticamente em intervalos específicos no sistema. O módulo Ifthenpay disponibiliza um cronjob para verificar o estado dos pagamentos, e cancelar encomendas que não foram pagas dentro do tempo limite configurado. A tabela abaixo mostra o tempo limite para cada método de pagamento, o qual o cronjob verifica e cancela as encomendas que não foram pagas dentro do tempo limite. Este tempo limite pode ser configurado apenas para o método de pagamento Multibanco com Referências Dinâmicas e Payshop.

A cron job is a scheduled task that is automatically executed at specific intervals in the system. The Ifthenpay module provides a cron job to check the status of payments and cancel orders that have not been paid within the configured time limit. The table below shows the time limit for each payment method, which the cron job checks and cancels orders that have not been paid within the time limit. This time limit can only be configured for the Multibanco with Dynamic References and Payshop payment methods.

| Payment Method     | Payment Deadline           |
|--------------------|----------------------------|
| Multibanco         | no deadline                |
| Multibanco Dynamic | Configurable, 1 to n days  |
| MB WAY             | 30 minutes                 |
| Payshop            | Configurable, 1 to 99 days |
| Credit Card        | 30 minutes                 |

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

You made it to the end of the ifthenpay magento 2 module manual. Congratulations!
