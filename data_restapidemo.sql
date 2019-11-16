-- Zrzucanie danych dla tabeli restapidemo.language: ~3 rows (około)
DELETE FROM `language`;
/*!40000 ALTER TABLE `language` DISABLE KEYS */;
INSERT INTO `language` (`id`, `abbr`) VALUES
	(2, 'en'),
	(1, 'pl'),
	(3, 'xx');
/*!40000 ALTER TABLE `language` ENABLE KEYS */;

-- Zrzucanie danych dla tabeli restapidemo.product: ~30 rows (około)
DELETE FROM `product`;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` (`id`, `price`, `quantity`) VALUES
	(9, 1234, 5678),
	(10, 901, 2345),
	(12, 2345, 6456);
/*!40000 ALTER TABLE `product` ENABLE KEYS */;

-- Zrzucanie danych dla tabeli restapidemo.product_name: ~33 rows (około)
DELETE FROM `product_name`;
/*!40000 ALTER TABLE `product_name` DISABLE KEYS */;
INSERT INTO `product_name` (`id`, `product_id`, `language_id`, `name`, `description`) VALUES
	(10, 9, 1, 'nóż', 'zażółć gęślą jaźń'),
	(11, 9, 2, 'knife', 'for cutting'),
	(12, 9, 3, 'cvxbcv', 'dklfjh slkdjf'),
	(13, 10, 2, 'fork', NULL),
	(14, 10, 1, 'widelec', 'opis widelca'),
	(15, 12, 1, 'kaczka', 'zwierzę'),
	(16, 12, 3, 'fgdfgd', 'swidfgddfgm'),
	(17, 12, 2, 'duck', 'animal');
/*!40000 ALTER TABLE `product_name` ENABLE KEYS */;

-- Zrzucanie danych dla tabeli restapidemo.user: ~2 rows (około)
DELETE FROM `user`;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `username`, `hash`, `read`, `write`) VALUES
	(1, 'testuser', '$1$/okDj.9q$I8ARoWvei8N8.S9IjIo1T0', b'1', b'1'),
	(2, 'reader', '$1$/okDj.9q$I8ARoWvei8N8.S9IjIo1T0', b'1', b'0');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
