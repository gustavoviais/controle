-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 05-Ago-2016 às 20:04
-- Versão do servidor: 10.1.13-MariaDB
-- PHP Version: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `controle`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `categoria`
--

CREATE TABLE `categoria` (
  `id_cat` int(11) NOT NULL,
  `desc` varchar(200) COLLATE utf8_swedish_ci NOT NULL,
  `limite` float(10,2) NOT NULL,
  `user` int(4) NOT NULL,
  `dsy` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Extraindo dados da tabela `categoria`
--

INSERT INTO `categoria` (`id_cat`, `desc`, `limite`, `user`, `dsy`) VALUES
(1, 'Refeição', 80.00, 0, '0000-00-00 00:00:00'),
(2, 'Taxi', 50.00, 0, '0000-00-00 00:00:00'),
(3, 'Hotel', 200.00, 0, '0000-00-00 00:00:00'),
(4, 'Veículo Ferrari', 0.00, 0, '0000-00-00 00:00:00'),
(5, 'Veículo Alugado', 100.00, 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `details`
--

CREATE TABLE `details` (
  `id_details` int(11) NOT NULL,
  `data_entrada` date NOT NULL,
  `data_saida` date DEFAULT NULL,
  `valor` float(10,2) NOT NULL,
  `local` varchar(200) NOT NULL,
  `obs` text,
  `id_emp` int(11) NOT NULL,
  `id_cat` int(11) NOT NULL,
  `user` int(4) NOT NULL,
  `dsy` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `details`
--

INSERT INTO `details` (`id_details`, `data_entrada`, `data_saida`, `valor`, `local`, `obs`, `id_emp`, `id_cat`, `user`, `dsy`) VALUES
(11, '2016-08-05', '2016-08-05', 25.00, '', '', 1, 1, 2, '2016-08-05 09:49:15'),
(12, '2016-08-05', '2016-08-06', 1.00, '', '', 1, 1, 2, '2016-08-05 09:39:47'),
(13, '2016-08-05', '2016-08-05', 1.00, '', '', 1, 1, 2, '2016-08-05 09:50:15');

-- --------------------------------------------------------

--
-- Estrutura da tabela `empresa`
--

CREATE TABLE `empresa` (
  `id_emp` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `user` int(4) NOT NULL,
  `dsy` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `empresa`
--

INSERT INTO `empresa` (`id_emp`, `nome`, `user`, `dsy`) VALUES
(1, 'Ferrari Zagatto', 0, '0000-00-00 00:00:00'),
(2, 'TIQS', 0, '0000-00-00 00:00:00'),
(3, 'Agrotis', 0, '0000-00-00 00:00:00'),
(4, 'Terceiros', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `login`
--

CREATE TABLE `login` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` char(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `login`
--

INSERT INTO `login` (`id`, `username`, `password`) VALUES
(2, 'gustavo', 'bf15be717ac1b080b4f1c456692825891ff5073d');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usr` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `id_emp` int(11) NOT NULL,
  `user` int(4) NOT NULL,
  `dsy` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id_usr`, `nome`, `id_emp`, `user`, `dsy`) VALUES
(1, 'Alex', 2, 0, '0000-00-00 00:00:00'),
(2, 'Flávio', 2, 0, '0000-00-00 00:00:00'),
(3, 'Érika', 3, 0, '0000-00-00 00:00:00'),
(4, 'Gustavo', 1, 2, '2016-08-05 09:47:38');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios_details`
--

CREATE TABLE `usuarios_details` (
  `id_details` int(11) NOT NULL,
  `id_usr` int(11) NOT NULL,
  `user` int(4) NOT NULL,
  `dsy` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `usuarios_details`
--

INSERT INTO `usuarios_details` (`id_details`, `id_usr`, `user`, `dsy`) VALUES
(11, 1, 2, '2016-08-05 09:49:15'),
(12, 3, 0, '0000-00-00 00:00:00'),
(13, 2, 2, '2016-08-05 09:50:16'),
(13, 3, 2, '2016-08-05 09:50:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_cat`);

--
-- Indexes for table `details`
--
ALTER TABLE `details`
  ADD PRIMARY KEY (`id_details`),
  ADD KEY `id_emp` (`id_emp`),
  ADD KEY `id_cat` (`id_cat`);

--
-- Indexes for table `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id_emp`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usr`),
  ADD KEY `id_emp` (`id_emp`);

--
-- Indexes for table `usuarios_details`
--
ALTER TABLE `usuarios_details`
  ADD PRIMARY KEY (`id_details`,`id_usr`),
  ADD KEY `id_usr` (`id_usr`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_cat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `details`
--
ALTER TABLE `details`
  MODIFY `id_details` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id_emp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `details`
--
ALTER TABLE `details`
  ADD CONSTRAINT `details_ibfk_1` FOREIGN KEY (`id_emp`) REFERENCES `empresa` (`id_emp`),
  ADD CONSTRAINT `details_ibfk_2` FOREIGN KEY (`id_cat`) REFERENCES `categoria` (`id_cat`);

--
-- Limitadores para a tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_emp`) REFERENCES `empresa` (`id_emp`);

--
-- Limitadores para a tabela `usuarios_details`
--
ALTER TABLE `usuarios_details`
  ADD CONSTRAINT `usuarios_details_ibfk_1` FOREIGN KEY (`id_details`) REFERENCES `details` (`id_details`),
  ADD CONSTRAINT `usuarios_details_ibfk_2` FOREIGN KEY (`id_usr`) REFERENCES `usuarios` (`id_usr`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
