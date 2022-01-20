1) Download project or extract zip.
2) cp .env.example .env and update database name
3) Fire below cmd comands at project root for update vendors.
	composer install or composer dump-autoload
4) Generate key
    php artisan key:generate
5) Add database credentials in .env 
6) Fire below cmd comands at project root for create tables.
	php artisan migrate
7) Put below 2 lines in .env files if not there, if it is already there just change the value
	QUEUE_DRIVER=database
	QUEUE_CONNECTION=database
8) Fire below cmd comands at project root for run project.
    php artisan cache:clear
    php artisan config:cache
	php artisan serve
9) For run job, need to follow below command.
   php artisan queue:work