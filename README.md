# ToDoList

Welcome on the ToDoList GitHub. A **Symfony 3.3** project.

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/aa0377c81f134f6a9cf767f7f3a7905f)](https://www.codacy.com/app/DamienVauchel/todolist?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=DamienVauchel/todolist&amp;utm_campaign=Badge_Grade)

## General context

This project is linked to the OpenClassRooms DA PHP/Symfony's studies. It is the 8th project in which it is asked to create a website to be able to manage a To Do List. This is the first unit tests implementation project.

## Prerequisite

* PHP 7
* MySQL
* Apache or IIS depend of your OS

Easier: You can download MAMP, WAMP or XAMPP depend of your OS
* Composer for **Symfony 3.3** and bundles installations

## Add-ons

* Bootstrap
* jQuery
* Font Awesome
* Google Fonts: *Rubik*

## ORM
Doctrine

## Bundles

* Twig
* PhpUnit

## Installation

Download project or clone it in the htdocs or www depend of your OS

* If you are using LAMPP on Linux, check your permissions: Go to /opt/lampp/htdocs/ open a bash and type:

        $  sudo ls -l
    Change permissions for everybody to be able to update informations in every repository's folders.

1. **Symfony 3.3 and bundles installations** Open bash in folder and type:

        composer install
        
2. **Database creation** Type:

        php bin/console doctrine:database:create
        
    Then
    
        php bin/console doctrine:schema:update --force
        
3. **Tests Database creation** Type:
   
       php bin/console doctrine:database:create --env=test
           
   Then
       
       php bin/console doctrine:schema:update --force --env=test

4. **Example Datas in database** Type:

        php bin/console todo:fixtures

5. **You can now access the application** by going on the URL:
[http://localhost/todolist/web](http://localhost/snow_tricks/web) (if you put the folder on your apache root)

And enjoy :)

If you have any question, you can contact me

Thanks!
