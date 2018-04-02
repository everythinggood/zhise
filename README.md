#init

    composer install
    php -S localhost:8080 -t public index.php
    
#docker

    install docker
    install docker-compose
    git clone https://github.com/everythinggood/docker-slim-server.git
    cp .env.example
    vim .env
    docker-compose build php-fpm nginx redis
    docker-compose -d run php-fpm nginx redis