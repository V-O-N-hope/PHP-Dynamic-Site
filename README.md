# PHP-Dynamic-Site

Данный проект был выполнен в ходе курса 
Веб-Разработка на языке php с использованием БД MySql

Цель работы: создать динамический сайт магазина, который бы позволял 
Динамически обновлять каталог, статусы заказов, информацию об элементах меню

Порядок действий: 
1) создать бд с именем web-site

2) создать таблицу 

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  shouldSend BOOLEAN,
  UNIQUE (email)
);

Добавить код 
ALTER TABLE users
ADD COLUMN uniqKey VARCHAR(255) NOT NULL UNIQUE AFTER shouldSend;

3) Создать таблицу для карточек товаров. код такой: 

CREATE TABLE product_cards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  image_path VARCHAR(255),
  price DECIMAL(10, 2),
  description TEXT,
  rating DECIMAL(3, 1)
);

3) Создать таблицу заказов. код вот такой: 

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  product_card_id INT,
  user_id INT,
  status ENUM('В стадии принятия', 'В стадии готовки', 'В стадии доставки', 'Доставлено'),
  FOREIGN KEY (product_card_id) REFERENCES product_cards(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id)
);


5) Поменять поля в файлике site/bd_data.php на свои для бд

6) Дальше создать таблицу
   CREATE TABLE user_logins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  login_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  os VARCHAR(20),
  ip_address VARCHAR(45)
);

Потом выполнить следующие строки для SQL запроса
ALTER TABLE user_logins ADD email VARCHAR(255);

ALTER TABLE product_cards ADD COLUMN name VARCHAR(255) AFTER id;


