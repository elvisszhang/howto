In our daily php development, most public packages can be directly used, but some private packages are limited inside company. In this case, we need to setup a private packagist.org-like private repository. We can use satis or toran proxy to build such a private repository. satis is easier to setup, so we choose satis.

## How to install satis

type command under shell:

```
cd /data/wwwroot/
composer create-project composer/satis --stability=dev --keep-vcs
mv satis packages.jrtk.net
cd packages.jrtk.net
```

**/data/wwwroot/** is my root for all web sites,you can change it to yours.
**packages.jrtk.net** is my domain, you can change it to yours.

## How to configure satis

type command:
```
vi satis.json
```

the content of satis.json is as follows:
```
{
    "name": "Jiaruan Repository",
    "homepage": "http://packages.jrtk.net",
    "repositories": [
        {"type": "vcs", "url": "http://git.jrtk.net/jrtk/jr-tp31.git"},
        {"type": "vcs", "url": "http://git.jrtk.net/jrtk/jr-phplib.git"},
    ],
    "require": {
        "jrtk/jr-tp31": "*",
        "jrtk/jr-phplib": "*",
    }
}
```


## How to build satis repository

type command:
```
php bin/satis build satis.json public/
```


## How to configure satis web server

type command:
```
cd /usr/local/nginx/conf/vhost/
vi packages.jrtk.net.conf
```

the content of packages.jrtk.net.conf
```
server {
listen 80;
server_name packages.jrtk.net;
access_log off;
index index.html index.htm index.php;
root /data/wwwroot/packages.jrtk.net/public;


location ~ \.php {
    fastcgi_pass unix:/dev/shm/php-cgi.sock;
    fastcgi_index index.php;
    include fastcgi_params;
    set $real_script_name $fastcgi_script_name;
        if ($fastcgi_script_name ~ "^(.+?\.php)(/.+)$") {
        set $real_script_name $1;
        #set $path_info $2;
        }
    fastcgi_param SCRIPT_FILENAME $document_root$real_script_name;
    fastcgi_param SCRIPT_NAME $real_script_name;
    }
location ~ .*\.(gif|jpg|jpeg|png|bmp|swf|flv|ico)$ {
    expires 30d;
    access_log off;
    }
location ~ .*\.(js|css)?$ {
    expires 7d;
    access_log off;
    }
}

```


## How to use satis in our php project

type command under shell:

```
cd /data/wwwroot/
mkdir my-project
cd my-project
vi composer.json
```

the content of composer.json:
```
{
    "name": "jrtk/my-project",
    "type": "project",
    "config": {
        "secure-http": false
    },
    "repositories": {
        "0": {
            "type": "composer",
            "url": "packages.jrtk.net"
        },
        "packagist": {
            "type": "composer",
            "url": "https://packagist.phpcomposer.com"
        }
    },
    "require": {
        "jrtk/jr-tp31": "3.1.3",
        "jrtk/jr-phplib": "1.0.0",
        "hellogerard/jobby": "^3.2"
    }
}

```

type composer command to fetch packages:

```
composer update
```


## How to speed up "composer update" command

```
{
    "archive": {
        "directory": "dist",
        "format": "tar",
        "prefix-url": "http://packages.jrtk.net/",
        "skip-dev": true
    }
}
```


## How to update satis repository through browser
When developers updated their code to git repository, we need to rebuild statis to keep update with git.
It's a good idea if developers can build satis through browser without having to log into shell.
Below is the php code, make sure git log info is remembered before executing this script through browser.

```
<html>
<head></head>
<body>
	<textarea style="width:800px;height:600px">
	<?php
	set_time_limit(0);
	$pwd = "*****";
	$pipes = array();
	$command = "sudo /usr/local/php/bin/php ./bin/satis build satis.json ./public";
	$desc = array(array('pipe', 'r'), array('pipe', 'w'), array('pipe', 'w'));
	$handle = proc_open($command, $desc, $pipes, $pwd);
	if (!is_resource($handle)) {
		fprintf(STDERR, "proc_open failed.\n");
		exit(1);
	}
	while( $ret=fgets($pipes[1]) ){
	   echo $ret;
	}
	fclose($pipes[0]);
	fclose($pipes[1]);
	fclose($pipes[2]);
	proc_close($handle);
	?>
	</textarea>
</body>
</html>
```


## References
[use satis to build private composer repository ](http://www.cnblogs.com/maxincai/p/5308284.html)
[handling-private-packages-with-satis](http://docs.phpcomposer.com/articles/handling-private-packages-with-satis.html)
