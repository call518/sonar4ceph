Sonar 4 CEPH (sonar4ceph)
================================


Intro
================================

* This is a project to summarize, organize, and visualize important information required for CEPH cluster operation. The normal system information is excluded, and only the information related to the CEPH cluster is handled.
* MAINTAINER: Jung-In.Jung (call518@gmail.com)
* 2018-12-13 ~ (On-going...)

(Note) If you are interested in this project, please contact me at any time.

## Goals

* Visualization of logical structure of CEPH cluster based on CRUSH map.
* Pool / OSD / PG distribution status visualization.
* Understanding the health status and key indicators of the main components.
* Minimization of monitoring load.

## Features

#### Sonar4Ceph

* Show Physical by CRUSH-MAP
* PG States
* PG Size
* PG Count
  * per Pool
  * per OSD
* Dump Infomations
  * Pool
  * OSD
  * PG
* Client Bandwidth & IOPS
  * Cluster
  * per Pool
* OSD Latency
  * Apply
  * Commit

#### Inkscope-lite

* OSD MAP
* OSD Status
* OSD Performance
* Relation Pool/OSD/PG
* Pool State
* Stuck PG
* Object Lookup
* Erasure Profiles
* CURSH MAP

## ScreenShots (PoC)

![ScreenShot](README/show-physical.png?raw=true)
![ScreenShot](README/cluster-bw.png?raw=true)
![ScreenShot](README/showDistributionPGs.png?raw=true)
![ScreenShot](README/poolspgsosds.png?raw=true)
![ScreenShot](README/showPGCountByEachOSD.png?raw=true)
![ScreenShot](README/showPGCountByEachPool.png?raw=true)
![ScreenShot](README/showSizeOfPGs.png?raw=true)

Installation
================================

## Environment

#### Tested Ceph 

```
ceph version 12.2.10 (177915764b752804194937482a39e95e0ca3de94) luminous (stable)
```

#### Tested OS

```
LSB Version:	:core-4.1-amd64:core-4.1-noarch:cxx-4.1-amd64:cxx-4.1-noarch:desktop-4.1-amd64:desktop-4.1-noarch:languages-4.1-amd64:languages-4.1-noarch:printing-4.1-amd64:printing-4.1-noarch
Distributor ID:	CentOS
Description:	CentOS Linux release 7.4.1708 (Core) 
Release:	7.4.1708
Codename:	Core
```

#### PHP >= 5.5

* The "array_column" function is required, and the minimum PHP version is 5.5 or higher.


#### CEPH-REST-API (TCP:5000)

* In the first test, a simple PHP "shell_exec()" was used to query using the "ceph {options} -f json" method, but as the send data increased and became more frequent, Slow and heavy system load.
  * Inevitably, information that can not be acquired by some ceph-rest-api is collected using "shell_exec()".
* To minimize the load, execute "ceph-rest-api" with ceph administration(ceph.admin) authority(TCP: 5000) and request/receive necessary information.
  * The authority is also changed to an account with no admin or read-only privileges.
* "shell_exec()" is excluded, and ceph-rest-api is the only alternative.


## Tutorial

* It is expected that CentOS7(x86_64) will be based on the platform, and there will be no big difference in the other platforms.

#### Apache(HTTPd)

```bash
yum install -y httpd
```

#### PHP (>=5.5)

* The basic PHP version of CentOS7 is 5.4, and we used the three repositories to install 5.6.
  * Reference : https://www.tecmint.com/install-php-5-6-on-centos-7/

```bash
yum install -y epel-release
yum install http://rpms.remirepo.net/enterprise/remi-release-7.rpm
yum install -y yum-utils
yum-config-manager --enable remi-php56
yum install php php-cli php-curl
php -v
PHP 5.6.39 (cli) (built: Dec  5 2018 15:31:03) 
Copyright (c) 1997-2016 The PHP Group
Zend Engine v2.6.0, Copyright (c) 1998-2016 Zend Technologies
```

#### CEPH-REST-API

* Select the location where you want to run it appropriately because a query using HTTPd mod_proxy will be executed.
* It is necessary to confirm that the location of the CEPH-REST-API service matches the value of the "$ceph_api" variable in "_config.php".
* (Note)
  * Use "screen" to execute in Daemon form.
  * The http-user(nobody or wwwdata or any) has access to the "/etc/ceph/ceph.client.admin.keyring" file.

```bash
chmod 644 /etc/ceph/ceph.client.admin.keyring

screen -dmSL Ceph-REST-API-Service ceph-rest-api -n client.admin
```

#### sonar4ceph source

```
git clone https://github.com/call518/sonar4ceph.git
mv sonar4ceph /var/www/html/
```

#### Web Access

```bash
<Browser Address Bar>

http://{your-http-server-ip-or-name}/sonar4ceph
```


Completed~~~~~~~~~~~~~~~~~ :)


Customizing Config
================================

* A list of the main configuration files.
  * "_config.php" file.
  * Configure apache "mod_proxy" to support access to CEPH-REST-API in inkscope-lite.

#### _config.php

* Accurately check / change the access point of CEPH-REST-API.
* Since Rest-API is the most necessary measure, it is necessary to check the connection.

#### Apache "mod_proxy"

/etc/httpd/conf.d/sonar4ceph.conf

* "http://127.0.0.1:5000/api/v0.1/"
  * (Note) Change address to "Ceph-Rest-API" listener address.


```bash
<VirtualHost *:80>
    ServerName  localhost
    ServerAdmin webmaster@localhost

    DocumentRoot /var/www/html
    <Directory "/var/www/html">
        Options All
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "logs/error_log"

    # Possible values include: debug, info, notice, warn, error, crit,
    # alert, emerg.
    LogLevel warn

    ProxyRequests Off
    ProxyPass /ceph-rest-api/ http://127.0.0.1:5000/api/v0.1/

    CustomLog "logs/access.log" combined
</VirtualHost>
```

APPENDIX
================================

Special Thanks~ ["inkscope-lite"](https://github.com/A-Dechorgnat/inkscope-lite) (["A-Dechorgnat"](https://github.com/A-Dechorgnat/inkscope-lite/commits?author=A-Dechorgnat))

There is a nice OpenSource called "*inkscope-lite*." Among the features I had planned, I included inkscope-lite and used it in the form of a module.
