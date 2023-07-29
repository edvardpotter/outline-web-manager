# OutlineVPN Web Manager

This is a web manager for the open source project [Outline VPN](https://getoutline.org).  
This project based on Symfony (PHP 8.2).

### Requirements

```
Docker
Docker Compose
```

### Screenshot
<details>
  <summary>Spoiler</summary>
<img width="1670" alt="image" src="https://github.com/edvardpotter/outline-web-manager/assets/16565815/f6b687d0-5f7b-4fb2-98a5-6e87d0f09b2a">
<img width="1678" alt="image" src="https://github.com/edvardpotter/outline-web-manager/assets/16565815/2b22333a-9ead-4cf8-bcc1-050472bfae6f">
<img width="1676" alt="image" src="https://github.com/edvardpotter/outline-web-manager/assets/16565815/a28d216b-9534-44a5-adba-eee9edc16426">
<img width="1665" alt="image" src="https://github.com/edvardpotter/outline-web-manager/assets/16565815/0c5f9d94-4802-45e0-a7f6-81589aaf1e19">
<img width="1667" alt="image" src="https://github.com/edvardpotter/outline-web-manager/assets/16565815/41c1270f-3692-4f71-9335-4174744866b2">
<img width="1673" alt="image" src="https://github.com/edvardpotter/outline-web-manager/assets/16565815/4b3a1917-a905-48f8-bd00-0197a69bc432">
</details>

### Features
* Add and manage an unlimited number of OutlineVPN servers
* Manage keys

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

NOTE: Change user email and password in admin panel after login
