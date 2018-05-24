# bfyp-at-nette-framework
You can start your nette proyects from here

Hi there i just published an app where you can start your proyect, the features are:

- Front templates by country
- identifying browser language, you can change it pressing language link
- Access control list ACL
- Easy whay to change superuser, it on config.nen 
- By default blocks all proxys but you can accept any want that you want
- Block ip
- App key on config.neon , this for tables that you want (sh1) verification key. in the app user_role and permission tables
- InnoDB and Myisam mysql engines, you have to configurate it on config.neon

It is very easy to implement your presenters and for your models there is a models containers where you can call all that you want, in the same way the forms

you can add new users with your email.

To Install

- 1 clone
- 2 composer update on composer.json directory
- 3 run sql from DB installer/nette_innoDB.sql or DB installer/nette_myisam.sql. If you use myIsam engine please uncomment lines 121 and 125 from config.neon
- 4 Config your database name and user on config.neon . lines 13 to 17
