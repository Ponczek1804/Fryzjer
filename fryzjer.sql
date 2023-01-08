-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 08 Sty 2023, 11:41
-- Wersja serwera: 10.4.24-MariaDB
-- Wersja PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `fryzjer`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `service_id` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Zrzut danych tabeli `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `service_id`, `date`) VALUES
(11, 8, 5, '2023-01-09 17:00:00');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Zrzut danych tabeli `services`
--

INSERT INTO `services` (`id`, `name`, `price`) VALUES
(4, 'sczyrzenie menskie', '10.00'),
(5, 'szczyrzenie damskie', '15.00');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(60) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `surname` varchar(60) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `email` varchar(60) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `number` varchar(30) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `admin` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `login` varchar(25) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `password` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `name`, `surname`, `email`, `number`, `admin`, `login`, `password`) VALUES
(2, 'andrzej', 'bomba', 'andzrej@gmail.com', '+48123456789', 0, 'andrzej2137', '$2y$10$XIsh5kvALJsbMwae.CwV0uv7FADV9RBiruiyig.LfRSyJOAFLESJm'),
(3, 'karol', 'brodka', 'karol@xd.pl', '+48123456789', 0, 'karol1337', '$2y$10$FrKZtmtxxIDK.ChoAd6ljOOAsWHt0hPOU.lUw/W5g1wchDrPYBDzK'),
(4, 'Adammmm', 'Adammmmm', 'asdasdaasdsd@fsdf.com', '+48122221123', 0, 'adam1804', '$2y$10$FrKZtmtxxIDK.ChoAd6ljOOAsWHt0hPOU.lUw/W5g1wchDrPYBDzK'),
(6, 'Adam', 'Ciesielski', 'cisiel2001@gmail.com', '+48123123123', 1, 'ponczek1804', '$2y$10$3MkeKyS5vNrmE6mB6Bjy9ehiVO5ik/MzexFLHppL.K2SjhhVIBTX.'),
(7, 'Adamm', 'Ciesielskii', 'adam@1804.com', '+48123123223', 1, 'LoginAdam', '$2y$10$PcexnCyXJJEDnD00z6swEOAbbRaraiR/IbHx/Odp7Fwjs2fll19/e'),
(8, 'Nowy user', 'asdasdads', 'asdasdas@adasd.com', '+48123123123', 0, 'AdamLogin', '$2y$10$NOuqqO19xlwQs5oq4NuskuqweGCDi4z5ybbXQ.YuoRlDnzaB4SqMq'),
(9, 'nowekonto', 'kontonowe', 'asdads@asdasd.com', '+48123123123', 0, 'NoweKonto', '$2y$10$rAp7joklr.SeDLqQDBJjlevcXtLtKjCUPJzOpbGs88h1M4Uo8vWWG'),
(10, 'gdfgdfgs', 'sgsgfdgdg', 'fsdfsdf@asdfsdf.com', '+48321123321', 0, 'dfgdgdfgdf', '$2y$10$L7ic8xg9YBCrOvzu5kZ9qeoPlmz7e0LQAk9r1bP.y3Leu8Lxq4U0e');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indeksy dla tabeli `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT dla tabeli `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
