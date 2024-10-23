## Installation
Fresh installation in a platform that does not yet have the ifthenpay extension.

- [x] Installation is successful;



## Uninstallation
Removing the extension form the platform.

- [x] Removal is successful and the extension is no longer selectable;



## Update
Updating the ifthenpay extension in a platform that already has ifthenpay extension installed.

- [x] When an update is available, it shows the notification for the new version update;
- [x] When an update is available, the link directs to the correct download site (normally github release page);
- [x] Update action performs without errors;



## Configuration
Extension configuration in the admin backoffice.
Some of the following checks need to be applied per payment method.

- [x] Initial ifthenpay backoffice key configuration processes without error and loads the correct (available) payment methods;
- [x] After saving a valid backoffice key, if a payment method is not available it displays a button to request said payment method;
- [x] After backoffice key configuration, the payment method configuration presents the correct default values or placeholders;
- [x] Validation of the form works as expected (does not allow invalid values to be submitted, this may vary according to payment method);
- [x] Activating callback updates the Callback URL and Anti-phishing Key on the ifthenpay server;
- [x] Activating callback displays the new Callback URL and Anti-phishing Key in the configuration page;
- [x] Clicking "Request account" sends email with data and the service to activate to the ifthenpay support email;
- [x] Clicking "Reset Accounts" clears all configuration and backoffice key;



## Pre-Checkout (client side)
Action of the application user ordering and choosing the payment before confirming.
Some of the following checks need to be applied per payment method.

- [x] If min/max, zone and other conditions are met, the payment method is displayed and can be selected;
- [x] If min/max value is not met, the payment method is NOT displayed;
- [x] If zone value is not met, the payment method is NOT displayed;



## Confirm-Checkout (cliente side)
Action of the application user confirming the payment method.
Some of the following checks need to be applied per payment method.

- [x] In case of online payment (mbway, ccard, cofidis, ifthenpaygateway), the user is directed to gateway/online payment page to proceed with payment;
- [x] In case of offline payment (multibanco, payshop), the user is directed to the "thank you" page where the payment details are displayed;



## Pos-Checkout (client side)
Actions performed after order has been created and payment process has finish, in case of offline payment methods that may mean the order is NOT yet paid.
Some of the following checks need to be applied per payment method.

- [x] Email with payment details is sent to the client email;
- [x] Backoffice order details show payment details of the selected payment method (normally as "Order History");
- [x] Frontoffice order details show payment details of the selected payment method (normally as "Order History");



## Callback
Server callback to store application when the payment has ocurred to update order state (normaly "paid").
Some of the following checks need to be applied per payment method.

- [x] Order state changes when simulating the callback of the server by calling the URL with the filled query string variables;
- [x] Callback changes state of order. Check if an order payed with a given method can process the callback from its method or ifthenpaygateway (selected method to pay x callback tha is executed):
  - [x] multibanco x multibanco
  - [x] mbway x mbway
  - [x] payshop x payshop
  - [x] cofidis x cofidis
  - [x] ccard x ccard
  - [x] ifthenpay x ifthenpay


  - [x] multibanco x ifthenpay
  - [x] mbway x ifthenpay
  - [x] payshop x ifthenpay
  - [x] cofidis x ifthenpay


  - [x] ifthenpay x multibanco
  - [x] ifthenpay x mbway
  - [x] ifthenpay x payshop
  - [x] ifthenpay x cofidis



## Cronjob
Server cronjob that runs the order cancel routine.

- [x] With the cancel cronjob running, the order state of an order which deadline has passed changes to "canceled";
- [x] With the cancel cronjob running, the order state of an order which does NOT have a deadline remain unchanged;



## Translation
Translation of PT and EN language across the extension.
It is very broad since these translations exist through all the extension.
Some of the following checks need to be applied per payment method.

- [x] There are no missing translations where it defaults to the translation keyword;
- [x] There are no translations to the wrong language (having a PT sentence when the EN language is selected);


## Other details (Payment method specific)
Finer details that are specific to each payment method

- [x] (ifthenpaygateway) The default logo is present and has not been replaced by the composite image that is generated when configuring the payment method; 

## Clean Up
Removing scafolding
- [x] There are no TODO comments left unresolved;
