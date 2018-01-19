-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  ven. 19 jan. 2018 à 13:40
-- Version du serveur :  5.7.19
-- Version de PHP :  5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `osureplay`
--
CREATE DATABASE IF NOT EXISTS `osureplay` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `osureplay`;

-- --------------------------------------------------------

--
-- Structure de la table `playerlist`
--

DROP TABLE IF EXISTS `playerlist`;
CREATE TABLE IF NOT EXISTS `playerlist` (
  `userId` int(10) NOT NULL COMMENT 'osu player id',
  `userName` varchar(256) NOT NULL COMMENT 'osu username'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `playerlist`
--

INSERT INTO `playerlist` (`userId`, `userName`) VALUES
(3481725, 'codevirtuel');

-- --------------------------------------------------------

--
-- Structure de la table `replaylist`
--

DROP TABLE IF EXISTS `replaylist`;
CREATE TABLE IF NOT EXISTS `replaylist` (
  `replayId` varchar(13) NOT NULL,
  `beatmapId` int(11) NOT NULL,
  `userId` int(10) NOT NULL,
  `OFN` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `permanent` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `requestlist`
--

DROP TABLE IF EXISTS `requestlist`;
CREATE TABLE IF NOT EXISTS `requestlist` (
  `replayId` varchar(13) NOT NULL COMMENT 'id du request',
  `beatmapId` int(6) NOT NULL,
  `beatmapSetId` int(10) NOT NULL,
  `OFN` longtext NOT NULL COMMENT 'osu file name',
  `BFN` longtext NOT NULL COMMENT 'Beatmap File Name',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date de depot du replay',
  `duration` int(11) NOT NULL COMMENT 'Durée du replay',
  `priority` smallint(1) NOT NULL DEFAULT '0' COMMENT 'priorite',
  `playerId` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `requestlist`
--
ALTER TABLE `requestlist` ADD FULLTEXT KEY `OFN` (`OFN`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
