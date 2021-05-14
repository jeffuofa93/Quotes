# Quotes
- URL - www.votequotes.com

## Project Description 

This project is a MVC full stack web application that allows users to register an account and then add, remove or vote on quotes submitted by themselves or other users. The project uses Javascript, CSS and HTML on for the front end, PHP for the backend and MYSQL as the database. The project is hosted at votequotes.com. Hosting the project was done through a digital ocean droplet of an ubuntu server. I installed and configured Apache, PHP and SQL on the server and used ssh to transfer files up to the server. 

## Features 

The project uses salted hashes for passwords to increase security. In addition, all user inputted SQL parameters are binded to prevent injection attacks. All quotes are ranked in order by number of votes which is implemented through DatabaseAdaptor.php. Also the controller.php file is fully modularized as a class to allow smooth adjustments and optimizations in the future. 
