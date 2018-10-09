Quizzle Demo Application v1.0.1
========================
[![Symfony](https://img.shields.io/badge/Powered_by-Symfony_Framework-green.svg?style=flat)](https://symfony.com/)

**WARNING**: This is only a demo application! **Viewer discretion is advised.** 

## What's inside?

**Quizzle** is a fast and simple lottery site with several games and some base functions: 

  * Registration, Authentication and Restore password;

  * Several popular games types with real prizes, cash prizes and bonus scores;

  * Admin interface to control base entities and operations;

  * Swiftmailer as a primary mailing engine;

  * Lightweight and pretty responsive template;
  
  * ... and many-many-many other cool stuff.

## Quick start

You don't need to clone whole project if you only wanna see how it works

Just visit our [**DEMO SITE**](https://www.quizzle.tk/) to check this out!

Default users credentials:

```
Admin user
 - Login: adminuser
 - Password: adminpassword

Test user
 - Login: testuser
 - Password: testpassword
```

...or register new one with registration form!
No email confirmation needed!
  
## Database Model

![](https://clip2net.com/clip/m0/e709f-clip-101kb.png?nocache=1)

## ER Model

```
Too many stuff;
Will be added later.
```

## Console commands

To make batch withdrawal you can use following command:
```
php bin/console withdrawal:batch <N>
```
Where `<N>` is a number of pending operations you want to proceed

How to install?
--------------

Installation is very fast and simple:

  * [**Clone**][1] git repository 

  * [**Run**][2] composer

  * Fill all prompted fields

  * [**Create**][3] database with Symfony console

  * [**Create**][4] fist Superadmin user with Symfony console

  * [**Configure**][5] your webserver document root and other stuff

All libraries and bundles included in the Symfony Standard Edition are
released under the MIT or BSD license.

Enjoy!

[1]:  https://git-scm.com/book/it/v2/Git-Basics-Getting-a-Git-Repository
[2]:  https://getcomposer.org/doc/01-basic-usage.md
[3]:  https://symfony.com/doc/current/doctrine.html
[4]:  https://symfony.com/doc/2.0/bundles/FOSUserBundle/command_line_tools.html
[5]:  https://symfony.com/doc/3.4/setup/web_server_configuration.html
