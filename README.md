


## nginx 代理配置 & PHP 配置
```
server{
    server_name b.dev.com;
    listen 8888;
    root         /www/app/www;
    index        index.html index.htm index.php;
    location / {
        try_files $uri $uri/ /index.php;
    }
    
    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
    
    # 代理后台
    location /admin {
        proxy_pass http://127.0.0.1:8888$request_uri;
    }
}
```

