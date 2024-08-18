1. modifier .env avec les valeurs correspondantes(mongodb,app_url,redis_url,mailer_dsn)
2. req: php >=8.2,mongodb,redis 
3. sudo apt-get install supervisor
4. sur le ficher /etc/supervisor/conf.d/messenger-worker.conf coller la config dispo sur ce article: https://symfony.com/doc/current/messenger.html#messenger-supervisor
