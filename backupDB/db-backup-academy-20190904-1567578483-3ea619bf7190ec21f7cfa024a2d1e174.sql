DROP TABLE IF EXISTS end_video;

CREATE TABLE `end_video` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `subtopic_id` int(6) NOT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `upload_date` datetime NOT NULL DEFAULT current_timestamp(),
  `duration` int(20) NOT NULL,
  `video_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




DROP TABLE IF EXISTS staff;

CREATE TABLE `staff` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `phone` bigint(10) NOT NULL,
  `password` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `role` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO staff VALUES("1","Zhambyl","Samat","7074105268","abc4ca4415a99f9f46c2260ef69dfa69","admin","2019-08-26 15:59:14");



DROP TABLE IF EXISTS status;

CREATE TABLE `status` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `title` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` int(1) NOT NULL DEFAULT 1,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO status VALUES("1","archive","0","");
INSERT INTO status VALUES("2","active","1","");
INSERT INTO status VALUES("3","not_submitted","0","");



DROP TABLE IF EXISTS student;

CREATE TABLE `student` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `school` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `class` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `phone` bigint(10) NOT NULL,
  `instagram` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject_id` int(6) NOT NULL,
  `password` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset` int(1) NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status_id` int(11) NOT NULL DEFAULT 3,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




DROP TABLE IF EXISTS subject;

CREATE TABLE `subject` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `title` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` int(1) NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO subject VALUES("19","Қазақстан тарихы","0","2019-09-03 19:48:37");
INSERT INTO subject VALUES("18","Геометрия","0","2019-09-03 19:48:37");
INSERT INTO subject VALUES("17","География","0","2019-09-03 19:48:37");
INSERT INTO subject VALUES("16","Алгебра","0","2019-09-03 19:48:36");
INSERT INTO subject VALUES("20","Математикалық сауаттылық","0","2019-09-03 19:48:37");
INSERT INTO subject VALUES("21","Физика","0","2019-09-03 19:48:37");
INSERT INTO subject VALUES("22","Химия","0","2019-09-03 19:48:37");



DROP TABLE IF EXISTS subtopic;

CREATE TABLE `subtopic` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `topic_id` int(6) NOT NULL,
  `title` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `subtopic_order` int(3) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=702 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO subtopic VALUES("36","16","Теріс сандар","6","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("35","16","Ондық бөлшектер","5","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("34","16","Жай бөлшектерді көбейту және бөлу","4","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("33","16","Жай бөлшектерді қосу және азайту","3","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("32","16","Амалдар","2","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("31","16","Кіріспе","1","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("37","16","Амаларды қайталау","7","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("38","16","Теңдеулер","8","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("39","16","Дәреже","9","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("40","16","Теріс дәреже","10","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("41","16","Түбір","11","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("42","16","Аралық бақылау. Фундамент ","12","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("43","16","Апталық есептер. Фундамент","13","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("44","16","Қорытынды","14","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("45","17","Кіріспе","1","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("46","17","Көпмүшелер","2","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("47","17","Ортақ көбейткішті жақша сыртына шығару","3","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("48","17","(a+-b)^2","4","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("49","17","a^2-b^2","5","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("50","17","a^3+-b^3","6","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("51","17","(a+-b)^3","7","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("52","17","Бөлшектерді қысқарту","8","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("53","17","Рационал бөлшектерді қосу/азайту 1","9","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("54","17","Рационал бөлшектерді қосу/азайту 2","10","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("55","17","Рационал бөлшектерді көбейту/бөлу","11","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("56","17","Өрнектерді ықшамдау","12","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("57","17","Апталық есептер. Өрнекті ықшамдау","13","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("58","17","Қорытынды","14","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("59","18","Кіріспе","1","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("60","18","Квадрат теңдеулер","2","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("61","18","Квадрат теңдеу арқылы көбейткіштерге жіктеу","3","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("62","18","Бөлшек рационал теңдеулер","4","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("63","18","Теңдеулер жүйесі","5","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("64","18","Апталық есептер. Теңдеулер","6","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("65","18","Қорытынды","7","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("66","19","Кіріспе","1","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("67","19","Теңсіздіктер. Квадрат теңсіздіктер","2","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("68","19","Интервал әдісімен теңсіздіктерді шешу","3","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("69","19","Бөлшек теңсіздіктер","4","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("70","19","Теңсіздіктер жүйесі","5","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("71","19","Аралық бақылау: Өрнекті ықшамдау, теңдеулер, теңсіздіктер, теңсіздіктер жүйесі","6","2019-09-03 19:48:36");
INSERT INTO subtopic VALUES("72","19","Апталық есептер. Теңсіздіктер","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("73","19","Қорытынды","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("74","20","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("75","20","Бөлшектің бөлімін иррационалдықтан арылту","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("76","20","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("77","21","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("78","21","Иррационал теңдеулер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("79","21","Жаңа айнымалы енгізу арқылы иррационалдық теңдеулерді шешу","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("80","21","Апталық есептер. Бөлшектің бөлімін иррационалдықтан арылту. Иррационал теңдеулер","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("81","21","Қорытынды","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("82","22","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("83","22","Арифметикалық прогрессия 1","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("84","22","Арифметикалық прогрессия 2","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("85","22","Геометриялық прогрессия 1","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("86","22","Геометриялық прогрессия 2","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("87","22","Аралас прогресия","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("88","22","Апталық есептер. Прогрессия","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("89","22","Қорытынды","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("90","23","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("91","23","Тригонометриялық өрнектің мәнін табу. Негізгі тригонометриялық формулалар","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("92","23","Тригонометриялық формулаларды қолдану арқылы тригонометриялық өрнекті есептеу","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("93","23","Келтіру формулалары. Функцияны периодтан арылту","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("94","23","Екі еселенген аргумент формулалары","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("95","23","Дәрежені төмендету формулалары","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("96","23","Аргументтерді қосу және азайту формулалары","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("97","23","Тригонометриялық функциялардың қосындысын көбейтіндіге және көбейтіндісін қосындыға түрлендіру","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("98","23","Кері тригонометриялық функциялар","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("99","23","Қайталауға арналған есептер","10","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("100","23","Аралық бақылау. Тригонометриялық ықшамдаулар","11","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("101","23","Апталық есептер. Тригонометриялық ықшамдаулар","12","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("102","23","Қорытынды","13","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("103","24","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("104","24","Тригонометриялық теңдеулер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("105","24","Тригонометриялық формулаларды пайдалана отырып тригонометриялық теңдеулерді шешу","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("106","24","Көбейткішке жіктеу арқылы тригонометриялық теңдеулерді шешу","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("107","24","Жаңа айнымалы енгізу арқылы тригонометриялық теңдеулерді шешу","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("108","24","Қосымша бұрыш енгізу арқылы тригонометриялық теңдеулерді шешу","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("109","24","Қорытынды","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("110","25","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("111","25","Тригонометриялық теңсіздіктер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("112","25","Тригонометриялық формулаларды пайдалана отырып тригонометриялық теңсіздіктерді шешу","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("113","25","Жаңа айнымалы енгізу арқылы тригонометриялық теңсіздіктерді шешу","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("114","25","Қорытынды","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("115","26","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("116","26","Тригонометриялық теңсіздіктер жүйесі","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("117","26","Тригонометриялық теңдеулер жүйесі 1","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("118","26","Тригонометриялық теңдеулер жүйесі 2","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("119","26","Тригонометриялық теңдеулер мен теңсіздіктерді қайталау есептері","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("120","26","Аралық бақылау. Тригонометриялық теңдеулер мен теңсіздіктер, теңдеулер мен теңсіздіктер жүйесі","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("121","26","Апталық есептер. Тригонометриялық теңдеулер мен теңсіздіктер","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("122","26","Қорытынды","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("123","27","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("124","27","Модуль таңбасы бар теңдеулер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("125","27","Модуль таңбасы бар теңсіздіктер","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("126","27","Апталық есептер. Модуль таңбасы бар теңдеулер мен теңсіздіктер","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("127","27","Қорытынды","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("128","28","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("129","28","Бөлшек дәреже","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("130","28","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("131","29","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("132","29","Көрсеткіштік теңдеулер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("133","29","Көбейткішке жіктеу арқылы көрсеткіштік теңдеулерді шешу","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("134","29","Жаңа айнымалыны енгізу арқылы көрсеткіштік теңдеулерді шешу","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("135","29","Көрсеткіштік теңсіздіктер","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("136","29","Жаңа айнымалыны енгізу арқылы көрсеткіштік теңсіздіктерді шешу","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("137","29","Аралық бақылау. Бөлшек дәреже, көрсеткіштік теңдеулер мен теңсіздіктер","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("138","29","Апталық есептер. Көрсеткіштік теңдеулер мен теңсіздіктер","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("139","29","Қорытынды","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("140","30","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("141","30","Логарифм мәнін анықтамасы бойынша анықтау","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("142","30","Логарифмдерді қосу және азайту формулалары","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("143","30","Басқа негізге көшу формулаларын пайдалана отырып логарифмдерді есептеу ","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("144","30","Логарифмді басқа логарифмдер арқылы есептеу","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("145","30","Қорытынды","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("146","31","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("147","31","Логарифмдік теңдеулер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("148","31","Логарифмдерді қосу және азайту формулаларын пайдалану арқылы және жаңа айнымалы енгізу арқылы логар","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("149","31","Бір негізге келтіру арқылы логарифмдік теңдеулерді шешу","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("150","31","Екі жағын логарифмдеу арқылы логарифмдік теңдеулерді шешу","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("151","31","Қорытынды","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("152","32","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("153","32","Логарифмдік теңсіздіктер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("154","32","Жаңа айнымалы енгізу арқылы логарифмдік теңсіздіктерді шешу","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("155","32","Қорытынды","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("156","33","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("157","33","Логарифмдік теңдеулер жүйесі","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("158","33","Логарифмдік теңсіздіктер жүйесі","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("159","33","Логарифмді қайталау есептері 1","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("160","33","Логарифмді қайталау есептері 2","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("161","33","Аралық бақылау. Логарифмдер","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("162","33","Апталық есептер. Логарифмдер","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("163","33","Қорытынды","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("164","34","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("165","34","Рационал функцияның туындысы","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("166","34","Иррационал функцияның туындысы","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("167","34","Тригонометриялық функциялардың туындысы","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("168","34","Көрсеткіштік және логарифмдік функциялардың туындысы","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("169","34","Туынды арқылы функцияның өсу және кему аралығын табу","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("170","34","Туындың арқылы функцияның экстремум нүктелерін табу","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("171","34","Туынды арқылы функцияның ең үлкен және ең кіші мәндерін табу","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("172","34","Туындының физикалық мағынасы","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("173","34","Жанаманың теңдеуі","10","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("174","34","Туындыны қайталау есептері 1","11","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("175","34","Туындыны қайталау есептері 2","12","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("176","34","Аралық бақылау. Туынды","13","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("177","34","Апталық есептер. Туынды","14","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("178","34","Қорытынды","15","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("179","35","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("180","35","Алғашқы функция 1","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("181","35","Алғашқы функция 2 ","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("182","35","Алғашқы функция 3","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("183","35","Интеграл","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("184","35","Интеграл арқылы қисықсызықты фигураның ауданын табу","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("185","35","Апталық есептер. Алғашқы функция. Интеграл","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("186","35","Қорытынды","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("187","36","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("188","36","Жұп және тақ функциялар. Функцияның анықталу облысын табу","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("189","36","Функцияның мәндер облысын табу","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("190","36","Функцияның ең кіші оң периодын табу. Кері функция","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("191","36","Графигі бойынша қисықсызықты фигураның ауданын табу","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("192","36","Аралық бақылау. Интеграл. Функция","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("193","36","Апталық есептер. Функция","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("194","36","Қорытынды","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("195","37","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("196","37","Тура пропорционалдық. Кері пропорционалдық. Санды бөліктерге бөлу. Масштаб","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("197","37","Пайызға арналған есептер","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("198","37","Шамалардың өзгеруін процентпен көрсету","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("199","37","Қозғалысқа арналған есептер","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("200","37","Жұмысқа арналған есептер","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("201","37","Концентрацияға арналған есептер","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("202","37","Бүтін сандарға арналған мәселе есептер","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("203","37","Апталық есептер. Мәселе есептер","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("204","37","Қорытынды","10","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("205","38","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("206","38","Тестпен жұмыс 1","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("207","38","Тестпен жұмыс 2","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("208","38","Тестпен жұмыс 3","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("209","38","Тестпен жұмыс 4","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("210","38","Тестпен жұмыс 5","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("211","38","Тестпен жұмыс 6","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("212","38","Қорытынды","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("213","39","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("214","39","Кіріспе","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("215","39","План және карта","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("216","39","Литосфера","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("217","39","Жердің ауа қабаты - Атмосфера","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("218","39","Гидросфера - жердің су қабаты","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("219","39","Биосфера және географиялық қабық","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("220","39","Қорытынды","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("221","40","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("222","40","Еуразия","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("223","40","Солтүстік Америка","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("224","40","Оңтүстік Америка","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("225","40","Африка","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("226","40","Аустралия және Мұхиттық аралдар","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("227","40","Антарктида","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("228","40","Қорытынды","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("229","41","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("230","41","Қазақ жерінің зерттелуі","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("231","41","Қазақстанның жер бедері, геологиялық құрылысы және пайдалы қазбалары","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("232","41","Қазақстанның климаты","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("233","41","Қазақстанның ішкі сулары","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("234","41","Қазақстанның топырақ жамылғысы мен табиғат зоналары","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("235","41","Табиғатты қорғау және қорықтар","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("236","41","Қазақстанның физикалық аудандары","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("237","41","Қорытынды","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("238","42","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("239","42","Халқы мен еңбек ресурсы","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("240","42","Отын энергетика кешені","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("241","42","Түсті және қара металлургия","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("242","42","Химия өнеркәсібі","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("243","42","Машина жасау","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("244","42","Агроөнеркәсіптік кешен","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("245","42","Тамақ және жеңіл өнеркәсіптік кешен","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("246","42","Көлік кешені","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("247","42","Экономикалық аудандар","10","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("248","42","Қорытынды","11","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("249","43","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("250","43","Халқы","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("251","43","Табиғат ресурстары","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("252","43","Дүниежүзілік шаруашылық","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("253","43","Көлік және байланыс","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("254","43","ТМД елдері Ресей","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("255","43","Еуропа елдері","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("256","43","Азия елдері","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("257","43","Жапония","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("258","43","Қытай","10","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("259","43","Үндістан","11","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("260","43","Америка елдері","12","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("261","43","Африка","13","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("262","43","Аустралия","14","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("263","43","Халықаралық ұйымдар","15","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("264","43","Қорытынды","16","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("265","44","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("266","44","Үшбұрыш және оның негізгі элементтері","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("267","44","Синустар және косинустар теоремасы","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("268","44","Үшбұрыштың биссектрисасы, медианасы және биіктігі","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("269","44","Тең бүйірлі және тікбұрышты үшбұрыш 1","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("270","44"," Тең қабырғалы үшбұрыш және тік бұрышты үшбұрыш 2.","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("271","44","Үшбұрыштардың ұқсастығы","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("272","44","Апталық есептер. Үшбұрыш","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("273","44","Қорытынды","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("274","45","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("275","45","Параллелограм","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("276","45","Тіктөртбұрыш және квадрат","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("277","45","Ромб","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("278","45","Трапеция","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("279","45","Көпбұрыштар","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("280","45","Төртбұрыштар мен көпбұрыштарға аралас есептер","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("281","45","Апталық есептер. Төртбұрыштар. Көпбұрыштар","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("282","45","Қорытынды","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("283","46","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("284","46","Шеңбер және оның элементтері","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("285","46","Шеңбермен аралас есептер","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("286","46","Үшбұрыштар мен төртбұрыштарды қайталау есептері","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("287","46","Аралық бақылау. Планиметрия","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("288","46","Апталық есептер. Шеңбер","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("289","46","Қорытынды","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("290","47","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("291","47","Вектор","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("292","47","Вектор 2","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("293","47","Қорытынды","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("294","48","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("295","48","Түзудің теңдеуі. Шеңбердің теңдеуі. Координаттар әдісі","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("296","48","Апталық есептер. Вектор. Түзудің, Шеңбердің теңдеуі","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("297","48","Қорытынды","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("298","49","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("299","49","Призма","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("300","49","Пирамида","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("301","49","Қиық пирамида","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("302","49","Апталық есептер. Призма. Пирамида","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("303","49","Қорытынды","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("304","50","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("305","50","Цилиндр","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("306","50","Конус","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("307","50","Қиық конус","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("308","50","Шар","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("309","50","Фигураны айналдырғанда пайда болатын денелер","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("310","50","Апталық есептер. Айналу денелері","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("311","50","Қорытынды","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("312","51","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("313","51","Денелер комбинациясы","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("314","51","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("315","52","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("316","52","Жазықтыққа көлбеуленген фигуралар","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("317","52","Стереометрияны қайталау есептері 1","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("318","52","Стереометрияны қайталау есептері 2","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("319","52","Қорытынды","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("320","53","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("321","53","Ежелгі адамдардың өмірі.Тас дәуірі","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("322","53","Қола дәуірі","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("323","53","Ерте темір дәуіріндегі Қазақстан. Сақтар.","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("324","53","Сарматтар","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("325","53","Ғұндар","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("326","53","Үйсіндер. Қаңлылар","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("327","53","Қазақстан көшпелілерінің мәдениеті","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("328","53","Қорытынды","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("329","54","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("330","54","Түрік қағанаты","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("331","54","Түргеш қағанаты. Қарлұқ қағанаты.","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("332","54","Оғыз мемлекеті. Қимақ қағанаты.","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("333","54","IV-IX ғасырлардағы мәдениет","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("334","54","Қорытынды","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("335","55","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("336","55","Қарахан мемлекеті. Наймандар, керейіттер, жалайырлар","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("337","55","Қарақытайлар. Қыпшақ хандығы","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("338","55","Ұлы жібек жолы. Қазақстандағы ортағасырлық қалалар","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("339","55","IX ғасырдың екінші жартысы- XIII ғасырдың басындағы мәдениет","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("340","55","Моңғол шапқыншылығы","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("341","55","Алтын Орда. Ақ Орда.","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("342","55","Моғолстан. Әмір Темір басқыншылығы","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("343","55","Ноғай Ордасы. Әбілқайыр Хандығы","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("344","55","XIII-XV ғ. бірінші жартысындағы қоғамдық-саяси құрылым, экономика, мәдениет","10","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("345","55","Қорытынды","11","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("346","56","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("347","56","Қазақ хандығының құрылуы","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("348","56","Қасым хан. Хақназар хан","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("349","56","Тәуекел хан. Есім хан","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("350","56","Жәңгір хан. Тәуке хан","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("351","56","XV-XVII ғасырдағы мәдениет, қоғамдық өмір","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("352","56","Қорытынды","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("353","57","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("354","57","Қазақ-жоңғар соғыстары","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("355","57","XVIII ғасырдағы Қазақ хандығы","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("356","57","Абылай хан","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("357","57","XVIII ғасырдағы Қазақ мәдениеті","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("358","57","Қорытынды","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("359","58","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("360","58","С.Датұлы бастаған ұлт-азаттық көтеріліс","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("361","58","Ж.Тіленшіұлы, Қ.Абылайұлы, С.Қасымұлы бастаған ұлт-азаттық көтеріліс","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("362","58","М.Өтемісұлы, И.Тайманұлы бастаған ұлт-азаттық көтеріліс","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("363","58","К.Қасымұлы бастаған ұлт-азаттық көтеріліс","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("364","58","Ж.Нұрмұхаммедұлы, Е.Көтібарұлы бастаған ұлт-азаттық көтеріліс","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("365","58","Ресей империясының Қазақстанның оңтүстік аймақтарын қосып алуы","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("366","58","Қорытынды","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("367","59","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("368","59","1860-70 жылдардағы азаттық күрестер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("369","59","Ресей империясының қоныстандыру саясаты. Қазақ қоғамындағы өзгерістер","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("370","59","XIX екінші жартысындағы әлеуметтік-экономикалық даму. XIX ғасырдағы әдебиет","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("371","59","XIX- XX ғасыр басындағы мәдениет","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("372","59","XIX- XX ғасыр басындағы ұлт зиялылары","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("373","59","Қорытынды","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("374","60","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("375","60","XX ғасыр басындағы Қазақстан","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("376","60","1916 жылғы ұлт-азаттық көтеріліс","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("377","60","Ақпан революциясы. Қазан төңкерілісі","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("378","60","Кеңес билігінің орнауы. Азамат соғысы","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("379","60","Қазақ автономиясының құрылуы","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("380","60","Жаңа өмір қиындықтары. Жер-су реформасы","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("381","60","Индустрияландыру","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("382","60","Ауыл шаруашылығын ұжымдастыру","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("383","60","1930 жылдардағы қоғамдық-саяси өмір","10","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("384","60","Қазақ КСР құрылуы","11","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("385","60","XX ғасырдың бірінші жартысындағы мәдениет","12","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("386","60","Қорытынды","13","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("387","61","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("388","61","Ұлы Отан соғысы. Тылдағы жұмыс","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("389","61","ҰОС ғылым мен мәдениет","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("390","61","Соғыстан кейінгі қоғамдық-саяси өмір","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("391","61","Өнеркәсіп. Экономиканы реформалау. Тың игеру","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("392","61","1940-60 жылдардағы мәдениет","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("393","61","Тоқырау жылдарындағы қоғамдық-саяси өмір","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("394","61","Тоқырау жылдарындағы өнеркәсіп, ауыл шаруашылығы","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("395","61","Тоқырау жылдарындағы рухани өмір","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("396","61","1986 жылғы желтоқсан оқиғалары. Оның салдары","10","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("397","61","Демократияландыру үрдісі","11","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("398","61","Экономикадағы дағдарыс","12","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("399","61","Қорытынды","13","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("400","62","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("401","62","Тәуелсіздік жолындағы қадам. Егемендік","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("402","62","Тәуелсіз Қазақстан","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("403","62","1993 жылғы, 1995 жылғы конституция","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("404","62","Демократиялық үдерістің дамуы","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("405","62","Тәуелсіз Қазақстан экономикасы","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("406","62","Тәуелсіз Қазақстан халқы","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("407","62","Елдің мәдени-рухани өмірі","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("408","62","Астана. Н.Ә.Назарбаев.","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("409","62","Қорытынды","10","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("410","63","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("411","63","Сандар теориясы 1","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("412","63","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("413","64","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("414","64","Сандар теориясы 2","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("415","64","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("416","65","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("417","65","Тура пропорционалдық. Кері пропорционалдық. Санды бөліктерге бөлу. Масштаб","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("418","65","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("419","66","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("420","66","Пайызға арналған есептер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("421","66","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("422","67","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("423","67","Шамалардың өзгеруін процентпен көрсету","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("424","67","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("425","68","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("426","68","Адам жасына және уақытқа байланысты есептер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("427","68","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("428","69","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("429","69","S=n(n-1), S=n(n-1)/2 формулаларына және кітап газет, журнал беттеріне арналған есептер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("430","69","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("431","70","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("432","70","Қозғалысқа арналған есептер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("433","70","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("434","71","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("435","71","Жұмысқа арналған есептер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("436","71","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("437","72","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("438","72","Концентрацияға арналған есептер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("439","72","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("440","73","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("441","73","Мәтіндік есептер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("442","73","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("443","74","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("444","74","Жиындардың қиылысуына арналған есептер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("445","74","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("446","75","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("447","75","Графиктермен және кестелермен берілген есептер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("448","75","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("449","76","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("450","76","Диаграммамен берілген есептер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("451","76","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("452","77","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("453","77","Геометриялық мағынадағы есептер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("454","77","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("455","78","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("456","78","Логикалық есептер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("457","78","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("458","79","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("459","79","Комбинаторика","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("460","79","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("461","80","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("462","80","Ықтималдылық","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("463","80","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("464","81","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("465","81","Тестпен дайындық 1","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("466","81","Тестпен дайындық 2","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("467","81","Тестпен дайындық 3","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("468","81","Тестпен дайындық 4","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("469","81","Тестпен дайындық 5","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("470","81","Тестпен дайындық 6","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("471","81","Қорытынды","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("472","82","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("473","82","Бірқалыпты қозғалыс","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("474","82","Орташа жылдамдық","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("475","82","Үдемелі қозғалыс","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("476","82","Еркін құлаған дене қозғалысы","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("477","82","Шеңбер бойымен қозғалыс","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("478","82","Горизонтқа бұрыш жасай лақтырылған дене қозғалысы","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("479","82","Аралық бақылау. Кинематика","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("480","82","Апталық есептер. Кинематика","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("481","82","Қорытынды","10","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("482","83","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("483","83","Динамика бастауы","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("484","83","Динамикадағы күштердің түрлері","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("485","83","Денелер жүйесінің динамикасы","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("486","83","Өткен тақырыптарды қайталау есептері","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("487","83","Көлбеу жазықтықтағы қозғалыс динамикасы","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("488","83","Статика","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("489","83","Механикалық кернеу. Юнг модулі","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("490","83","Аралық бақылау. Динамика","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("491","83","Апталық есептер. Динамика","10","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("492","83","Қорытынды","11","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("493","84","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("494","84","Импульс. Импульстің сақталу за","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("495","84","Импульс есептері 2","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("496","84","Жұмыс. Энергия","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("497","84","Энергияның сақталу заңы","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("498","84","Қуат. ПӘК","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("499","84","Сақталу заңдары бөлімін қайталау есептері","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("500","84","Аралық бақылау: Сақталу заңдары","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("501","84","Апталық есептер. Сақталу заңдары","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("502","84","Қорытынды","10","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("503","85","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("504","85","Механикалық тербелістер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("505","85","Механикалық толқындар","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("506","85","Аралық бақылау. Сақталу заңдары және мех-қ терб-р мен толқындар","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("507","85","Апталық есептер. Механикалық тербелістер мен толқындар","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("508","85","Қорытынды","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("509","86","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("510","86","Гидростатика. Гидродинамика","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("511","86","Апталық есептер. Гидростатика. Гидродинамика","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("512","86","Қорытынды","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("513","87","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("514","87","Молекулалық физика бастамасы","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("515","87","МКТ-ның негізгі теңдеуі","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("516","87","Менделеев-Клапейрон теңдеуі","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("517","87","Изопроцестер","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("518","87","Термодинамика","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("519","87","Жылу машиналары","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("520","87","Жылу алмасу","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("521","87","Ылғалдылық","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("522","87","Бақылауға дайындық есептері 1","10","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("523","87","Бақылауға дайындық есептері 2","11","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("524","87","Аралық бақылау. Молекулалық физика","12","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("525","87","Апталық есептер. Молекулалық физика","13","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("526","87","Қорытынды","14","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("527","88","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("528","88","Электр өрісі. Кулон заңы","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("529","88","Электр өрісінің жұмысы. Потенциалдық энергия","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("530","88","Конденсатор","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("531","88","Апталық есептер. Электр өрісі","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("532","88","Қорытынды","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("533","89","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("534","89","Тұрақты электр тогы. Ток күші. Кедергі. Жұмыс. Қуат","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("535","89","Тізбектерді қосу","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("536","89","ЭҚК","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("537","89","Әр түрлі ортадағы электр тогы","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("538","89","Бақылауға дайындық есептері. Электр өрісі","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("539","89","Бақылауға дайындық есептері. Тұрақты ток","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("540","89","Аралық бақылау. Электр өрісі. Тұрақты ток","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("541","89","Апталық есептер. Тұрақты ток","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("542","89","Қорытынды","10","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("543","90","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("544","90","Магнит өрісі басы","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("545","90","Заттардағы магнит өрісі","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("546","90","Электромагниттік индукция. Магнит ағыны","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("547","90","Өздік индукция","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("548","90","Апталық есептер. Магнит өрісі","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("549","90","Қорытынды","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("550","91","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("551","91","Еркін электромагниттік тербелістер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("552","91","Еріксіз электромагниттік тербелістер","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("553","91","Трансформатор","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("554","91","Электромагниттік толқындар","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("555","91","Бақылауға дайындық есептері. Магнит өрісі","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("556","91","Бақылауға дайындық есептері. Электромагниттік тербелістер мен толқындар","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("557","91","Аралық бақылау. Магнит өрісі. Электромагниттік тербелістер мен толқындар ","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("558","91","Апталық есептер. Электромагниттік тербелістер мен толқындар","9","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("559","91","Қорытынды","10","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("560","92","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("561","92","Геометриялық оптика","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("562","92","Апталық есептер. Геометриялық оптика","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("563","92","Толқындық оптика","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("564","92","Спектральді анализ","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("565","92","Люминисценция","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("566","92","Апталық есептер. Толқындық оптика","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("567","92","Қорытынды","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("568","93","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("569","93","Фотоэффект","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("570","93","Фотоэффект есептері 2","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("571","93","Бақылауға дайындық есептері. Оптика және фотоэффект","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("572","93","Аралық бақылау. Опитка. Фотоэффект.","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("573","93","Апталық есептер. Фотоэффект","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("574","93","Қорытынды","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("575","94","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("576","94","Атом және оның құрылысы","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("577","94","Радиоактивтілік","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("578","94","Зарядталған бөлшектерді тіркегіштер","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("579","94","Ядролық реакция","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("580","94","Апталық есептер. Ядролық физика","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("581","94","Қорытынды","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("582","95","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("583","95","Эйнштейннің салыстырмалылық теориясы","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("584","95","Бақылауға дайындық есептері. Ядролық физика. Салыстырмалылық теория","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("585","95","Аралық бақылау. Ядролық физика. Салыстырмалылық теория","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("586","95","Апталық есептер. Эйнштейннің салыстырмалылық теориясы","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("587","95","Қорытынды","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("588","96","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("589","96","Тестпен дайындық 1","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("590","96","Тестпен дайындық 2","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("591","96","Тестпен дайындық 3","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("592","96","Тестпен дайындық 4","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("593","96","Тестпен дайындық 5","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("594","96","Тестпен дайындық 6","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("595","96","Қорытынды","8","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("596","97","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("597","97","Химия пәні. Таза зат және қоспа. Құбылыс. Химиялық теңдеу. Атом. Химиялық элемент. Салыстырмалы атом","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("598","97","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("599","98","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("600","98","Химиялық элемент таңбалары. Құрам тұрақтылық заңы. Химиялық формула. Салыстырмалы молекулалық масса.","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("601","98","Валенттілік. Масса сақталу заңы. Химиялық теңдеу. Реакция типтері","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("602","98","Зат мөлшері. Моль. Мольдік масса. Мольдік көлем. Тығыздық. Газдың салыстырмалы тығыздығы. Авогадро з","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("603","98","Қорытынды","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("604","99","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("605","99","Оксидтер. Қышқылдар.","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("606","99","Негіздер. Тұздар","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("607","99","Генетикалық байланыс","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("608","99","Қорытынды","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("609","100","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("610","100","Периодтық жүйе. Атом құрылысы ","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("611","100","Электрондар қозғалысы. Энергетикалық деңгейлердің құрылысы","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("612","100","Атом құрылысы туралы ілім тұрғысынан периодтық заң. Элементтердің периодты түрде өзгеретін қасиеттер","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("613","100","Қорытынды","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("614","101","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("615","101","Апталық есеп 1","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("616","101","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("617","102","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("618","102"," Коваленттік байланыс.Ков.байланыс түзуінің донорлы-акцепторлы механизмі","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("619","102","Иондық байланыс. Ионды және ковалентті қосылыстардың қасиеттері. Металдық ж/е сутектік байланыс","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("620","102","Қорытынды","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("621","103","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("622","103","Су - еріткіш. Ерітінді. Еріген заттың массалық үлесін анықтау. Күрделі заттағы элементтің массалық ү","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("623","103","Өнімнің іс жүзіндегі шығымы. Қоспадағы заттың массалық үлесі. ","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("624","103","Қорытынды","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("625","104","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("626","104","Электролиттік диссоциация теориясы. Қышқылдың, негіздің, тұздың электролиттік диссоциациялануы.","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("627","104","Диссоциациялану дәрежесі. Күшті және әлсіз электролиттер. Ион алмасу реакциялары.","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("628","104","Тұздар гидролизі","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("629","104","Қорытынды","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("630","105","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("631","105","Тотығу және тотықсыздану реакциялары","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("632","105","Электролиз","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("633","105","Қорытынды","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("634","106","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("635","106","Апталық есеп 2","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("636","106","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("637","107","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("638","107","Химиялық реакцияның жылдамдығы. Химиялық реакцияға өршіткінің әсері","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("639","107","Химиялық тепе–теңдік. Химиялық тепе–теңдіктің ығысуы. Ле–Шателье принципі.","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("640","107","Қорытынды","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("641","108","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("642","108","Металдар және бейметалдар. Негізгі және қосымша топша металдар. Металдардың құрылысы, физикалық және","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("643","108","S-элементтер. Жалпы сипаттамасы. Сілтілік металдар. Натрий және калий","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("644","108","ІІ топтың негізгі топша элементтерінің жалпы сипаттамасы. Магний және кальций. Судың кермектігі","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("645","108","D-элементтер. Мыс, мырыш және хром","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("646","108","Темір. Металдардың жемірілуі. Алюминий","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("647","108","Қорытынды","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("648","109","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("649","109","IVA топша элементтері. Көміртек, кремний","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("650","109","VА топ элементтері. Азот. Фосфор. Күкірт","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("651","109","Күкірт. Оттек және сутек","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("652","109","Галогендер. Хлор. Йод","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("653","109","Қорытынды","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("654","110","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("655","110","Органикалық химия ерекшеліктері. Құрылыс теориясы. Изомерия. Гибридтену және гибридтелген орбитальда","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("656","110","Қорытынды","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("657","111","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("658","111","Алкандар","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("659","111","Циклоалкандар","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("660","111","Қорытынды","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("661","112","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("662","112","Алкендер","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("663","112","Алкадиендер","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("664","112","Алкиндер","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("665","112","Арендер. Бензол.","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("666","112","Қорытынды","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("667","113","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("668","113","Қаныққан біратомды спирттер ","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("669","113","Көпатомды спирттер","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("670","113","Фенолдар","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("671","113","Қорытынды","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("672","114","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("673","114"," Альдегид пен кетондар ","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("674","114","Карбон қышқылдары","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("675","114","Қорытынды","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("676","115","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("677","115","Жай және күрделі эфирлер.","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("678","115","Майлар. Сабын және синтетикалық жуғыш заттар","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("679","115","Қорытынды","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("680","116","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("681","116","Моносахаридтер. Глюкоза","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("682","116","Дисахаридтер","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("683","116","Полисахаридтер","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("684","116","Қорытынды","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("685","117","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("686","117","Нитроқосылыстар","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("687","117","Аминдер. Аминқышқылдары.","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("688","117","Нәруыздар. Нуклеин қышқылы.","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("689","117","Қорытынды","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("690","118","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("691","118","Синтездік жоғары молекулалық қосылыстар","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("692","118","Табиғы және мұнайға серік газдар. Мұнай. Отын","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("693","118","Қорытынды","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("694","119","Кіріспе","1","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("695","119","Тестпен жұмыс 1","2","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("696","119","Тестпен жұмыс 2","3","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("697","119","Тестпен жұмыс 3","4","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("698","119","Тестпен жұмыс 4","5","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("699","119","Тестпен жұмыс 5","6","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("700","119","Тестпен жұмыс 6","7","2019-09-03 19:48:37");
INSERT INTO subtopic VALUES("701","119","Қорытынды","8","2019-09-03 19:48:37");



DROP TABLE IF EXISTS topic;

CREATE TABLE `topic` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `subject_id` int(6) NOT NULL,
  `title` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `topic_order` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=120 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO topic VALUES("19","16","Теңсіздіктер","2019-09-03 19:48:36","4");
INSERT INTO topic VALUES("18","16","Теңдеулер","2019-09-03 19:48:36","3");
INSERT INTO topic VALUES("17","16","Өрнекті ықшамдау","2019-09-03 19:48:36","2");
INSERT INTO topic VALUES("16","16","Фундамент","2019-09-03 19:48:36","1");
INSERT INTO topic VALUES("20","16","Бөлшектің бөлімін иррационалдықтан арылту","2019-09-03 19:48:37","5");
INSERT INTO topic VALUES("21","16","Иррационал теңдеулер","2019-09-03 19:48:37","6");
INSERT INTO topic VALUES("22","16","Прогрессия","2019-09-03 19:48:37","7");
INSERT INTO topic VALUES("23","16","Тригонометриялық ықшамдаулар","2019-09-03 19:48:37","8");
INSERT INTO topic VALUES("24","16","Тригонометриялық теңдеулер","2019-09-03 19:48:37","9");
INSERT INTO topic VALUES("25","16","Тригонометриялық теңсіздіктер","2019-09-03 19:48:37","10");
INSERT INTO topic VALUES("26","16","Тригонометриялық теңсіздіктер және теңдеулер жүйесі","2019-09-03 19:48:37","11");
INSERT INTO topic VALUES("27","16","Модуль","2019-09-03 19:48:37","12");
INSERT INTO topic VALUES("28","16","Бөлшек дәреже","2019-09-03 19:48:37","13");
INSERT INTO topic VALUES("29","16","Көрсеткіштік теңдеулер мен теңсіздіктер","2019-09-03 19:48:37","14");
INSERT INTO topic VALUES("30","16","Логарифмдік ықшамдаулар","2019-09-03 19:48:37","15");
INSERT INTO topic VALUES("31","16","Логарифмдік теңдеулер","2019-09-03 19:48:37","16");
INSERT INTO topic VALUES("32","16","Логарифмдік теңсіздіктер","2019-09-03 19:48:37","17");
INSERT INTO topic VALUES("33","16","Логарифмдік теңдеулер мен теңсіздіктер жүйесі","2019-09-03 19:48:37","18");
INSERT INTO topic VALUES("34","16","Туынды","2019-09-03 19:48:37","19");
INSERT INTO topic VALUES("35","16","Алғашқы функция. Интеграл","2019-09-03 19:48:37","20");
INSERT INTO topic VALUES("36","16","Функция","2019-09-03 19:48:37","21");
INSERT INTO topic VALUES("37","16","Мәселе есептер","2019-09-03 19:48:37","22");
INSERT INTO topic VALUES("38","16","Тестпен жұмыс","2019-09-03 19:48:37","23");
INSERT INTO topic VALUES("39","17","Физикалық география","2019-09-03 19:48:37","1");
INSERT INTO topic VALUES("40","17","Материктер","2019-09-03 19:48:37","2");
INSERT INTO topic VALUES("41","17","Қазақстанның физикалық географиясы","2019-09-03 19:48:37","3");
INSERT INTO topic VALUES("42","17","Қазақстанның экономикалық және әлеуметтік географиясы","2019-09-03 19:48:37","4");
INSERT INTO topic VALUES("43","17","Дүние жүзінің экономикалық және әлеуметтік географиясы","2019-09-03 19:48:37","5");
INSERT INTO topic VALUES("44","18","Үшбұрыштар","2019-09-03 19:48:37","1");
INSERT INTO topic VALUES("45","18","Төртбұрыштар","2019-09-03 19:48:37","2");
INSERT INTO topic VALUES("46","18","Шеңбер","2019-09-03 19:48:37","3");
INSERT INTO topic VALUES("47","18","Вектор","2019-09-03 19:48:37","4");
INSERT INTO topic VALUES("48","18","Түзудің және шеңбердің теңдеуі","2019-09-03 19:48:37","5");
INSERT INTO topic VALUES("49","18","Көпжақтар","2019-09-03 19:48:37","6");
INSERT INTO topic VALUES("50","18","Айналу денелері","2019-09-03 19:48:37","7");
INSERT INTO topic VALUES("51","18","Денелер комбинациясы","2019-09-03 19:48:37","8");
INSERT INTO topic VALUES("52","18","Жазықтыққа көлбеуленген фигуралар","2019-09-03 19:48:37","9");
INSERT INTO topic VALUES("53","19","Ежелгі дәуірдегі Қазақстан","2019-09-03 19:48:37","1");
INSERT INTO topic VALUES("54","19","Ерте орта ғасырлардағы Қазақстан","2019-09-03 19:48:37","2");
INSERT INTO topic VALUES("55","19","Дамыған орта ғасырдағы Қазақстан","2019-09-03 19:48:37","3");
INSERT INTO topic VALUES("56","19","Кейінгі орта ғасырдағы Қазақстан","2019-09-03 19:48:37","4");
INSERT INTO topic VALUES("57","19","Қазақ-жоңғар қатынастары","2019-09-03 19:48:37","5");
INSERT INTO topic VALUES("58","19","Ресейдің отарлауы ","2019-09-03 19:48:37","6");
INSERT INTO topic VALUES("59","19","Ресей құрамындағы Қазақстан","2019-09-03 19:48:37","7");
INSERT INTO topic VALUES("60","19","XX ғ. бірінші жартысындағы Қазақстан","2019-09-03 19:48:37","8");
INSERT INTO topic VALUES("61","19","XXғ. екінші жартысындағы Қазақстан","2019-09-03 19:48:37","9");
INSERT INTO topic VALUES("62","19","Тәуелсіз Қазақстан","2019-09-03 19:48:37","10");
INSERT INTO topic VALUES("63","20","Сандар теориясы 1","2019-09-03 19:48:37","1");
INSERT INTO topic VALUES("64","20","Сандар теориясы 2","2019-09-03 19:48:37","2");
INSERT INTO topic VALUES("65","20","Тура пропорционалдық. Кері пропорционалдық. Санды бөліктерге бөлу. Масштаб","2019-09-03 19:48:37","3");
INSERT INTO topic VALUES("66","20","Пайызға арналған есептер","2019-09-03 19:48:37","4");
INSERT INTO topic VALUES("67","20","Шамалардың өзгеруін процентпен көрсету","2019-09-03 19:48:37","5");
INSERT INTO topic VALUES("68","20","Адам жасына және уақытқа байланысты есептер","2019-09-03 19:48:37","6");
INSERT INTO topic VALUES("69","20","S=n(n-1), S=n(n-1)/2 формулаларына және кітап газет, журнал беттеріне арналған есептер","2019-09-03 19:48:37","7");
INSERT INTO topic VALUES("70","20","Қозғалысқа арналған есептер","2019-09-03 19:48:37","8");
INSERT INTO topic VALUES("71","20","Жұмысқа арналған есептер","2019-09-03 19:48:37","9");
INSERT INTO topic VALUES("72","20","Концентрацияға арналған есептер","2019-09-03 19:48:37","10");
INSERT INTO topic VALUES("73","20","Мәтіндік есептер","2019-09-03 19:48:37","11");
INSERT INTO topic VALUES("74","20","Жиындардың қиылысуына арналған есептер","2019-09-03 19:48:37","12");
INSERT INTO topic VALUES("75","20","Графиктермен және кестелермен берілген есептер","2019-09-03 19:48:37","13");
INSERT INTO topic VALUES("76","20","Диаграммамен берілген есептер","2019-09-03 19:48:37","14");
INSERT INTO topic VALUES("77","20","Геометриялық мағынадағы есептер","2019-09-03 19:48:37","15");
INSERT INTO topic VALUES("78","20","Логикалық есептер","2019-09-03 19:48:37","16");
INSERT INTO topic VALUES("79","20","Комбинаторика","2019-09-03 19:48:37","17");
INSERT INTO topic VALUES("80","20","Ықтималдылық","2019-09-03 19:48:37","18");
INSERT INTO topic VALUES("81","20","Тестпен дайындық","2019-09-03 19:48:37","19");
INSERT INTO topic VALUES("82","21","Кинематика","2019-09-03 19:48:37","1");
INSERT INTO topic VALUES("83","21","Динамика","2019-09-03 19:48:37","2");
INSERT INTO topic VALUES("84","21","Сақталу заңдары","2019-09-03 19:48:37","3");
INSERT INTO topic VALUES("85","21","Механикалық тербелістер мен толқындар","2019-09-03 19:48:37","4");
INSERT INTO topic VALUES("86","21","Гидростатика. Гидродинамика","2019-09-03 19:48:37","5");
INSERT INTO topic VALUES("87","21","Молекулалық физика","2019-09-03 19:48:37","6");
INSERT INTO topic VALUES("88","21","Электр өрісі","2019-09-03 19:48:37","7");
INSERT INTO topic VALUES("89","21","Тұрақты ток","2019-09-03 19:48:37","8");
INSERT INTO topic VALUES("90","21","Магнит өрісі","2019-09-03 19:48:37","9");
INSERT INTO topic VALUES("91","21","Электромагниттік тербелістер мен толқындар","2019-09-03 19:48:37","10");
INSERT INTO topic VALUES("92","21","Оптика","2019-09-03 19:48:37","11");
INSERT INTO topic VALUES("93","21","Фотоэффект","2019-09-03 19:48:37","12");
INSERT INTO topic VALUES("94","21","Ядролық физика","2019-09-03 19:48:37","13");
INSERT INTO topic VALUES("95","21","Эйнштейннің салыстырмалылық теориясы","2019-09-03 19:48:37","14");
INSERT INTO topic VALUES("96","21","Тестпен дайындық","2019-09-03 19:48:37","15");
INSERT INTO topic VALUES("97","22","Химияның негізгі түсініктері","2019-09-03 19:48:37","1");
INSERT INTO topic VALUES("98","22","Химиядағы  сандық есептеу  ","2019-09-03 19:48:37","2");
INSERT INTO topic VALUES("99","22","Бейорганикалық заттар классы","2019-09-03 19:48:37","3");
INSERT INTO topic VALUES("100","22","Периодтық заң. Атом құрылысы","2019-09-03 19:48:37","4");
INSERT INTO topic VALUES("101","22","Апталық есеп 1","2019-09-03 19:48:37","5");
INSERT INTO topic VALUES("102","22","Химиялық байланыс. Зат құрылысы","2019-09-03 19:48:37","6");
INSERT INTO topic VALUES("103","22","Ерітінді","2019-09-03 19:48:37","7");
INSERT INTO topic VALUES("104","22","Электролиттік диссоциация. Тұздар гидролизі","2019-09-03 19:48:37","8");
INSERT INTO topic VALUES("105","22"," Тотығу-тотықсыздану реакциялары","2019-09-03 19:48:37","9");
INSERT INTO topic VALUES("106","22","Апталық есеп 2","2019-09-03 19:48:37","10");
INSERT INTO topic VALUES("107","22","Химиялық кинетика және катализ. Химиялық тепе-теңдік ","2019-09-03 19:48:37","11");
INSERT INTO topic VALUES("108","22"," Металдардың жалпы қасиеттері","2019-09-03 19:48:37","12");
INSERT INTO topic VALUES("109","22","Бейметалдар. IVА топша элементтері","2019-09-03 19:48:37","13");
INSERT INTO topic VALUES("110","22","Органикалық химия","2019-09-03 19:48:37","14");
INSERT INTO topic VALUES("111","22","Қаныққан көмірсутектер","2019-09-03 19:48:37","15");
INSERT INTO topic VALUES("112","22","Қанықпаған көмірсутектер. Арендер. Галогентуындылар.","2019-09-03 19:48:37","16");
INSERT INTO topic VALUES("113","22","Спирттер","2019-09-03 19:48:37","17");
INSERT INTO topic VALUES("114","22","Карбонилді қосылыстар","2019-09-03 19:48:37","18");
INSERT INTO topic VALUES("115","22","Жай және күрделі эфирлер. Майлар","2019-09-03 19:48:37","19");
INSERT INTO topic VALUES("116","22","Көмірсулар","2019-09-03 19:48:37","20");
INSERT INTO topic VALUES("117","22","Азотты органикалық қосылыстар","2019-09-03 19:48:37","21");
INSERT INTO topic VALUES("118","22","Полимер және мұнай","2019-09-03 19:48:37","22");
INSERT INTO topic VALUES("119","22","Тестпен жұмыс","2019-09-03 19:48:37","23");



DROP TABLE IF EXISTS tutorial_document;

CREATE TABLE `tutorial_document` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `subtopic_id` int(6) NOT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `upload_date` datetime NOT NULL DEFAULT current_timestamp(),
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




DROP TABLE IF EXISTS tutorial_video;

CREATE TABLE `tutorial_video` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `subtopic_id` int(6) NOT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `duration` int(20) NOT NULL,
  `video_order` int(11) NOT NULL,
  `upload_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=162 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO tutorial_video VALUES("1","32","https://vimeo.com/273478341","Натурал сандарға амалдар қолдану","3520","1","2019-09-03 19:48:36");
INSERT INTO tutorial_video VALUES("2","33","https://vimeo.com/273484645","Жай бөлшектерді қосу азайту","2955","1","2019-09-03 19:48:36");
INSERT INTO tutorial_video VALUES("3","34","https://vimeo.com/273488385","Жай бөлшектерді көбейту бөлу","1425","1","2019-09-03 19:48:36");
INSERT INTO tutorial_video VALUES("4","35","https://vimeo.com/273833318","Ондық бөлшектер","2464","1","2019-09-03 19:48:36");
INSERT INTO tutorial_video VALUES("5","36","https://vimeo.com/273846887","Теріс сандар","1214","1","2019-09-03 19:48:36");
INSERT INTO tutorial_video VALUES("6","37","https://vimeo.com/273848466","Аралас амалдар","1737","1","2019-09-03 19:48:36");
INSERT INTO tutorial_video VALUES("7","38","https://vimeo.com/273850632","Теңдеулер","862","1","2019-09-03 19:48:36");
INSERT INTO tutorial_video VALUES("8","39","https://vimeo.com/273852853","Дәреже","1387","1","2019-09-03 19:48:36");
INSERT INTO tutorial_video VALUES("9","40","https://vimeo.com/273854318","Теріс дәреже","593","1","2019-09-03 19:48:36");
INSERT INTO tutorial_video VALUES("10","41","https://vimeo.com/273866944","Түбір","1708","1","2019-09-03 19:48:36");
INSERT INTO tutorial_video VALUES("11","46","https://vimeo.com/273868659","Көпмүшелер","1792","1","2019-09-03 19:48:36");
INSERT INTO tutorial_video VALUES("12","47","https://vimeo.com/273873365","Ортақ көбейткішті жақша сыртына, топтау әдісі","1362","1","2019-09-03 19:48:36");
INSERT INTO tutorial_video VALUES("13","61","https://vimeo.com/273875804","Квадрат теңдеу арқылы көбейткіштерге жіктеу","632","1","2019-09-03 19:48:36");
INSERT INTO tutorial_video VALUES("14","67","https://vimeo.com/274201306","Теңсіздіктер","1059","1","2019-09-03 19:48:36");
INSERT INTO tutorial_video VALUES("15","68","https://vimeo.com/274202412","Интервалдар әдісімен теңсіздіктерді шешу","1208","1","2019-09-03 19:48:36");
INSERT INTO tutorial_video VALUES("16","69","https://vimeo.com/274203791","Бөлшек теңсіздіктер","1288","1","2019-09-03 19:48:36");
INSERT INTO tutorial_video VALUES("17","70","https://vimeo.com/274204534","Теңсіздіктер жүйесі","1549","1","2019-09-03 19:48:36");
INSERT INTO tutorial_video VALUES("18","75","https://vimeo.com/274205784","Бөлшектің бөлімін иррационалдықтан арылту","1488","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("19","78","https://vimeo.com/274207520","Иррационалдық теңдеулер","1450","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("20","83","https://vimeo.com/274208461","Арифметикалық прогрессия","872","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("21","85","https://vimeo.com/274208986","Геометриялық прогрессия","1021","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("22","87","https://vimeo.com/274212933","Аралас прогрессия","807","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("23","91","https://vimeo.com/274213650","1. Тригонометрия басы 1","2365","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("24","91","https://vimeo.com/274216248","2. Тригонометрия басы 2","1729","2","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("25","91","https://vimeo.com/274218997","3. Негізгі тригонометриялық формулалар","695","3","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("26","92","https://vimeo.com/274219882","Тригонометриялық формулаларды қолдану арқылы тригонометриялық өрнекті есептеу","757","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("27","93","https://vimeo.com/274220652","Келтіру формулалары. Функцияны периодтан арылту","1137","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("28","94","https://vimeo.com/274224155","Екі еселенген аргумент формулалары","1244","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("29","95","https://vimeo.com/274225873","Дәрежені төмендету формулалары","808","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("30","96","https://vimeo.com/274230417","Аргументтерді қосу және азайту формулалары","952","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("31","97","https://vimeo.com/274231777","Тригонометриялық функциялардың қосындысын көбейтіндіге және көбейтіндісін қосындыға түрлендіру","1055","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("32","98","https://vimeo.com/274232767","Кері тригонометриялық функциялар","1819","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("33","104","https://vimeo.com/274619841","Тригонометриялық теңдеулер","2391","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("34","111","https://vimeo.com/274621248","1. cosx және sinx теңсіздіктері","2193","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("35","111","https://vimeo.com/274622991","2. tgx және ctgx теңсіздіктері","1275","2","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("36","113","https://vimeo.com/274624234","Жаңа айнымалы енгізу арқылы тригонометриялық теңсіздіктерді шешу","1157","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("37","116","https://vimeo.com/274625496","Тригонометриялық теңсіздіктер жүйесі","948","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("38","117","https://vimeo.com/274627677","Тригонометриялық теңдеулер жүйесі","629","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("39","124","https://vimeo.com/274628280","Модуль таңбасы бар теңдеулер","1885","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("40","125","https://vimeo.com/274631126","Модуль таңбасы бар теңсіздіктер","1680","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("41","129","https://vimeo.com/274632687","Бөлшек дәреже","467","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("42","132","https://vimeo.com/274636578","Көрсеткіштік теңдеулер","701","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("43","133","https://vimeo.com/274639399","Көбейткішке жіктеу арқылы көрсеткіштік теңдеулерді шешу","598","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("44","135","https://vimeo.com/274640803","Көрсеткіштік теңсіздіктер","688","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("45","136","https://vimeo.com/274641949","Жаңа айнымалы енгізу арқылы көрсеткіштік теңсіздіктерді шешу","817","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("46","141","https://vimeo.com/274644146","Логарифмдер","1069","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("47","142","https://vimeo.com/274653213","Логарифмдерді қосу және азайту формулалары","241","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("48","143","https://vimeo.com/274665514","Басқа негізге көшу формулаларын пайдалана отырып логарифмдерді есептеу","432","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("49","147","https://vimeo.com/274668627","Логарифмдік теңдеулер","1158","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("50","148","https://vimeo.com/274670316","Логарифмдерді қосу және азайту формулаларын пайдалану арқылы және жаңа айнымалы енгізу арқылы логарифмдік теңдеулерді шешу","847","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("51","149","https://vimeo.com/277455543","Бір негізге келтіру арқылы логарифмдік теңдеулерді шешу","939","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("52","150","https://vimeo.com/277455756","Екі жағын логарифмдеу арқылы логарифмдік теңдеулерді шешу","582","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("53","153","https://vimeo.com/277455887","Логарифмдік теңсіздіктер","819","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("54","165","https://vimeo.com/277634067","Туынды","1969","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("55","180","https://vimeo.com/277634252","Алғашқы функция","1623","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("56","184","https://vimeo.com/277634391","Интеграл арқылы фигураның ауданын табу","2021","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("57","188","https://vimeo.com/277646626","Функцияның түрлері. Жұп және тақ функция. Анықталу облысы","2691","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("58","189","https://vimeo.com/277646881","Функцияның мәндер облысы","2275","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("59","190","https://vimeo.com/277647118","Функцияның ең кіші оң периодын табу. Кері функция","984","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("60","196","https://vimeo.com/277647228","Тура пропорционалдық. Кері пропорционалдық. Масштаб","1016","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("61","197","https://vimeo.com/272754915","Пайызға арналған есептер","1380","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("62","199","https://vimeo.com/273061154","Қозғалысқа арналған есептер","1736","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("63","200","https://vimeo.com/273442554","Жұмысқа арналған есептер","1383","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("64","201","https://vimeo.com/273447004","Концентрацияға арналған есептер","955","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("65","266","https://vimeo.com/275046574","Үшбұрыш және оның элементтері","1821","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("66","268","https://vimeo.com/275050265","Медиана. Биссектриса. Биіктік","1162","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("67","269","https://vimeo.com/275052428","Тең бүйірлі және тік бұрышты үшбұрыш","1186","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("68","270","https://vimeo.com/275053903","Тең қабырғалы үшбұрыш","395","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("69","271","https://vimeo.com/275056563","Ұқсас үшбұрыштар","790","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("70","275","https://vimeo.com/275057809","Параллелограмм","694","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("71","276","https://vimeo.com/275060746","Тіктөртбұрыш және квадрат","1224","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("72","277","https://vimeo.com/275062905","Ромб","780","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("73","278","https://vimeo.com/275076737","Трапеция","1504","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("74","279","https://vimeo.com/275079328","Көпбұрыштар","1234","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("75","280","https://vimeo.com/275082999","Жалпы төртбұрыштар қасиеттері","352","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("76","284","https://vimeo.com/275085784","Шеңбер және оның элементтері","2092","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("77","291","https://vimeo.com/275089375","Вектор","4671","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("78","295","https://vimeo.com/276211894","Декарттық координаталар, түзудің және шеңбердің теңдеуі","4643","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("79","299","https://vimeo.com/276216072","Призма","2293","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("80","300","https://vimeo.com/276368208","Пирамида","2222","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("81","301","https://vimeo.com/276370225","Қиық пирамида","1067","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("82","305","https://vimeo.com/276371344","Цилиндр","1276","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("83","306","https://vimeo.com/276378958","Конус","1091","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("84","308","https://vimeo.com/276380812","Шар, Сфера","846","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("85","309","https://vimeo.com/276381933","Фигураларды айналдырғанда пайда болатын денелер","1727","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("86","313","https://vimeo.com/276385700","Стереометриялық денелер комбинациясы","1906","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("87","316","https://vimeo.com/276393893","Кеңістіктегі түзулер мен жазықтықтар","1404","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("88","411","https://vimeo.com/272709611","Сандар теориясы 1","2409","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("89","414","https://vimeo.com/272711789","Сандар теориясы 2","1921","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("90","417","https://vimeo.com/273470302","Тура проп, кері проп, масштаб","1016","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("91","420","https://vimeo.com/272754915","Пайызға арналған есептер","1380","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("92","426","https://vimeo.com/272713485","Адам жасына және уақытқа байланысты есептер","2287","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("93","429","https://vimeo.com/272715314","Газет-журнал беттері","2384","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("94","432","https://vimeo.com/273061154","Қозғалысқа арналған есептер","1736","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("95","435","https://vimeo.com/273442554","Жұмысқа арналған есептер","1383","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("96","438","https://vimeo.com/273447004","Концентрацияға арналған есептер","955","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("97","444","https://vimeo.com/273447839","Жиындардың қиылысуына арналған есептер","734","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("98","453","https://vimeo.com/273448893","Геометриялық мағынадағы есептер","2588","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("99","456","https://vimeo.com/273451790","Логикалық есептер","568","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("100","459","https://vimeo.com/273452608","Комбинаторика","3805","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("101","462","https://vimeo.com/273467758","Ықтималдылық","2172","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("102","473","https://vimeo.com/277040828","Бірқалыпты түзусызықты қозғалыс","1973","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("103","474","https://vimeo.com/277041435","Орташа жылдамдық","800","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("104","475","https://vimeo.com/277041596","Үдемелі қозғалыс","1933","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("105","476","https://vimeo.com/277042247","Еркін құлаған дене қозғалысы","984","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("106","477","https://vimeo.com/277042617","Шеңбер бойымен қозғалыс","1240","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("107","478","https://vimeo.com/277042952","Горизонтқа бұрыш жасай лақтырылған дене қозғалысы","1829","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("108","483","https://vimeo.com/277046067","Динамика басы","2198","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("109","484","https://vimeo.com/277046676","Динамикадағы күштер","2570","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("110","485","https://vimeo.com/277047286","Денелер жүйесінің динамикасы","1132","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("111","487","https://vimeo.com/277047406","Көлбеу жазықтықтағы қозғалыс динамикасы","933","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("112","488","https://vimeo.com/277047521","Статика","1859","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("113","489","https://vimeo.com/277047977","Механикалық кернеу","2058","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("114","494","https://vimeo.com/277217616","Импульс","1316","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("115","496","https://vimeo.com/277217703","Жұмыс. Энергия","1697","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("116","497","https://vimeo.com/277217804","Энергияның сақталу заңы","600","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("117","498","https://vimeo.com/277217840","Қуат, ПӘК","1241","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("118","504","https://vimeo.com/277218022","Механикалық тербелістер","2735","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("119","505","https://vimeo.com/277218435","Механикалық толқындар","1760","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("120","510","https://vimeo.com/277218712","Гидростатика","1745","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("121","514","https://vimeo.com/277220177","Молекулалық физика басы","1425","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("122","515","https://vimeo.com/277220387","МКТ-ның негізгі теңдеуі","1454","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("123","516","https://vimeo.com/277220633","Менделеев-Клапейрон теңдеуі","1455","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("124","517","https://vimeo.com/277220733","Изопроцестер","863","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("125","518","https://vimeo.com/277220845","Термодинамика","2974","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("126","519","https://vimeo.com/277220993","Жылу машиналары","1418","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("127","520","https://vimeo.com/277221103","Жылу алмасу","2837","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("128","521","https://vimeo.com/277221300","Ылғалдылық","1541","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("129","528","https://vimeo.com/277223139","1. Элекр өрісі 1-бөлім","2325","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("130","528","https://vimeo.com/277223419","2. Электр өрісі 2-бөлім","1327","2","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("131","529","https://vimeo.com/277223629","1. Электр өрісінің жұмысы. Потенциалдық энергия","1239","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("132","529","https://vimeo.com/277223841","2. Потенциал","1429","2","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("133","530","https://vimeo.com/277224107","1. Электр сыйымдылығы. Конденсатор","1133","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("134","530","https://vimeo.com/277224321","2. Конденсатор энергиясы. Конденсаторларды тізбектей және параллель қосу","1038","2","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("135","534","https://vimeo.com/277231094","Тұрақты ток","2503","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("136","535","https://vimeo.com/277231674","Тізбектерді тізбектей және параллель қосу","1181","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("137","536","https://vimeo.com/277231899","ЭҚК","1596","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("138","537","https://vimeo.com/277232300","Әр түрлі ортадағы электр тогы. 1-бөлім","1561","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("139","537","https://vimeo.com/277232646","Әр түрлі ортадағы электр тогы. 2-бөлім","1369","2","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("140","537","https://vimeo.com/277232948","Әр түрлі ортадағы электр тогы. 3-бөлім","542","3","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("141","544","https://vimeo.com/277399887","Магнит өрісі 1","2272","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("142","544","https://vimeo.com/277400218","Магнит өрісі 2","2871","2","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("143","544","https://vimeo.com/277400618","Магнит өрісі 3","725","3","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("144","545","https://vimeo.com/277400732","Заттардағы магнит өрісі","862","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("145","546","https://vimeo.com/277400888","Электромагниттік индукция. Магнит ағыны","2751","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("146","547","https://vimeo.com/277401240","Өздік индукция","1394","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("147","551","https://vimeo.com/277412698","Еркін электромагниттік тербелістер","1921","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("148","552","https://vimeo.com/277412845","1. Еріксіз электромагниттік тербелістер","3228","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("149","552","https://vimeo.com/277413159","2. Айымалы электр тогының қуаты","1381","2","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("150","553","https://vimeo.com/277413462","Трансформатор","1239","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("151","554","https://vimeo.com/277413793","Электромагниттік толқындар","2679","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("152","561","https://vimeo.com/277417161","Геометриялық оптика","4822","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("153","563","https://vimeo.com/277417562","Толқындық оптика","4906","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("154","564","https://vimeo.com/277418013","Спектральді анализ","1169","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("155","565","https://vimeo.com/277418158","Люминисценция","1060","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("156","569","https://vimeo.com/277418296","Фотоэффект","2243","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("157","576","https://vimeo.com/277445598","Атомның құрылысы. Массалық ақау. Байланыс энергиясы","3579","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("158","577","https://vimeo.com/277445995","Радиоактивтілік","2072","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("159","578","https://vimeo.com/277446236","Зарядталған бөлшектерді тіркегіштер","1071","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("160","579","https://vimeo.com/277446338","Ядролық реакция","2724","1","2019-09-03 19:48:37");
INSERT INTO tutorial_video VALUES("161","583","https://vimeo.com/277449245","Эйнштейннің салыстырмалылық теориясы","2058","1","2019-09-03 19:48:37");



