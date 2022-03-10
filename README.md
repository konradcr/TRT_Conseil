# TRT Conseil
***
![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?logo=css3&logoColor=white)
![Bootstrap](https://img.shields.io/badge/bootstrap-%23563D7C.svg?logo=bootstrap&logoColor=white)
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/symfony-%23000000.svg?logo=symfony&logoColor=white)
![PhpStorm](https://img.shields.io/badge/phpstorm-143?logo=phpstorm&logoColor=black&color=black&labelColor=darkorchid)
[![Heroku App Status](http://heroku-shields.herokuapp.com/trt-conseil-recruiting)](https://trt-conseil-recruiting.herokuapp.com)

## Getting Start - Local Deployment 
### Clone the repository
```
git clone https://github.com/konradcr/TRT_Conseil
```
### Navigate to the repository
```
cd TRT_Conseil
```
### Install dependencies
```
composer install
```
### Create the database
Into the .env file modify the database url with your database :
```
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
```
Then, create the database and do the migrations :
```
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```
### Launch the server
```
symfony server:start
```

***
## Getting Start - Heroku Deployment
Make sure that you have installed Heroku CLI and that you committed the project on git.
### Connect your Heroku account
```
heroko login
```
### Create the Heroku project
```
heroko create
```
Then create the Procfile :
```
echo 'web: heroku-php-apache2 public/' > Procfile
```
### Environment variables
Set APP_ENV to prod :
```
heroku config:set APP_ENV=prod
```
Change the APP_SECRET in production :
```
heroku config:set APP_SECRET=$(php -r 'echo bin2hex(random_bytes(16));')
```
### Heroku Add-ons - Database & SMTP Server
Add the JawDB Maria Add-ons from Heroku to take advantage of a real database :
```
heroku addons:create jawsdb-maria:kitefin
```
Copy your database url from :
```
heroku config:get JAWSDB_MARIA_URL
```
Set your new DATABASE_URL :
```
heroku config:set DATABASE_URL=your_db_url
```
Add the Mailgun Add-ons from Heroku to take advantage of a SMTP server :
```
heroku addons:create mailgun:starter
```
Copy your SMTP username :
```
heroku config:get MAILGUN_SMTP_LOGIN
```
Copy your SMTP password :
```
heroku config:get MAILGUN_SMTP_PASSWORD
```
Set your new MAILER_DSN :
```
heroku config:set MAILER_DSN=mailgun+smtp://you_username:your_password@default?region=us
```
### Deploy to Heroku
```
git add .
git commit -m "Heroku configuration"
git push heroku master
heroku open
```
## Learn more

[Symfony](https://symfony.com)  
[Bootstrap](https://getbootstrap.com)   
[Heroku](https://heroku.com)  
[JawDB Maria](https://www.jawsdb.com/docs/)  
[Mailgun](https://www.mailgun.com/)

## License

Released under [MIT](/LICENSE) by [@konradcr](https://github.com/konradcr).