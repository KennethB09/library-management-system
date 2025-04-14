-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2025 at 09:11 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(64) NOT NULL,
  `firstName` varchar(250) NOT NULL,
  `lastName` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `firstName`, `lastName`, `email`, `password`) VALUES
(1, 'kenneth', 'bacaltos', 'kennethbacaltos091@gmail.com', 'admin12345');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(64) NOT NULL,
  `title` varchar(250) NOT NULL,
  `type` varchar(250) NOT NULL,
  `genre` varchar(250) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `author` varchar(250) NOT NULL,
  `format` varchar(20) NOT NULL DEFAULT 'physical'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `type`, `genre`, `description`, `author`, `format`) VALUES
(16, 'Titan Academy', 'non-academic', 'fantasy', 'In the world of magic and abilities there\'s an academy that train the children\'s of the elites and powerful people but the poor and lacking stays at the bottom of society. But in a certain circumstances a young girl that came from a poor family get in to the academy that she hate and that\'s the Titan Academy that where the powerful and influential people study.', 'alexa', 'physical'),
(17, 'Temptation Island', 'non-academic', 'romance', 'Relationship advice of rexter pacamo that he experience thru his life.', 'Rexter Pacamo', 'physical'),
(19, 'The Lord of the Rings', 'non-academic', 'fantasy', 'In the Second Age, the Dark Lord Sauron wanted to rule Middle-earth. He disguised himself as Annatar, the \"Lord of Gifts\", and pretended to be good. As Annatar he told the elves how to make magical rings which give power to their wearers. Sauron and the elves together made sixteen rings. The Elves also made three rings by themselves, called Vilya, Nenya and Narya. These nineteen rings were the Rings of Power. But Sauron secretly forged a Great Ring of his own, the One Ring. In this Ring Sauron put half of his power. He planned to control the wearers of the other rings with this One Ring. But the Elves finally realized that Annatar really was the evil Sauron and hid the Rings of Power.', 'J. R. R. Tolkien', 'physical'),
(20, 'Dune', 'non-academic', 'sci-fi', 'Set in a distant future, this epic tale explores politics, religion, and ecology on the desert planet of Arrakis, where the valuable spice melange drives the universe\'s power struggles.', 'Frank Herbert', 'physical'),
(21, 'The Left Hand of Darkness', 'non-academic', 'sci-fi', 'On the planet Gethen, where inhabitants can change sex, a human envoy learns about the complexities of culture and gender in a society unlike any other.', 'Ursula K. Le Guin', 'physical'),
(22, 'Neuromancer', 'non-academic', 'sci-fi', 'A cyberpunk classic that explores artificial intelligence, hacking, and virtual reality as a washed-up computer hacker is recruited for one last job.', 'William Gibson', 'physical'),
(23, 'The Three-Body Problem', 'non-academic', 'sci-fi', 'A physicist discovers a hidden alien civilization while investigating the mysterious death of a colleague, leading to an epic intergalactic conflict.', 'Liu Cixin', 'physical'),
(24, 'Snow Crash', 'non-academic', 'sci-fi', 'In a near-future world where virtual reality and real life intersect, a computer hacker and a samurai warrior team up to prevent a dangerous new computer virus from spreading.', 'Neal Stephenson', 'physical'),
(25, 'The Name of the Wind', 'non-academic', 'fantasy', 'The first book in The Kingkiller Chronicle follows the life of Kvothe, a gifted musician and magician who grows up to become a legendary figure.', 'Patrick Rothfuss', 'physical'),
(26, 'Mistborn: The Final Empire', 'non-academic', 'fantasy', 'In a world where ash falls from the sky, a young woman discovers she has powerful abilities and leads a group of rebels against an oppressive empire.', 'Brandon Sanderson', 'physical'),
(27, 'The Lies of Locke Lamora', 'non-academic', 'fantasy', 'Set in the city of Camorr, a place full of thieves and intrigue, this book follows Locke Lamora, a master thief, as he navigates dangerous heists and schemes.', 'Scott Lynch', 'physical'),
(28, 'A Court of Thorns and Roses', 'non-academic', 'fantasy', 'A retelling of Beauty and the Beast, this series combines romance, adventure, and magic as Feyre, a mortal girl, gets caught up in the politics of the faerie realm.', 'Sarah J. Maas', 'physical'),
(29, 'The Priory of the Orange Tree', 'non-academic', 'fantasy', 'A sprawling epic where dragons, magic, and strong female characters come together in a fight for the survival of the world.', 'Samantha Shannon', 'physical'),
(30, 'The Shining', 'non-academic', 'horror', 'A psychological horror novel about a man who takes a job as a winter caretaker in a remote hotel, only to slowly lose his sanity under the hotel\'s malevolent influence.', 'Stephen King', 'physical'),
(31, 'The Exorcist', 'non-academic', 'horror', 'The terrifying tale of a young girl possessed by a demon, and the battle between good and evil as a priest attempts to save her.', 'William Peter Blatty', 'physical'),
(32, 'Pride and Prejudice', 'non-academic', 'romance', 'A classic romance where Elizabeth Bennet navigates love and social expectations with the aloof yet charming Mr. Darcy.', 'Jane Austen', 'physical'),
(33, 'To Kill a Mockingbird', 'non-academic', 'history', 'A deeply moving novel about racial injustice in the American South, told through the perspective of young Scout Finch, as her father defends an innocent black man accused of raping a white woman.', 'Harper Lee', 'physical'),
(34, 'The Structure of Scientific Revolutions', 'academic', 'science', 'A groundbreaking work in the philosophy of science, Kuhn\'s book argues that scientific progress is not gradual but instead occurs through paradigm shifts. It challenges the traditional view of science as a steady accumulation of knowledge and explores how scientific communities react to changes in their understanding of the world.', 'Thomas S. Kuhn', 'physical'),
(35, 'Guns, Germs, and Steel: The Fates of Human Societies', 'academic', 'history', 'This influential book examines the factors that have shaped human history, particularly the development of civilizations. Diamond explores the roles of geography, biology, and environmental factors in determining the success and failure of societies, offering a wide-ranging analysis of human history.', 'Jared Diamond', 'physical'),
(40, 'Beginning Programming for Dummies', 'academic', 'science', 'About Programming', 'alexa', 'digital'),
(41, 'dtyty', 'academic', 'fantasy', 'fyf', 'hfhfh', 'digital');

-- --------------------------------------------------------

--
-- Table structure for table `books_copy`
--

CREATE TABLE `books_copy` (
  `id` int(64) NOT NULL,
  `bookRef` int(64) NOT NULL,
  `status` varchar(250) NOT NULL,
  `format` varchar(20) NOT NULL DEFAULT 'physical'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books_copy`
--

INSERT INTO `books_copy` (`id`, `bookRef`, `status`, `format`) VALUES
(76, 16, 'borrowed', 'physical'),
(77, 16, 'available', 'physical'),
(78, 16, 'available', 'physical'),
(79, 16, 'available', 'physical'),
(199, 17, 'available', 'physical'),
(200, 17, 'available', 'physical'),
(201, 17, 'available', 'physical'),
(202, 17, 'available', 'physical'),
(203, 17, 'available', 'physical'),
(204, 17, 'available', 'physical'),
(205, 17, 'available', 'physical'),
(206, 17, 'available', 'physical'),
(207, 17, 'available', 'physical'),
(208, 17, 'available', 'physical'),
(219, 19, 'available', 'physical'),
(220, 19, 'available', 'physical'),
(221, 19, 'available', 'physical'),
(222, 19, 'available', 'physical'),
(223, 19, 'available', 'physical'),
(224, 19, 'available', 'physical'),
(225, 19, 'available', 'physical'),
(226, 19, 'available', 'physical'),
(227, 19, 'available', 'physical'),
(228, 19, 'available', 'physical'),
(229, 20, 'available', 'physical'),
(230, 20, 'available', 'physical'),
(231, 20, 'available', 'physical'),
(232, 20, 'available', 'physical'),
(233, 20, 'available', 'physical'),
(234, 21, 'available', 'physical'),
(235, 21, 'available', 'physical'),
(236, 22, 'available', 'physical'),
(237, 22, 'available', 'physical'),
(238, 22, 'available', 'physical'),
(239, 22, 'available', 'physical'),
(240, 22, 'available', 'physical'),
(241, 22, 'available', 'physical'),
(242, 22, 'available', 'physical'),
(243, 23, 'available', 'physical'),
(244, 23, 'available', 'physical'),
(245, 23, 'available', 'physical'),
(246, 23, 'available', 'physical'),
(247, 24, 'available', 'physical'),
(248, 24, 'available', 'physical'),
(249, 24, 'available', 'physical'),
(250, 24, 'available', 'physical'),
(251, 24, 'available', 'physical'),
(252, 24, 'available', 'physical'),
(253, 24, 'available', 'physical'),
(254, 24, 'available', 'physical'),
(255, 24, 'available', 'physical'),
(256, 24, 'available', 'physical'),
(257, 25, 'available', 'physical'),
(258, 25, 'available', 'physical'),
(259, 25, 'available', 'physical'),
(260, 25, 'available', 'physical'),
(261, 25, 'available', 'physical'),
(262, 25, 'available', 'physical'),
(263, 25, 'available', 'physical'),
(264, 25, 'available', 'physical'),
(265, 25, 'available', 'physical'),
(266, 25, 'available', 'physical'),
(267, 25, 'available', 'physical'),
(268, 26, 'available', 'physical'),
(269, 26, 'available', 'physical'),
(270, 26, 'available', 'physical'),
(271, 26, 'available', 'physical'),
(272, 26, 'available', 'physical'),
(273, 26, 'available', 'physical'),
(274, 26, 'available', 'physical'),
(275, 26, 'available', 'physical'),
(276, 26, 'available', 'physical'),
(277, 26, 'available', 'physical'),
(278, 26, 'available', 'physical'),
(279, 27, 'available', 'physical'),
(280, 27, 'available', 'physical'),
(281, 27, 'available', 'physical'),
(282, 27, 'available', 'physical'),
(283, 28, 'available', 'physical'),
(284, 28, 'available', 'physical'),
(285, 28, 'available', 'physical'),
(286, 29, 'available', 'physical'),
(287, 29, 'available', 'physical'),
(288, 29, 'available', 'physical'),
(289, 29, 'available', 'physical'),
(290, 29, 'available', 'physical'),
(291, 29, 'available', 'physical'),
(292, 29, 'available', 'physical'),
(293, 29, 'available', 'physical'),
(294, 30, 'available', 'physical'),
(295, 30, 'available', 'physical'),
(296, 30, 'available', 'physical'),
(297, 30, 'available', 'physical'),
(298, 30, 'available', 'physical'),
(299, 31, 'available', 'physical'),
(300, 31, 'available', 'physical'),
(301, 31, 'available', 'physical'),
(302, 31, 'available', 'physical'),
(303, 32, 'available', 'physical'),
(304, 32, 'available', 'physical'),
(305, 32, 'available', 'physical'),
(306, 33, 'available', 'physical'),
(307, 33, 'available', 'physical'),
(308, 33, 'available', 'physical'),
(309, 34, 'available', 'physical'),
(310, 34, 'available', 'physical'),
(311, 34, 'available', 'physical'),
(312, 34, 'available', 'physical'),
(313, 34, 'available', 'physical'),
(314, 34, 'available', 'physical'),
(315, 34, 'available', 'physical'),
(316, 34, 'available', 'physical'),
(317, 34, 'available', 'physical'),
(318, 34, 'available', 'physical'),
(319, 34, 'available', 'physical'),
(320, 34, 'available', 'physical'),
(321, 34, 'available', 'physical'),
(322, 34, 'available', 'physical'),
(323, 34, 'available', 'physical'),
(324, 34, 'available', 'physical'),
(325, 34, 'available', 'physical'),
(326, 34, 'available', 'physical'),
(327, 34, 'available', 'physical'),
(328, 34, 'available', 'physical'),
(329, 35, 'available', 'physical'),
(330, 35, 'available', 'physical'),
(331, 35, 'available', 'physical'),
(332, 35, 'available', 'physical'),
(333, 35, 'available', 'physical'),
(334, 35, 'available', 'physical'),
(335, 35, 'available', 'physical'),
(336, 35, 'available', 'physical'),
(337, 35, 'available', 'physical'),
(338, 35, 'available', 'physical'),
(339, 35, 'available', 'physical'),
(340, 35, 'available', 'physical'),
(341, 35, 'available', 'physical'),
(342, 35, 'available', 'physical'),
(343, 35, 'available', 'physical'),
(344, 35, 'available', 'physical'),
(345, 35, 'available', 'physical'),
(346, 35, 'available', 'physical'),
(347, 35, 'available', 'physical'),
(348, 35, 'available', 'physical'),
(349, 35, 'available', 'physical'),
(350, 35, 'available', 'physical'),
(351, 35, 'available', 'physical'),
(352, 0, 'available', 'physical'),
(353, 16, 'available', 'physical'),
(354, 40, 'available', 'digital'),
(355, 41, 'available', 'digital');

-- --------------------------------------------------------

--
-- Table structure for table `borrowed_books`
--

CREATE TABLE `borrowed_books` (
  `id` int(64) NOT NULL,
  `bookRef` int(64) NOT NULL,
  `borrowedOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `dueDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `borrower` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowed_books`
--

INSERT INTO `borrowed_books` (`id`, `bookRef`, `borrowedOn`, `dueDate`, `borrower`) VALUES
(24, 76, '2025-04-12 16:00:00', '2025-04-14 16:00:00', '2301010066');

-- --------------------------------------------------------

--
-- Table structure for table `request_books`
--

CREATE TABLE `request_books` (
  `id` int(64) NOT NULL,
  `bookRef` int(64) NOT NULL,
  `requestOn` timestamp NOT NULL DEFAULT current_timestamp(),
  `requesterId` varchar(10) NOT NULL,
  `dueDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` int(11) NOT NULL,
  `bookRef` int(64) NOT NULL,
  `location` varchar(250) NOT NULL,
  `fileName` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`id`, `bookRef`, `location`, `fileName`) VALUES
(3, 38, '../uploads/Beginning Programming for Dummies.pdf', 'Beginning Programming for Dummies.pdf'),
(4, 39, '../uploads/New_Horizons.pdf', 'New_Horizons.pdf'),
(5, 40, '../uploads/Beginning Programming for Dummies.pdf', 'Beginning Programming for Dummies.pdf'),
(6, 41, '../uploads/New_Horizons.pdf', 'New_Horizons.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(64) NOT NULL,
  `studentNumber` varchar(10) NOT NULL,
  `firstName` varchar(250) NOT NULL,
  `lastName` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `section` varchar(250) NOT NULL,
  `course` varchar(250) NOT NULL,
  `credential` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`credential`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `studentNumber`, `firstName`, `lastName`, `password`, `email`, `section`, `course`, `credential`) VALUES
(1, '2301010066', 'Kenneth', 'Bacaltos', '$2y$10$P7nWCTLNjg/MwF6cqeUYs.VsNJybOeSN5sRv6yE6YW62ptktyc4Qa', 'kennethbacaltos09@gmail.com', 'IT2A', 'BSIT', '{\"endpoint\":\"https://fcm.googleapis.com/fcm/send/chOrVID8DcU:APA91bHX5pwXmDoAth0Sr--LBN6Tsrr4JRRE-tg8E4ZxeF5MbE8QoXj1WAAxA945DvJ2DdP1jAenVGqf-Uf5wMkfHPD6uHKW1GLdkr4AVXmctk2TcSijgoslBGV7txB0NzDpVPkBbq96\",\"expirationTime\":null,\"keys\":{\"p256dh\":\"BDJRGVyukke_HXMYuHnLDy6ZZYCygqFtubCnOzG7IJTL4NnReVxXikwrdAXuo_VrUf8UnXEWJ5jKbeA0kj38EjI\",\"auth\":\"_qEMUCvzZn5VXOjGAUa6VQ\"}}'),
(2, '2301010077', 'Kenzo', 'Yuki', '$2y$10$Xr0/nndfuPyeJuKuLVtySO4Fo.kP/0NuoniBDgqJM7KY39rIW/TOO', 'keno@gmail.com', 'TM2B', 'BSTM', '{\"endpoint\":\"https://wns2-bl2p.notify.windows.com/w/?token=BQYAAAA8E7fOrICQLOaTICiFTSmbRDYmACEOckb1h3r9TNkeJXchmhckFgwl%2b9Vk2yL%2fJOmh2%2bl9Z4OMfZH%2fBK7mn6DZ0m6A1etg8gzkQSudkek%2bB4Qv%2bG%2bU%2bKpL8raFpX%2b3btN9HugjCGkHXP%2bltRwdD50NSYpdwYSki2lth9qad1BQB%2frbBC7twOAxHvQwXFDE8nlk48GoY3zPCCV2oCQZ301ta6FlZFvTLo5EptAR52htNfw8PWLt1OshDObqQtUgMWrKYPoaVcuJT4uMHtfH40561%2fbIadO7mzASZTggjXtDrwQFIpnDwlOO1Nn00%2ftiFX0%3d\",\"expirationTime\":null,\"keys\":{\"p256dh\":\"BG2-EFnfAZEUQhQTMZ2gh-yVLFgfRDwCz_vhuAdlxqS7hrxJ4tVQHrq1BOJtaLi1Y26d6RrQagMqcbAVmuPvGio\",\"auth\":\"wTgPRpQhbc8Uqgi4OtBCow\"}}'),
(3, '230', 'Maria Alexa', 'Canete', '$2y$10$AoeTXUE8zTKDF61i0EcKa.fTJ4gTJCuMTYnKY30OqEG3Eefp2wHTO', 'rilecanete@gmail.com', 'TM2B', 'BSTM', '');

-- --------------------------------------------------------

--
-- Table structure for table `waitlist`
--

CREATE TABLE `waitlist` (
  `id` int(11) NOT NULL,
  `userId` varchar(10) NOT NULL,
  `bookRef` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `waitlist`
--

INSERT INTO `waitlist` (`id`, `userId`, `bookRef`) VALUES
(2, '2301010066', '199'),
(3, '2301010066', '234'),
(4, '2301010066', '234'),
(5, '2301010066', '234');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books_copy`
--
ALTER TABLE `books_copy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `borrowed_books`
--
ALTER TABLE `borrowed_books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `request_books`
--
ALTER TABLE `request_books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `waitlist`
--
ALTER TABLE `waitlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `books_copy`
--
ALTER TABLE `books_copy`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=356;

--
-- AUTO_INCREMENT for table `borrowed_books`
--
ALTER TABLE `borrowed_books`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `request_books`
--
ALTER TABLE `request_books`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `waitlist`
--
ALTER TABLE `waitlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
