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
    
    
#API

###免费机会接口
    post:/api/is/free
    request:
    {
    	"wxopenid":"wx102",
    	"machine":"machine102"
    }
    response:
    {
        "data": {
            "free": true
        },
        "errMsg": "request:ok!",
        "code": 0
    }
    
###免费机会保存接口
    post:/api/set/free
    request:
    {
    	"wxopenid":"wx102",
    	"machine":"machine102"
    }
    response:
    {
        "data": {
            "set": true,
            "wxopenid": "wx102",
            "machine": "machine102"
        },
        "errMsg": "request:ok!",
        "code": 0
    }
    
###cpm跳转接口
    post:/api/get/cpm
    request:
    {
    	"wxopenid":"wx102",
    	"machine":"machine102",
    	"tag":"01"
    }
    response:
    
    