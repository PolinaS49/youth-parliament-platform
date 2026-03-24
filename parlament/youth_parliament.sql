-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 24 2026 г., 12:39
-- Версия сервера: 8.0.24
-- Версия PHP: 8.0.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `youth_parliament`
--

-- --------------------------------------------------------

--
-- Структура таблицы `achievements`
--

CREATE TABLE `achievements` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` text,
  `earned_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `achievements`
--

INSERT INTO `achievements` (`id`, `user_id`, `title`, `description`, `earned_date`) VALUES
(1, 5, 'Лучший участник месяца', 'За активное участие в 7 мероприятиях за апрель', '2026-04-30 10:00:00'),
(2, 5, 'Победитель хакатона', '1 место в хакатоне \"Цифровая трансформация\"', '2026-04-17 18:00:00'),
(3, 6, 'Активист', 'Участие в 6 мероприятиях за месяц', '2026-04-30 10:00:00'),
(4, 7, 'Супер-участник', 'Посещение 11 мероприятий за месяц', '2026-04-30 10:00:00'),
(5, 7, 'Лучший видеоролик', 'Победа в конкурсе видеоблогов', '2026-04-27 15:00:00'),
(6, 7, 'Грантополучатель', 'Получение гранта на социальный проект', '2026-04-12 12:00:00'),
(7, 8, 'Тех-лидер', 'Лучший технический проект на хакатоне', '2026-04-17 18:00:00'),
(8, 9, 'Социальный активист', 'Самый активный участник социальных проектов', '2026-04-30 10:00:00'),
(9, 9, 'Медийное лицо', 'Лучший контент в социальных сетях', '2026-04-25 12:00:00'),
(10, 10, 'Спортсмен года', 'Победа в спортивном турнире', '2026-04-29 16:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `events`
--

CREATE TABLE `events` (
  `id` int NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text,
  `event_date` datetime NOT NULL,
  `location` varchar(200) DEFAULT NULL,
  `category` enum('IT','Социальное проектирование','Медиа','Другое') DEFAULT 'Другое',
  `difficulty_coefficient` decimal(3,2) DEFAULT '1.00',
  `points_awarded` int DEFAULT '10',
  `bonus_description` text,
  `organizer_id` int DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `location`, `category`, `difficulty_coefficient`, `points_awarded`, `bonus_description`, `organizer_id`, `is_verified`, `created_at`) VALUES
(1, 'Хакатон \"Цифровая трансформация\"', 'Командное соревнование по разработке цифровых решений для городской среды. Участники создают прототипы приложений за 48 часов.', '2026-04-15 10:00:00', 'Москва, Технопарк Сколково', 'IT', '1.80', 50, 'Победители получают стажировку в IT-компании и мерч', 1, 1, '2026-03-23 12:32:51'),
(2, 'Воркшоп по Python для начинающих', 'Интенсивный курс по основам программирования на Python. Практические задания и проектная работа.', '2026-04-05 14:00:00', 'Онлайн (Zoom)', 'IT', '1.20', 20, 'Сертификат об окончании курса', 1, 1, '2026-03-23 12:32:51'),
(3, 'IT-лекторий: Искусственный интеллект', 'Лекция о современных трендах в ИИ и машинном обучении от эксперта из Яндекса', '2026-04-20 18:00:00', 'Москва, Коворкинг \"Точка кипения\"', 'IT', '1.00', 15, 'Билеты на конференцию AI Journey', 1, 1, '2026-03-23 12:32:51'),
(4, 'Форум молодежных инициатив', 'Площадка для презентации социальных проектов и поиска партнеров. Грантовый конкурс с призовым фондом 500 000 руб.', '2026-04-10 09:00:00', 'Москва, Цифровое деловое пространство', 'Социальное проектирование', '1.50', 40, 'Грант на реализацию проекта', 3, 1, '2026-03-23 12:32:51'),
(5, 'Школа социального проектирования', 'Образовательная программа по разработке и упаковке социальных проектов. 5 интенсивных занятий.', '2026-04-07 15:00:00', 'Санкт-Петербург, Пространство \"Среда\"', 'Социальное проектирование', '1.30', 25, 'Наставничество от экспертов', 3, 1, '2026-03-23 12:32:51'),
(6, 'Эко-волонтерская акция', 'Субботник в парке и лекция о раздельном сборе отходов. Участники получают экосумки.', '2026-04-12 11:00:00', 'Москва, Парк Горького', 'Социальное проектирование', '1.10', 20, 'Экосумка и мерч', 3, 1, '2026-03-23 12:32:51'),
(7, 'Медиафорум \"Новые горизонты\"', 'Конференция для молодых журналистов и блогеров. Мастер-классы от ведущих СМИ.', '2026-04-18 10:00:00', 'Онлайн (YouTube трансляция)', 'Медиа', '1.40', 35, 'Стажировка в крупном СМИ', 2, 1, '2026-03-23 12:32:51'),
(8, 'Конкурс видеоблогов', 'Создание видеороликов на тему \"Молодежь и будущее\". Приз зрительских симпатий.', '2026-04-25 23:59:00', 'Онлайн', 'Медиа', '1.60', 30, 'Профессиональная камера и подписка на видеоредактор', 2, 1, '2026-03-23 12:32:51'),
(9, 'Мастер-класс по SMM', 'Практическое занятие по продвижению в социальных сетях. Таргет, контент-план, аналитика.', '2026-04-08 16:00:00', 'Онлайн (Telegram)', 'Медиа', '1.20', 20, 'Годовая подписка на сервис автопостинга', 2, 1, '2026-03-23 12:32:51'),
(10, 'Нетворкинг-вечеринка', 'Встреча участников молодежного парламента для обмена опытом и поиска единомышленников.', '2026-04-22 19:00:00', 'Москва, Бар \"Бункер\"', 'Другое', '1.00', 15, 'Фуршет и приглашения на закрытые мероприятия', 4, 1, '2026-03-23 12:32:51'),
(11, 'Квест \"Городские легенды\"', 'Командная игра по историческому центру города с поиском артефактов.', '2026-04-14 12:00:00', 'Санкт-Петербург, Невский проспект', 'Другое', '1.20', 25, 'Билеты в музей и сувениры', 4, 1, '2026-03-23 12:32:51'),
(12, 'Спортивный турнир', 'Мини-футбол и волейбол между командами участников. Спортивный праздник.', '2026-04-28 10:00:00', 'Москва, Стадион \"Лужники\"', 'Другое', '1.10', 20, 'Спортивный инвентарь и мерч', 4, 1, '2026-03-23 12:32:51'),
(13, 'еще', 'еще', '2026-03-12 19:40:00', 'еще', 'IT', '1.00', 10, 'еще', 1, 1, '2026-03-23 12:40:35');

-- --------------------------------------------------------

--
-- Структура таблицы `event_participants`
--

CREATE TABLE `event_participants` (
  `id` int NOT NULL,
  `event_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `points_earned` int DEFAULT '0',
  `qr_code` varchar(255) DEFAULT NULL,
  `attended_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `event_participants`
--

INSERT INTO `event_participants` (`id`, `event_id`, `user_id`, `status`, `points_earned`, `qr_code`, `attended_at`) VALUES
(1, 1, 5, 'confirmed', 90, NULL, '2026-04-15 10:00:00'),
(2, 2, 5, 'confirmed', 24, NULL, '2026-04-05 14:00:00'),
(3, 4, 5, 'confirmed', 60, NULL, '2026-04-10 09:00:00'),
(4, 6, 5, 'confirmed', 22, NULL, '2026-04-12 11:00:00'),
(5, 7, 5, 'confirmed', 49, NULL, '2026-04-18 10:00:00'),
(6, 9, 5, 'confirmed', 24, NULL, '2026-04-08 16:00:00'),
(7, 11, 5, 'confirmed', 15, NULL, '2026-04-14 12:00:00'),
(8, 1, 6, 'confirmed', 90, NULL, '2026-04-15 10:00:00'),
(9, 2, 6, 'confirmed', 24, NULL, '2026-04-05 14:00:00'),
(10, 3, 6, 'confirmed', 15, NULL, '2026-04-20 18:00:00'),
(11, 4, 6, 'confirmed', 60, NULL, '2026-04-10 09:00:00'),
(12, 8, 6, 'confirmed', 48, NULL, '2026-04-25 23:59:00'),
(13, 10, 6, 'confirmed', 15, NULL, '2026-04-22 19:00:00'),
(14, 1, 7, 'confirmed', 90, NULL, '2026-04-15 10:00:00'),
(15, 2, 7, 'confirmed', 24, NULL, '2026-04-05 14:00:00'),
(16, 3, 7, 'confirmed', 15, NULL, '2026-04-20 18:00:00'),
(17, 4, 7, 'confirmed', 60, NULL, '2026-04-10 09:00:00'),
(18, 5, 7, 'confirmed', 33, NULL, '2026-04-07 15:00:00'),
(19, 7, 7, 'confirmed', 49, NULL, '2026-04-18 10:00:00'),
(20, 8, 7, 'confirmed', 48, NULL, '2026-04-25 23:59:00'),
(21, 9, 7, 'confirmed', 24, NULL, '2026-04-08 16:00:00'),
(22, 10, 7, 'confirmed', 15, NULL, '2026-04-22 19:00:00'),
(23, 11, 7, 'confirmed', 30, NULL, '2026-04-14 12:00:00'),
(24, 12, 7, 'confirmed', 22, NULL, '2026-04-28 10:00:00'),
(25, 1, 8, 'confirmed', 90, NULL, '2026-04-15 10:00:00'),
(26, 2, 8, 'confirmed', 24, NULL, '2026-04-05 14:00:00'),
(27, 4, 8, 'confirmed', 60, NULL, '2026-04-10 09:00:00'),
(28, 6, 8, 'confirmed', 22, NULL, '2026-04-12 11:00:00'),
(29, 10, 8, 'confirmed', 15, NULL, '2026-04-22 19:00:00'),
(30, 12, 8, 'confirmed', 22, NULL, '2026-04-28 10:00:00'),
(31, 2, 9, 'confirmed', 24, NULL, '2026-04-05 14:00:00'),
(32, 4, 9, 'confirmed', 60, NULL, '2026-04-10 09:00:00'),
(33, 5, 9, 'confirmed', 33, NULL, '2026-04-07 15:00:00'),
(34, 6, 9, 'confirmed', 22, NULL, '2026-04-12 11:00:00'),
(35, 7, 9, 'confirmed', 49, NULL, '2026-04-18 10:00:00'),
(36, 8, 9, 'confirmed', 48, NULL, '2026-04-25 23:59:00'),
(37, 9, 9, 'confirmed', 24, NULL, '2026-04-08 16:00:00'),
(38, 10, 9, 'confirmed', 15, NULL, '2026-04-22 19:00:00'),
(39, 11, 9, 'confirmed', 30, NULL, '2026-04-14 12:00:00'),
(40, 1, 10, 'confirmed', 90, NULL, '2026-04-15 10:00:00'),
(41, 2, 10, 'confirmed', 24, NULL, '2026-04-05 14:00:00'),
(42, 3, 10, 'confirmed', 15, NULL, '2026-04-20 18:00:00'),
(43, 10, 10, 'confirmed', 15, NULL, '2026-04-22 19:00:00'),
(44, 12, 10, 'confirmed', 22, NULL, '2026-04-28 10:00:00'),
(45, 2, 1, 'pending', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `organizer_reviews`
--

CREATE TABLE `organizer_reviews` (
  `id` int NOT NULL,
  `organizer_id` int DEFAULT NULL,
  `reviewer_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Дамп данных таблицы `organizer_reviews`
--

INSERT INTO `organizer_reviews` (`id`, `organizer_id`, `reviewer_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 5, 5, 'Отличная организация хакатона! Все четко по расписанию, отличные призы.', '2026-03-23 12:32:51'),
(2, 1, 6, 4, 'Хорошее мероприятие, но хотелось бы больше времени на задания.', '2026-03-23 12:32:51'),
(3, 1, 7, 5, 'Супер! Очень крутые спикеры и полезные воркшопы.', '2026-03-23 12:32:51'),
(4, 2, 5, 5, 'Медиафорум прошел на высшем уровне! Пригласили крутых экспертов.', '2026-03-23 12:32:51'),
(5, 2, 7, 5, 'Отличная организация, все технические моменты продуманы.', '2026-03-23 12:32:51'),
(6, 2, 9, 4, 'Хорошо, но немного затянули начало.', '2026-03-23 12:32:51'),
(7, 3, 5, 5, 'Лучший форум в этом году! Очень полезно для социальных проектов.', '2026-03-23 12:32:51'),
(8, 3, 8, 5, 'Отличная школа проектирования, много практики.', '2026-03-23 12:32:51'),
(9, 3, 9, 4, 'Интересно, но хотелось бы больше живого общения.', '2026-03-23 12:32:51'),
(10, 4, 6, 5, 'Квест был супер! Очень креативно.', '2026-03-23 12:32:51'),
(11, 4, 7, 4, 'Хорошо, но можно было добавить больше локаций.', '2026-03-23 12:32:51'),
(12, 4, 10, 5, 'Отличная атмосфера на нетворкинге!', '2026-03-23 12:32:51');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('participant','organizer','admin','hr') DEFAULT 'participant',
  `city` varchar(100) DEFAULT NULL,
  `age` int DEFAULT NULL,
  `education_org` varchar(200) DEFAULT NULL,
  `team_name` varchar(100) DEFAULT NULL,
  `total_points` int DEFAULT '0',
  `trust_rating` decimal(3,2) DEFAULT '5.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `city`, `age`, `education_org`, `team_name`, `total_points`, `trust_rating`, `created_at`, `is_active`) VALUES
(1, 'admin', 'admin@example.com', 'password123', 'Алексей Иванов', 'admin', 'Москва', 32, 'МИФИ', 'Администрация', 0, '5.00', '2026-03-23 12:32:51', 1),
(2, 'organizer1', 'org1@example.com', 'password123', 'Иван Петров', 'organizer', 'Москва', 28, 'МГУ', 'IT-Команда', 150, '4.85', '2026-03-23 12:32:51', 1),
(3, 'organizer2', 'org2@example.com', 'password123', 'Елена Смирнова', 'organizer', 'Санкт-Петербург', 26, 'СПбГУ', 'МедиаЦентр', 85, '4.92', '2026-03-23 12:32:51', 1),
(4, 'organizer3', 'org3@example.com', 'password123', 'Дмитрий Козлов', 'organizer', 'Казань', 29, 'КФУ', 'Социальные проекты', 0, '4.75', '2026-03-23 12:32:51', 1),
(5, 'participant1', 'part1@example.com', 'password123', 'Анна Сидорова', 'participant', 'Санкт-Петербург', 22, 'СПбГПУ', 'Кодеры', 420, '5.00', '2026-03-23 12:32:51', 1),
(6, 'participant2', 'part2@example.com', 'password123', 'Максим Волков', 'participant', 'Москва', 23, 'МГТУ им. Баумана', 'Технолидеры', 380, '5.00', '2026-03-23 12:32:51', 1),
(7, 'participant3', 'part3@example.com', 'password123', 'Екатерина Морозова', 'participant', 'Новосибирск', 21, 'НГУ', 'DigitalStars', 520, '5.00', '2026-03-23 12:32:51', 1),
(8, 'participant4', 'part4@example.com', 'password123', 'Артем Соколов', 'participant', 'Екатеринбург', 24, 'УрФУ', 'IT-Лидеры', 290, '5.00', '2026-03-23 12:32:51', 1),
(9, 'participant5', 'part5@example.com', 'password123', 'Ольга Новикова', 'participant', 'Казань', 22, 'КФУ', 'Социальный комитет', 410, '5.00', '2026-03-23 12:32:51', 1),
(10, 'participant6', 'part6@example.com', 'password123', 'Денис Павлов', 'participant', 'Москва', 25, 'РАНХиГС', 'МедиаГруппа', 175, '5.00', '2026-03-23 12:32:51', 1),
(11, 'hr_user', 'hr@example.com', 'password123', 'Светлана Андреева', 'hr', 'Москва', 35, 'Кадровое агентство', '', 0, '5.00', '2026-03-23 12:32:51', 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organizer_id` (`organizer_id`);

--
-- Индексы таблицы `event_participants`
--
ALTER TABLE `event_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_participation` (`event_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `organizer_reviews`
--
ALTER TABLE `organizer_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organizer_id` (`organizer_id`),
  ADD KEY `reviewer_id` (`reviewer_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `achievements`
--
ALTER TABLE `achievements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `events`
--
ALTER TABLE `events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `event_participants`
--
ALTER TABLE `event_participants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT для таблицы `organizer_reviews`
--
ALTER TABLE `organizer_reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `achievements`
--
ALTER TABLE `achievements`
  ADD CONSTRAINT `achievements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `event_participants`
--
ALTER TABLE `event_participants`
  ADD CONSTRAINT `event_participants_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `organizer_reviews`
--
ALTER TABLE `organizer_reviews`
  ADD CONSTRAINT `organizer_reviews_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `organizer_reviews_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
