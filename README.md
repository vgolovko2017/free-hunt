FreelanceFunt project's viewer.

- git clone https://github.com/vgolovko2017/free-hunt.git
- composer install
- create empty mysql database and change .env file according to your credentials
- run script and create db tables:
    - php migrate.php
- run script and import all necessary data from freeHunt:
    - php import_remote_data.php
- run project:
    - php -S localhost:8000 -t public

Dev env:
  - xubuntu 20.04.6 x86_64 LTS
  - php 8.1.17
  - vsc 1.76.2
  - mysql 8.0.32

  Thanks.
