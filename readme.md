## Requirements

- Lumen Framework >= 5.2 (famous API Framework)
- MongoDB (main storage of Gma)
- JWT (generate WEB TOKEN secured)
- APIDoc (use for API document)
- Composer

## Installation Guide

Install apidoc

```
#!bash
sudo npm -g install apidoc

```

To install MongoDB, please follow the official guide at MongoDB site (Linux, MacOS or Windows)

Run composer install for getting all dependency packages

```
#!bash
composer install

```

Run DB Seed document

```
#!bash
php artisan db:seed

```

## Development

Generate a new MongoDB Repository

```
#!bash
php artisan make:repomongo <NAME>

```

Add New Criteria

```
#!bash
php artisan make:criteria Criteria
```

Generate a new Mailabe

```
#!bash
php artisan make:mail <NAME>

```

Generate a new Notifiable

```
#!bash
php artisan make:notification <NAME>

```

Generate a new Job

```
#!bash
php artisan make:job <NAME>

```

Generate a new Schedule

```
#!bash
php artisan make:schedule <NAME>

```

Generate a new Eloquent repository

```
#!bash
php artisan make:repository <NAME>

```

Generate docs for API

```
#!bash

npm install apidoc -g
apidoc -i app/Http/Controllers/Api/V1 -o public/apidoc

```

## Some useful commands

Clear composer autoload

```
#!bash
composer dump-autoload

```

Upgrade PHP

```
#!bash
curl -s http://php-osx.liip.ch/install.sh | bash -s 7.0
```

Using PHP of XAMPP

```
#!bash
export XAMPP_HOME=/Applications/XAMPP
export PATH=${XAMPP_HOME}/bin:${PATH}
export PATH
#Make command to load source
source ~/.bash_profile
```

CHECK PHP INI

```
#!bash
php --ini
```

Install MongoDB on XAMP

```
#!bash
PATH=/Applications/XAMPP/bin/:$PATH LDFLAGS="-L/usr/local/opt/openssl/lib" CPPFLAGS="-I/usr/local/opt/openssl/include" pecl install -f mongodb
echo "extension=mongodb.so" >> /Applications/XAMPP/xamppfiles/etc/php.ini
```

Install package without dependence

```
#!bash
composer update --ignore-platform-reqs
```

Start Lumen

```
#!bash
php -S localhost:8000 -t public/
```

FORMAT DATETIME BY CARBON

```
#!bash
https://scotch.io/tutorials/easier-datetime-in-laravel-and-php-with-carbon
```

ADD USER/PASSWORD TO MONGODB

```
#!bash
1. New Install
2. export LC_ALL=C
3. mongod --port 27017 --dbpath /var/lib/mongo
4. New other Install
5. mongo --port 27017
6. use AutoShift
7. db.createUser(
   {
     user: "babycare",
     pwd: "@DB",
     roles: [ "readWrite", "dbAdmin" ]
   }
)
```

CREATE JOB SERVER

```
#!bash
1. Install supervisor (yum install supervisor)
2. Add config supervisor to /etc/supervisord.conf
	[program:tanca-worker]
	process_name=%(program_name)s_%(process_num)02d
	command=php /home/greenapp/thekingsapi/artisan queue:work --queue=emails,default --sleep=3 --tries=3 --timeout=60
	autostart=true
	autorestart=true
	user=root
	numprocs=8
	redirect_stderr=true
	stdout_logfile=/home/greenapp/thekingsapi/worker.log
	stderr_logfile=/home/greenapp/thekingsapi/worker.err.log
3. Start supervisord
```

CREATE CHEDULE SERVER

```
#!bash
1. create schedule.txt in /etc/cron.d: * * * * * root php /home/tanca/api/artisan schedule:run >> /dev/null 2>&1
2. Using php artisan make:schedule <NAME> to create schedule
3. Add this schedule to app/Console/Kernel
```
