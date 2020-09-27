**Set environment variables**

You will need a authorize.net sandbox login & transaction ID to complete the checkout process.\
If you don't want to do this, everything bar checkout should work using the defaults.

To set your sandbox values update these lines in .env:

```
AUTHDOTNET_LOGIN_ID=YOURLOGINID
AUTHDOTNET_TRANS_ID=YOURTRANSACTION_ID
```

[Authorize.net sandbox signup](https://developer.authorize.net/hello_world/sandbox.html)

**Download Composer dependencies**

```
composer install
```

**Start Docker containers**

```
docker-compose up -d
```

**Create database and load fixtures**

```
bin/console doctrine:database:create
bin/console doctrine:migration:migrate -n
bin/console doctrine:fixtures:load -n
```
**Login**

You can browse the site as a guest and add products to your cart. However you will need to login to checkout.

```
Email: user@user.com
Password: pass
```