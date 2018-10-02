added code to Exceptions/Handler.php
added code to Http/RedirectIfAuthenticated.php

php artisan config:cache to save changes in .env file

APP_URL=http://organizer.test

MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=2525
MAIL_PORT=587
MAIL_USERNAME=gilmouralmalbisdev@gmail.com
MAIL_PASSWORD=heheksdi1
MAIL_ENCRYPTION=tls

ADD the following
1. scout
2. composer require teamtnt/tntsearch
3. composer require teamtnt/laravel-scout-tntsearch-driver

https://laravel-news.com/tntsearch-with-laravel-scout

1. upload files
2. store them in a temp folder 
3. parse all files 
4. store info of temp files e.g. $filename = time() . '' . $file->getClientOriginalName(); using controller
5. send data to Confirmation page with availability of editing division; return view(confirmation_page)
6. if save. move files to respective division folder, and save to database;
7. if not save. return to index...

IF NEW INSTANCE OF ADD IS BEING COMMITED THEN CLEAN THE TEMP DIRECTORY using cleanDirectory method in Filesystem Facade.