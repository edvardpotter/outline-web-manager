# OutlineVPN Web Manager

This is a web manager for the open source project [Outline VPN](https://getoutline.org).  
This project based on Symfony (PHP 8.2).

### Requirements

```
Docker
Docker Compose
```
### Install

1. Clone this repository
```shell
git clone git@github.com:edvardpotter/outline-web-manager.git
```

2. Copy `.env`
```shell
cp .env.dist .env.local
```
3. Set `APP_SECRET` in .env.local  
```dotenv
# For example
APP_SECRET=1bb915f10df615fa087cc891dfd9cc5f6be86d79
```
4. Build containers
```shell
docker-compose up -d
```
5. Open http://localhost:8086/admin  
Email: `user@email.local`  
Password: `123456`
