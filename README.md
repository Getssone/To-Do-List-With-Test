# To-Do-List-With-Test

Updating an application

> Openclassrooms PHP/Symfony developer course project 8 : improving an existing project.

**Previously :**

- "php": ">=5.5.9",

- "symfony/symfony": "3.1.\*",

**Objective :**

- "php": ">=8.1",

- "symfony/symfony": "6.4.\*",

## Features

- Implement new functionalities;
- Fix a few bugs;
- Implement automated tests.
- Analyze the project to visualize code quality.
- Suggest ways of improving the application.

## 🔍Need

### **Corrections d'anomalies**

- [ ] **A task must be attached to a user**
  > Currently, when a task is created, it is not attached to a user. You are asked to make the necessary corrections so that, when the task is saved, the authenticated user is automatically attached to the newly created task.
  >
  > When editing a task, the author cannot be changed.
  >
  > For tasks already created, they must be attached to an "anonymous" user.
- [ ] **Choosing a role for a user**
  > When a user is created, it must be possible to choose a role for him/her. The roles listed are:
  >
  > - user role (_ROLE_USER_) ;
  > - admin role (_ROLE_ADMIN_).
  >
  > When editing a user, it is also possible to change the user's role.

### **Implementation of new functionalities**

- [ ] **Authorization**
  > Only users with the administrator role (_ROLE_ADMIN_) should have access to the user management pages.
  >
  > Tasks can only be deleted by the users who created them.
  >
  > Tasks assigned to the "anonymous" user can only be deleted by users with the administrator role (_ROLE_ADMIN_).

### **Automated test implementation**

- [ ] Unit testing
- [ ] Functional testing

> Provide test data to prove operation

### **Technical documentation**

- [ ] Authentication implementation documentation :
  - [ ] understand which file(s) to modify and why ;
  - [ ] how authentication works;
  - [ ] where users are stored.
- [ ] Document explaining how all developers wishing to make changes should proceed
  - [ ] Which quality process to use
  - [ ] What are the rules to be respected.

### **Code quality & application performance audit**

- [ ] Take stock of the application's technical debt.
- [ ] Perform a code audit along the following two axes:
  - [ ] code quality (Codacy or CodeClimate)
  - [ ] Performance. (Symfony Profiling, Blackfire or New Relic)
- [ ] Take stock after modifying the application.

## Specs

- PHP 8.1
- Symfony 6.4
- Php-Unit

## Install on local webserver

You can install this project on your WAMP, Laragon, MAMP, or other local webserver.
To do so, you will first need to ensure the following requirements are met.

To install this project, you can use [Mamp](https://www.mamp.info/en/windows/) installed on your Computer.
Once your Mamp configuration is up and ready, you can launch the project.

Then go to symfony server:start where you should be able to access the blog.

## Requirements

- You need to have [composer](https://getcomposer.org/download/) on your computer
- Your server needs PHP version 8.1
- MySQL

## Install dependencies

Before running the project, you need to run the following commands in order to install the appropriate dependencies.

```
composer install
```

## Set up your environment

If you would like to install this project on your computer, you will first need to [clone the repo](https://github.com/Getssone/To-Do-List-With-Test) of this project using Git.

## Replace with your personal BDD config

1. create .env.local file:
1. Create name of database :

```
DATABASE_URL=mysql://root:password@127.0.0.1:3306/To-Do-List-With-Test
```

or

```
DATABASE_URL=mysql://root:root@127.0.0.1:3306/To-Do-List-With-Test
```

Start creation :

```
php bin/console doctrine:database:create
```

Create database tables:

```
php bin/console doctrine:schema:update --force
```

Insert a dataset:

```
php bin/console doctrine:fixtures:load
```

## Import database files

Once the mamp is launched, go to <http://localhost/To-Do-List-With-Test/> on your browser. You need to import my BDD :"localhost.sql" file into your BDD.

## Try on WebSite

Then, go on your favorite browser, try :

After enjoy The **To-Do-List-With-Test**
