# OutlineVPN Web Manager

This is a web manager for the open source project [Outline VPN](https://getoutline.org).  
This project based on Symfony (PHP 8.2).

### Requirements

```
Docker
Docker Compose
```
### Install

Clone this repository
```shell
git clone git@github.com:edvardpotter/outline-web-manager.git
```

Copy `.env`
```shell
cp .env.dist .env.local
```
 Set `APP_SECRET` in .env.local
Build containers
```shell
docker-compose up -d
```
Open http://localhost:8086/admin  

Email: `user@email.local`  
Password: `123456`
