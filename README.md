A simple ecommerce site built using Symfony.

Additional tools used include Docker, automated testing, static analysis 
and continuous integration.

Products have attributes split across 3 tables with the interface designed
to allow users to easily select multiple variations of the same product where available.\
The Shop controller is the most complex part of the site featuring product listings
filtered by multiple categories, brands and colours as well as sorting, pagination
and searches.

For demonstration purposes there are more than 60 test products provided.

**Environment variables**

You will need an 
[Authorize.net payment processor sandbox account](https://developer.authorize.net/hello_world/sandbox.html) 
to complete the checkout process.\
However everything else should work using the defaults.

To use your Authorize.net account update the following lines in .env:

```
AUTHDOTNET_LOGIN_ID=$yourLoginId
AUTHDOTNET_TRANS_ID=$yourTransactionId
```

**Download dependencies**

```
composer install
```

**Start Apache, PHP & MySQL containers**

```
docker-compose up -d
```

**Create database schema and load test data**

```
bin/console doctrine:database:create
bin/console doctrine:migration:migrate -n
bin/console doctrine:fixtures:load -n
```
**Login**

You can browse the site as a guest and add products to your cart.\
To checkout you will need to login.

```
Email: user@user.com
Password: pass
Email: admin@admin.com
Password: pass
```