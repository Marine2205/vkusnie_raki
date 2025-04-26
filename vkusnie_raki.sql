CREATE DATABASE `vkusnie_raki`;
USE `vkusnie_raki`;

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `tag` text NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `category` (`id`, `name`, `tag`) VALUES
(1,	'Икра',	'caviar'),
(2,	'Рак',	'cryfish'),
(3,	'Услуги',	'services'),
(4,	'Специи',	'spices'),
(5,	'Копчённые',	'smoker');

DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `img` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `gallery` (`id`, `img`, `description`) VALUES
(1,	'varka1.jpg',	'Сверенные средние раки, по фирменному реценпту'),
(2,	'img2.jpg',	'Вкусный рак');

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `category_id` bigint NOT NULL,
  `description` text NOT NULL,
  `price` int NOT NULL,
  `img` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
);

INSERT INTO `products` (`id`, `name`, `category_id`, `description`, `price`, `img`) VALUES
(1,	'Крупный рак',	2,	'от 60гр/шт до 150гр/шт',	3100,	'large.jpg'),
(2,	'Средний рак',	2,	'от 40гр/шт до 60гр/шт',	2800,	'middle.png'),
(3,	'Мелкий рак',	2,	'от 30гр/шт до 40гр/шт',	1900,	'melk.png'),
(4,	'Икра щучья 0,2 кг',	1,	'Икра щуки 0,2кг',	2500,	'red_caviar.jpg'),
(5,	'Варка',	3,	'цена за 1 кг варки',	100,	'varka.jpg'),
(6,	'Укроп',	4,	'100 грамм',	150,	'ukrop.jpg'),
(7,	'Доставка',	3,	'Доставка осуществляется Яндекс Доставкой',	0,	'dilivery.png'),
(8,	'Икра сиги 0,2кг',	1,	'Икра сиги из армении',	1500,	'red_caviar.jpg'),
(9,	'Специя \"Пикантная\"',	4,	'Укроп, чеснок, перец горошек и секретные ингридиенты ',	150,	'spice.jpg'),
(10,	'Специя \"Классическая\"',	4,	'Укроп, лавровый лист, перец горошек',	150,	'spice.jpg'),
(11,	'Специя \"Для Креветок и раков\"',	4,	'Укроп, перец горошек, лавровый лист, красная паприка',	150,	'spice.jpg'),
(12,	'Копчёные раки 3шт',	5,	'Копчённые раки 3шт в упаковке, горячего копчения',	360,	'kop.jpg');

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `create_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `stars` int NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `reviews` (`id`, `text`, `create_at`, `stars`) VALUES
(1,	'Классные раки, очень вкусные',	'2025-03-01 13:00:34',	5),
(2,	'Норм',	'2025-03-01 14:51:14',	4),
(3,	'Норм',	'2025-03-01 14:51:14',	4);

DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `sessionID` text NOT NULL,
  `product` bigint NOT NULL,
  `quantity` int NOT NULL,
  KEY `product` (`product`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product`) REFERENCES `products` (`id`)
);

