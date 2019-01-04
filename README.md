Sonar 4 CEPH (sonar4ceph)
================================


서두
================================

* CEPH 클러스터 운영에 필요한 핵심 정보를 요약/정리/시각화 해보기 위한 프로젝트이며, 통상적인 시스템 정보는 배제되었으며, 오직 CEPH 클러스터에 관련된 정보만을 다룬다.
* MAINTAINER: Jung-In.Jung (call518@gmail.com)
* 2018-12-13 ~ (On-going...)

## 목표

* CRUSH 맵에 기초한 CEPH 클러스터의 논리적 구성 시각화.
* Pool/OSD/PG의 분포 상태 시각화.
* 주요 컴포넌트들의 Health 상태 및 중요 지표 파악.
* 모니터링 부하 최소화.

## 스크린샷 (PoC)

![ScreenShot](README/show-physical.png?raw=true)
![ScreenShot](README/cluster-bw.png?raw=true)
![ScreenShot](README/showDistributionPGs.png?raw=true)
![ScreenShot](README/poolspgsosds.png?raw=true)
![ScreenShot](README/showPGCountByEachOSD.php?raw=true)
![ScreenShot](README/showPGCountByEachPool.png?raw=true)

설치
================================

* "Install"이라고 했지만, 특별함은 없다.
* 현재는, 단순 PHP/HTML로만 작성된 PoC 단계라, HTTPd의 Root위치에 배치만 하면 완료.
* 그러나 그냥 되면 재미 없으므로, 일부 요구되는 환경을 필요로 한다.


## 요구 환경

#### PHP >= 5.5

* "array_column" 함수를 필요로 하여, PHP 최소 버전은 5.5 이상 요구됨.


#### CEPH-REST-API (TCP:5000)

* 첫 테스트 시에는 단순 PHP의 "shell_exec()"를 이용해, "ceph {options} -f json" 방식으로 쿼리를 하였으나, 전송 데이터가 증가하고, 빈도수가 많아짐에 따라, 느려짐과 시스템 부하가 커짐.
  * (Note) 일부 ceph-rest-api로 취득 불가한 정보는 불가피하게 "shell_exec()"를 이용해 수집한다.
* 부하를 최소화 하기 위해, "ceph-rest-api"를 ceph 관리자(ceph.admin) 권한으로 실행(TCP:5000)하여 필요한 정보를 요청/수신하게 처리.
* (Note)
  * <del>현제 "check-osd_pg_state.sh"를 제외하고는 모두 ceph-rest-api를 통해 데이터 취득중이며, "check-osd_pg_state.sh" 역시, ceph-rest-api로 대체 예정.</del> -> "완료"
  * jq-cluster-io.php는 shell_exec() 필요로함. 역시, ceph-rest-api로 대체 예정.


## Tutorial

* (Note) CentOS7(x86_64)를 플랫폼을 기준으로 진행하며, 다른 플랫폼이라 해도 큰 차이는 없을 것이라 예상함.

#### Apache(HTTPd) 설치

```bash
yum install -y httpd
```

#### PHP 설치 (>=5.5)

* CentOS7의 기본 PHP는 5.4인 관계로, 3사 Repository를 이용하여 5.6 설치 하여 진행하였음.
  * 참고 문서: https://www.tecmint.com/install-php-5-6-on-centos-7/

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

#### CEPH-REST-API 서비스

* CEPH 노드든 HTTPd서버 노드든 관계 없음. 쿼리 수행만 정상확인 필수.
* CEPH-REST-API 서비스 위치와, "_config.php"의 "$ceph_api" 변수값 일치 확인 필수.
* (Note)
  * 데몬 타입으로 시작 방법을 몰라, 일단 screen을 이용해 진행 중임.....;;;
  * http 유져가 /etc/ceph/ceph.client.admin.keyring 파일에 접근 권한이 있어야 함.

```bash
chmod 644 /etc/ceph/ceph.client.admin.keyring

screen -dmSL Ceph-REST-API-Service ceph-rest-api -n client.admin
```

#### sonar4ceph 배치

```
git clone https://github.com/call518/sonar4ceph.git
mv sonar4ceph /var/www/html/
```

#### Web 접근

```bash
<브라우져 주소창>

http://{your-http-server-ip-or-name}/sonar4ceph
```


Completed~~~~~~~~~~~~~~~~~ :)


설정
================================

* HTTPd 서버만 요구되므로 아래 2가지 정도만 적절히 설정 하면 완료.
  * "_config.php" 파일
  * inkscope-lite에서 CEPH-REST-API에 대한 접근 지원을 위해, Apache "mod_proxy" 설정.

#### _config.php

* CEPH-REST-API의 엑세스 포인트를 정확히 확인/변경 한다.
* 당연한 이야기지만, 관측에 필요한 모든 데이터 소스가 Rest-API인데, 연결이 안되면 --> :(

#### Apache "mod_proxy"

/etc/httpd/conf.d/inkscope-lite.conf

* "http://127.0.0.1:5000/api/v0.1/" 주소를 ceph-rest-api의 LISTEN 주소로 수정.

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

## (Note) '바퀴를 새로 만들지 말자!'

"*inkscope-lite*" 라는 멋진 OpenSource가 있다. 구상했던 기능들중 중복 항목은 inkscope-lite를 include하여 모듈 형태로 활용.

Thanks~ ["inkscope-lite"](https://github.com/A-Dechorgnat/inkscope-lite) (["A-Dechorgnat"](https://github.com/A-Dechorgnat/inkscope-lite/commits?author=A-Dechorgnat))


히스토리
================================

##### tag v0.1.0

* 구상했던 기능들 초안 완료 버전 태깅.

##### tag v0.0.3

* 메인 콘솔 페이지와 CURSH맵 시각화 페이지 분리.
* "shell_exec()"를 통해 실행했던 "check-osd_pg_state.sh"를 ceph-rest-api를 통해 자료 수집 변환 완료.
* client IOPS 그래프 기능 추가.


##### tag v0.0.2

* 초안 완료. (CURSH맵 시각화)

##### tag v0.0.1

* shell_exec() 방식의 코드본 아카이빙
