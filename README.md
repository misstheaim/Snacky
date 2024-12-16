# Snacky

## Application for managing food products and cookies inside the company. 

</br>


**- How to install project on a local machine**

Firtst of all clone git repository to a local folder:

```
git clone https://github.com/misstheaim/Snacky
```

If you are interested in testing and perfomance managing tools you can switch to the testing branch:

```
git checkout origin/testing
```

This is a laravel project buit on php **8.3, laravel 11** so you will need these dependencies been installed</br>
> [!IMPORTANT]
> After git cloning you will need to `cd Snacky` because the project itself located in the Snacky folder and the root folder is for Docker setuping</br>

Next run:

```
composer install
```

or if you run it on production:

```
composer install --no-dev
```

Then you will need to set up default laravel project:

```
cp .env.example .env
```

Set up your database into the .env file and run:

```
php artisan migrate
php artisan seed
php artisan seed VerifySeeder
php artisan key:generate
```

Now you can access application via next accounts:
+ admin@admin.com password: admin
+ manager@manager.com password: manager
+ developer@developer.com password: developer

</br>
Well done the app is ready!
</br>

> [!WARNING]
> If you switched to the testing branch before every commit will run setup of checking commands ( PhpStan, Laravel Pint, CodeSniffer, Enlightn), so it may take some time.</br>


**- How to install project using Docker**

After clonining git repository in the root folder run:

```
docker compose up -d
docker exec -it php_con sh
```

And in this terminal you can work with the application like any other laravel project, for example continue installation from the `composer install` step from the previous section


> [!TIP]
> There is few commands implemented in the app</br>
> + `php artisan receive-categories` - to fill categories
> + `php artisan update-snacks` - to update all added snacks (!Note may take much time)
