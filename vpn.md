Create a complete VPN Management Module in Laravel.

The system will have VPN category/name management, VPN account management, purchase functionality, and user purchase history.

Admin can create, update, and delete VPN names. Admin can also add and modify VPN accounts with VPN name, expiry date, username, password, access link, and status.

Users can purchase available VPN accounts and view their purchase history from their dashboard.

Required tables:

`vpn_names`

* id
* name
* status
* timestamps

`vpns`

* id
* vpn_name_id
* expire_date
* user_name
* password
* link
* status
* timestamps

Please follow the existing Laravel project structure, route style, controller pattern, validation style, permission system, and UI design. Make the module clean, responsive, and easy to manage.
