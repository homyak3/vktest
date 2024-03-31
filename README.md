create DataBase "tester"
create table
  CREATE TABLE users2(id int AUTO_INCREMENT PRIMARY KEY, email char(255), password char(255))


/register api
url: vktest/register
body: {"email": "example@mail.ru","password": "Password1"}

/authorize
url: vktest/authorize
body: {"email": "example@mail.ru","password": "Password1"}

/feed
url: vktest/feed
body: {"jwt": "token"} // токен полученный от авторизации
