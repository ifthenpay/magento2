
the callback controller validates the request and responds with a specific error code

returns ok, fail, or warning:
 * ok is returned along with http 200 if everything executed successfully
 * warning is returned with http 200 if order already paid
 * fail is returned with 400 if there is an error processing the callback, the code refers the nature of the error

The table below shows the meaning of each error number.
NOTE: the error number is only a string, it is unrelated to the http code of the response.

| Error Number | Status  | HTTP Code | Error Description                          |
| ------------ | ------- | --------- | ------------------------------------------ |
| N/A          | OK      | 200       | Everything executed successfully           |
| N/A          | Warning | 200       | Order already paid                         |
| 10           | Fail    | 400       | StoredPaymentData not found in local table |
| 20           | Fail    | 400       | Invalid payment method                     |
| 30           | Fail    | 400       | Callback is not active                     |
| 40           | Fail    | 400       | Invalid anti-phishing key                  |
| 50           | Fail    | 400       | Order not found                            |
| 60           | Fail    | 400       | Invalid amount                             |
