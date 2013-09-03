LittleBigJoe
============
This project contains all necessary files used by the LittleBigJoe project.

## Prerequisites
This version uses Symfony 2.3+, PHP 5.4.

## Installation
Installation is a quick (I promise!) 7 step process:

1) Get the sources

2) Run composer update

3) Create database

4) Create database triggers 

5) Empty Symfony2 caches

6) Create admin account

7) Start using the app !

### Step 1: Get the sources
Get the sources from this GITHUB project, to retrieve all the files necessary to run the project.

### Step 2 : Run composer update
This project uses multiple vendors packages, so you need to get them via Composer. Run the command : 

``` bash
$ php composer.phar update
```

### Step 3 : Create database
This project uses a database to stock projects, users, categories, etc..., so you need to create the database by running this command : 

``` bash
$ php app/console doctrine:schema:update --force
```

### Step 4 : Create database triggers
In a way to accelerate FO rendering, some database are required. Please launch this SQL request in your database manager : 

``` sql
delimiter $$
CREATE TRIGGER `after_insert_project_like`
    AFTER INSERT ON `project_like` FOR EACH ROW
    BEGIN
       UPDATE `project`
       SET likes_count = likes_count + 1 
       WHERE id = NEW.project_id;
    END;
$$
delimiter $$
CREATE TRIGGER `after_insert_project_contribution`
    AFTER INSERT ON `project_contribution` FOR EACH ROW
    BEGIN
       UPDATE `project`
       SET amount_count = amount_count + NEW.mangopay_amount
       WHERE id = NEW.project_id;
    END;
$$
```

### Step 5 : Empty Symfony2 caches
To make sure everything is alright, run the following commands : 

``` bash
$ php app/console cache:clear
$ php app/console cache:clear --env=prod
```

### Step 6 : Create admin account
If you want to access to the project administration, you'll need an admin account.
There\'s two ways to get this admin account.

#### First way
1) Go the FO register page (http://your.domain.tld/register/).

2) Fill the form and create your account

3) Promote your user account with the following command : 

``` bash
$ php app/console fos:user:promote youremail@yourdomain.tld ROLE_ADMIN
```

4) Access administration via : http://your.domain.tld/admin/

Email : **youremail@yourdomain.tld**

Password : **yourpassword**

#### Second way (lazy one)
1) Go to your database manager and execute the following SQL query : 

``` sql
INSERT INTO `user` (`firstname`, `lastname`, `birthday`, `facebook_url`, `twitter_url`, `google_url`, `website_url`, `city`, `country`, `nationality`, `default_language`, `photo`, `bio`, `ip_address`, `person_type`, `mangopay_user_id`, `mangopay_created_at`, `mangopay_updated_at`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `locked`, `expired`, `expires_at`, `confirmation_token`, `password_requested_at`, `roles`, `credentials_expired`, `credentials_expire_at`)
VALUES ('Admin', 'Account', '2013-09-01 00:00:00', NULL, NULL, NULL, NULL, 'Paris', 'FR', 'French', 'fr', NULL, NULL, '127.0.0.1', 'NATURAL_PERSON', 0, '2013-09-01 00:00:00', '2013-09-01 00:00:00', 'admin@littlebigjoe.com', 'admin@littlebigjoe.com', 'admin@littlebigjoe.com', 'admin@littlebigjoe.com', 1, 'p99iito6r40w4csok480skko80kwwgc', 'vrCDHIUTHHOccqX4FAYneGjwn+UYonW2Wlhr3DPR8wTipwxonnM9bclkAUcjX8gdL2LtUKBW8dox7R7bS5q9/Q==', '2013-09-01 00:00:00', 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:10:"ROLE_ADMIN";}', 0, NULL);
```

2) Access administration via : http://your.domain.tld/admin/

Email : **admin@littlebigjoe.com**

Password : **admin**

### Step 7 : Start using the app !
Enjoy !

## Configuration

### Translations
To generate .yml translations files for the application (routes, validators, strings), please use the following command : 

``` bash
$ php app/console translation:extract fr --enable-extractor=jms_i18n_routing --bundle=LittleBigJoeFrontendBundle
```

It will generate and save these files in /src/LittleBigJoe/FrontendBundle/Resources/translations folder.
