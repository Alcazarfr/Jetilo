-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mer 10 Août 2011 à 22:42
-- Version du serveur: 5.1.44
-- Version de PHP: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `jet`
--

-- --------------------------------------------------------

--
-- Structure de la table `Action`
--

CREATE TABLE `Action` (
  `ActionID` mediumint(6) NOT NULL AUTO_INCREMENT,
  `ActionType` varchar(40) NOT NULL,
  `ActionSourceType` varchar(20) NOT NULL,
  `ActionSourceID` mediumint(6) NOT NULL,
  `ActionTimeDebut` int(11) NOT NULL,
  `ActionTimeFin` int(11) NOT NULL,
  PRIMARY KEY (`ActionID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=375 ;

--
-- Contenu de la table `Action`
--

INSERT INTO `Action` VALUES(1, 'renforcer-defense', 'ETAT', 1, 1311093341, 1311093461);
INSERT INTO `Action` VALUES(2, 'renforcer-defense', 'ETAT', 1, 1311093429, 1311093549);
INSERT INTO `Action` VALUES(3, 'renforcer-defense', 'ETAT', 1, 1311093433, 1311093553);
INSERT INTO `Action` VALUES(4, 'renforcer-defense', 'ETAT', 1, 1311093434, 1311093554);
INSERT INTO `Action` VALUES(5, 'renforcer-defense', 'ETAT', 1, 1311093456, 1311093576);
INSERT INTO `Action` VALUES(6, 'renforcer-defense', 'ETAT', 1, 1311093477, 1311093597);
INSERT INTO `Action` VALUES(7, 'renforcer-defense', 'ETAT', 1, 1311093516, 1311093636);
INSERT INTO `Action` VALUES(8, 'renforcer-defense', 'ETAT', 1, 1311093535, 1311093655);
INSERT INTO `Action` VALUES(9, 'renforcer-defense', 'ETAT', 1, 1311093571, 1311093691);
INSERT INTO `Action` VALUES(10, 'renforcer-defense', 'ETAT', 1, 1311093590, 1311093710);
INSERT INTO `Action` VALUES(11, 'renforcer-defense', 'ETAT', 1, 1311093629, 1311093749);
INSERT INTO `Action` VALUES(12, 'renforcer-defense', 'ETAT', 1, 1311093630, 1311093750);
INSERT INTO `Action` VALUES(13, 'renforcer-defense', 'ETAT', 1, 1311093837, 1311093957);
INSERT INTO `Action` VALUES(14, 'renforcer-defense', 'ETAT', 1, 1311093929, 1311094049);
INSERT INTO `Action` VALUES(15, 'renforcer-defense', 'ETAT', 1, 1311093988, 1311094108);
INSERT INTO `Action` VALUES(16, 'renforcer-defense', 'ETAT', 1, 1311094016, 1311094136);
INSERT INTO `Action` VALUES(17, 'renforcer-defense', 'ETAT', 1, 1311094017, 1311094137);
INSERT INTO `Action` VALUES(18, 'renforcer-defense', 'ETAT', 1, 1311094045, 1311094165);
INSERT INTO `Action` VALUES(19, 'renforcer-defense', 'ETAT', 1, 1311094046, 1311094166);
INSERT INTO `Action` VALUES(20, 'renforcer-defense', 'ETAT', 1, 1311094131, 1311094251);
INSERT INTO `Action` VALUES(21, 'renforcer-defense', 'ETAT', 1, 1311094132, 1311094252);
INSERT INTO `Action` VALUES(22, 'renforcer-defense', 'ETAT', 1, 1311094169, 1311094289);
INSERT INTO `Action` VALUES(23, 'renforcer-defense', 'ETAT', 1, 1311094171, 1311094291);
INSERT INTO `Action` VALUES(24, 'renforcer-defense', 'ETAT', 1, 1311107747, 1311107867);
INSERT INTO `Action` VALUES(25, 'renforcer-defense', 'ETAT', 1, 1311108035, 1311108155);
INSERT INTO `Action` VALUES(26, 'renforcer-defense', 'ETAT', 1, 1311108057, 1311108177);
INSERT INTO `Action` VALUES(27, 'renforcer-defense', 'ETAT', 1, 1311108060, 1311108180);
INSERT INTO `Action` VALUES(28, 'renforcer-defense', 'ETAT', 1, 1311108061, 1311108181);
INSERT INTO `Action` VALUES(29, 'renforcer-defense', 'ETAT', 1, 1311108075, 1311108195);
INSERT INTO `Action` VALUES(30, 'renforcer-defense', 'ETAT', 1, 1311108077, 1311108197);
INSERT INTO `Action` VALUES(31, 'renforcer-defense', 'ETAT', 1, 1311180514, 1311180634);
INSERT INTO `Action` VALUES(32, 'renforcer-defense', 'ETAT', 1, 1311180517, 1311180637);
INSERT INTO `Action` VALUES(33, 'arreter-croissance-population', 'ETAT', 1, 1311180464, 1311181064);
INSERT INTO `Action` VALUES(34, 'arreter-croissance-population', 'ETAT', 1, 1311180923, 1311181523);
INSERT INTO `Action` VALUES(35, 'arreter-croissance-population', 'ETAT', 1, 1311181359, 1311181959);
INSERT INTO `Action` VALUES(36, 'arreter-croissance-population', 'ETAT', 1, 1311181365, 1311181965);
INSERT INTO `Action` VALUES(37, 'renforcer-defense', 'ETAT', 1, 1311181452, 1311181572);
INSERT INTO `Action` VALUES(38, 'renforcer-defense', 'ETAT', 1, 1311181492, 1311181612);
INSERT INTO `Action` VALUES(39, 'renforcer-defense', 'ETAT', 1, 1311181497, 1311181617);
INSERT INTO `Action` VALUES(40, 'renforcer-defense', 'ETAT', 1, 1311181518, 1311181638);
INSERT INTO `Action` VALUES(41, 'renforcer-defense', 'ETAT', 1, 1311181560, 1311181680);
INSERT INTO `Action` VALUES(42, 'renforcer-defense', 'ETAT', 1, 1311181561, 1311181681);
INSERT INTO `Action` VALUES(43, 'renforcer-defense', 'ETAT', 1, 1311181565, 1311181685);
INSERT INTO `Action` VALUES(44, 'arreter-croissance-population', 'ETAT', 1, 1311181507, 1311182107);
INSERT INTO `Action` VALUES(45, 'augmenter-croissance-population', 'ETAT', 1, 1311182498, 1311183098);
INSERT INTO `Action` VALUES(46, 'augmenter-croissance-population', 'ETAT', 1, 1311182518, 1311183118);
INSERT INTO `Action` VALUES(47, 'augmenter-croissance-population', 'ETAT', 1, 1311182520, 1311183120);
INSERT INTO `Action` VALUES(48, 'reduire-croissance-population', 'ETAT', 1, 1311182622, 1311183222);
INSERT INTO `Action` VALUES(49, 'reduire-croissance-population', 'ETAT', 1, 1311182624, 1311183224);
INSERT INTO `Action` VALUES(50, 'renforcer-defense', 'ETAT', 1, 1311182733, 1311182853);
INSERT INTO `Action` VALUES(51, 'renforcer-defense', 'ETAT', 1, 1311182744, 1311182864);
INSERT INTO `Action` VALUES(52, 'affaiblir-defense', 'ETAT', 1, 1311182822, 1311183122);
INSERT INTO `Action` VALUES(53, 'affaiblir-defense', 'ETAT', 1, 1311182824, 1311183124);
INSERT INTO `Action` VALUES(54, 'augmenter-croissance-population', 'ETAT', 1, 1311182864, 1311183464);
INSERT INTO `Action` VALUES(55, 'augmenter-croissance-population', 'ETAT', 1, 1311182865, 1311183465);
INSERT INTO `Action` VALUES(56, 'augmenter-croissance-population', 'ETAT', 1, 1311182866, 1311183466);
INSERT INTO `Action` VALUES(57, 'reduire-croissance-population', 'ETAT', 1, 1311182878, 1311183478);
INSERT INTO `Action` VALUES(58, 'reduire-croissance-population', 'ETAT', 1, 1311182879, 1311183479);
INSERT INTO `Action` VALUES(59, 'reduire-croissance-population', 'ETAT', 1, 1311182880, 1311183480);
INSERT INTO `Action` VALUES(60, 'renforcer-defense', 'ETAT', 1, 1311183000, 1311183120);
INSERT INTO `Action` VALUES(61, 'creer-armee', 'ETAT', 1, 1311269872, 1311269872);
INSERT INTO `Action` VALUES(62, 'creer-armee', 'ETAT', 1, 1311269877, 1311269877);
INSERT INTO `Action` VALUES(63, 'creer-armee', 'ETAT', 1, 1311269925, 1311269925);
INSERT INTO `Action` VALUES(64, 'creer-armee', 'ETAT', 1, 1311269928, 1311269928);
INSERT INTO `Action` VALUES(65, 'creer-armee', 'ETAT', 1, 1311270023, 1311270023);
INSERT INTO `Action` VALUES(66, 'creer-armee', 'ETAT', 1, 1311270120, 1311270120);
INSERT INTO `Action` VALUES(67, 'creer-armee', 'ETAT', 1, 1311271113, 1311271113);
INSERT INTO `Action` VALUES(68, 'creer-armee', 'ETAT', 1, 1311271166, 1311271166);
INSERT INTO `Action` VALUES(69, 'creer-armee', 'ETAT', 1, 1311271207, 1311271207);
INSERT INTO `Action` VALUES(70, 'creer-armee', 'ETAT', 1, 1311271259, 1311271259);
INSERT INTO `Action` VALUES(71, 'creer-armee', 'ETAT', 1, 1311271342, 1311271342);
INSERT INTO `Action` VALUES(72, 'creer-armee', 'ETAT', 1, 1311271371, 1311271371);
INSERT INTO `Action` VALUES(73, 'creer-armee', 'ETAT', 1, 1311271429, 1311271429);
INSERT INTO `Action` VALUES(74, 'creer-armee', 'ETAT', 1, 1311271430, 1311271430);
INSERT INTO `Action` VALUES(75, 'creer-armee', 'ETAT', 1, 1311271698, 1311271698);
INSERT INTO `Action` VALUES(76, 'creer-armee', 'ETAT', 1, 1311271799, 1311271799);
INSERT INTO `Action` VALUES(77, 'creer-armee', 'ETAT', 1, 1311272063, 1311272063);
INSERT INTO `Action` VALUES(78, 'creer-armee', 'ETAT', 1, 1311272298, 1311272298);
INSERT INTO `Action` VALUES(79, 'creer-armee', 'ETAT', 1, 1311272361, 1311272361);
INSERT INTO `Action` VALUES(80, 'creer-armee', 'ETAT', 1, 1311272377, 1311272377);
INSERT INTO `Action` VALUES(81, 'creer-armee', 'ETAT', 1, 1311272470, 1311272470);
INSERT INTO `Action` VALUES(82, 'creer-armee', 'ETAT', 1, 1311272492, 1311272492);
INSERT INTO `Action` VALUES(83, 'creer-armee', 'ETAT', 1, 1311272783, 1311272783);
INSERT INTO `Action` VALUES(84, 'creer-armee', 'ETAT', 1, 1311272866, 1311272866);
INSERT INTO `Action` VALUES(85, 'creer-armee', 'ETAT', 1, 1311272913, 1311272913);
INSERT INTO `Action` VALUES(86, 'creer-armee', 'ETAT', 1, 1311281239, 1311281239);
INSERT INTO `Action` VALUES(87, 'creer-armee', 'ETAT', 1, 1311281252, 1311281252);
INSERT INTO `Action` VALUES(88, 'creer-armee', 'ETAT', 1, 1311281271, 1311281271);
INSERT INTO `Action` VALUES(89, 'renforcer-defense', 'ETAT', 1, 1311352990, 1311352990);
INSERT INTO `Action` VALUES(90, 'renforcer-defense', 'ETAT', 1, 1311353568, 1311353568);
INSERT INTO `Action` VALUES(91, 'creer-armee', 'ETAT', 1, 1311353544, 1311353544);
INSERT INTO `Action` VALUES(92, 'arreter-croissance-population', 'ETAT', 1, 1311354508, 1311355108);
INSERT INTO `Action` VALUES(93, 'reduire-croissance-population', 'ETAT', 1, 1311354599, 1311355199);
INSERT INTO `Action` VALUES(94, 'renforcer-defense', 'ETAT', 1, 1311354655, 1311354655);
INSERT INTO `Action` VALUES(95, 'renforcer-defense', 'ETAT', 1, 1311354657, 1311354657);
INSERT INTO `Action` VALUES(96, 'renforcer-defense', 'ETAT', 1, 1311354678, 1311354678);
INSERT INTO `Action` VALUES(97, 'renforcer-defense', 'ETAT', 1, 1311354724, 1311354724);
INSERT INTO `Action` VALUES(98, 'renforcer-defense', 'ETAT', 1, 1311354756, 1311354756);
INSERT INTO `Action` VALUES(99, 'renforcer-defense', 'ETAT', 1, 1311354829, 1311354829);
INSERT INTO `Action` VALUES(100, 'renforcer-defense', 'ETAT', 1, 1311354886, 1311354886);
INSERT INTO `Action` VALUES(101, 'renforcer-defense', 'ETAT', 1, 1311354967, 1311354967);
INSERT INTO `Action` VALUES(102, 'renforcer-defense', 'ETAT', 1, 1311354975, 1311354975);
INSERT INTO `Action` VALUES(103, 'arreter-croissance-population', 'ETAT', 1, 1311355023, 1311355623);
INSERT INTO `Action` VALUES(104, 'arreter-croissance-population', 'ETAT', 1, 1311355025, 1311355625);
INSERT INTO `Action` VALUES(105, 'renforcer-defense', 'ETAT', 1, 1311355103, 1311355103);
INSERT INTO `Action` VALUES(106, 'renforcer-defense', 'ETAT', 1, 1311355132, 1311355132);
INSERT INTO `Action` VALUES(107, 'augmenter-croissance-population', 'ETAT', 1, 1311355192, 1311355792);
INSERT INTO `Action` VALUES(108, 'renforcer-defense', 'ETAT', 1, 1311355233, 1311355233);
INSERT INTO `Action` VALUES(109, 'creer-armee', 'ETAT', 1, 1311355267, 1311355267);
INSERT INTO `Action` VALUES(110, 'affaiblir-defense', 'ETAT', 1, 1311355303, 1311355603);
INSERT INTO `Action` VALUES(111, 'augmenter-croissance-population', 'ETAT', 1, 1311355314, 1311355914);
INSERT INTO `Action` VALUES(112, 'creer-armee', 'ETAT', 1, 1311355311, 1311355311);
INSERT INTO `Action` VALUES(113, 'arreter-croissance-population', 'ETAT', 1, 1311355385, 1311355985);
INSERT INTO `Action` VALUES(114, 'arreter-croissance-population', 'ETAT', 1, 1311355431, 1311356031);
INSERT INTO `Action` VALUES(115, 'arreter-croissance-population', 'ETAT', 1, 1311355541, 1311356141);
INSERT INTO `Action` VALUES(116, 'arreter-croissance-population', 'ETAT', 1, 1311355561, 1311356161);
INSERT INTO `Action` VALUES(117, 'arreter-croissance-population', 'ETAT', 1, 1311355576, 1311356176);
INSERT INTO `Action` VALUES(118, 'arreter-croissance-population', 'ETAT', 1, 1311355615, 1311356215);
INSERT INTO `Action` VALUES(119, 'renforcer-defense', 'ETAT', 1, 1311355706, 1311355706);
INSERT INTO `Action` VALUES(120, 'renforcer-defense', 'ETAT', 1, 1311355722, 1311355722);
INSERT INTO `Action` VALUES(121, 'creer-armee', 'ETAT', 1, 1311355675, 1311355675);
INSERT INTO `Action` VALUES(122, 'creer-armee', 'ETAT', 1, 1311355757, 1311355757);
INSERT INTO `Action` VALUES(123, 'creer-armee', 'ETAT', 1, 1311356016, 1311356016);
INSERT INTO `Action` VALUES(124, 'creer-armee', 'ETAT', 1, 1311356048, 1311356048);
INSERT INTO `Action` VALUES(125, 'creer-armee', 'ETAT', 1, 1311356049, 1311356049);
INSERT INTO `Action` VALUES(126, 'reduire-croissance-population', 'ETAT', 1, 1311356184, 1311356784);
INSERT INTO `Action` VALUES(127, 'reduire-croissance-population', 'ETAT', 1, 1311356194, 1311356794);
INSERT INTO `Action` VALUES(128, 'augmenter-croissance-population', 'ETAT', 1, 1311356205, 1311356805);
INSERT INTO `Action` VALUES(129, 'arreter-croissance-population', 'ETAT', 1, 1311356701, 1311357301);
INSERT INTO `Action` VALUES(130, 'creer-armee', 'ETAT', 1, 1311356719, 1311356719);
INSERT INTO `Action` VALUES(131, 'creer-armee', 'ETAT', 1, 1311356763, 1311356763);
INSERT INTO `Action` VALUES(132, 'creer-armee', 'ETAT', 1, 1311356793, 1311356793);
INSERT INTO `Action` VALUES(133, 'creer-armee', 'ETAT', 1, 1311356817, 1311356817);
INSERT INTO `Action` VALUES(134, 'creer-armee', 'ETAT', 1, 1311356885, 1311356885);
INSERT INTO `Action` VALUES(135, 'creer-armee', 'ETAT', 1, 1311356941, 1311356941);
INSERT INTO `Action` VALUES(136, 'creer-armee', 'ETAT', 1, 1311356985, 1311356985);
INSERT INTO `Action` VALUES(137, 'creer-armee', 'ETAT', 1, 1311356988, 1311356988);
INSERT INTO `Action` VALUES(138, 'creer-armee', 'ETAT', 1, 1311357035, 1311357035);
INSERT INTO `Action` VALUES(139, 'renforcer-defense', 'ETAT', 1, 1311357153, 1311357153);
INSERT INTO `Action` VALUES(140, 'affaiblir-defense', 'ETAT', 1, 1311357137, 1311357437);
INSERT INTO `Action` VALUES(141, 'renforcer-defense', 'ETAT', 1, 1311357178, 1311357178);
INSERT INTO `Action` VALUES(142, 'renforcer-defense', 'ETAT', 1, 1311357302, 1312357301);
INSERT INTO `Action` VALUES(143, 'renforcer-defense', 'ETAT', 1, 1311357346, 1312357345);
INSERT INTO `Action` VALUES(144, 'renforcer-defense', 'ETAT', 1, 1311357436, 1312357435);
INSERT INTO `Action` VALUES(145, 'renforcer-defense', 'ETAT', 1, 1311357508, 1312357507);
INSERT INTO `Action` VALUES(146, 'renforcer-defense', 'ETAT', 1, 1311357696, 1312357695);
INSERT INTO `Action` VALUES(147, 'renforcer-defense', 'ETAT', 1, 1311357733, 1312357732);
INSERT INTO `Action` VALUES(148, 'renforcer-defense', 'ETAT', 1, 1311357853, 1312357852);
INSERT INTO `Action` VALUES(149, 'renforcer-defense', 'ETAT', 1, 1311357978, 1312357977);
INSERT INTO `Action` VALUES(150, 'renforcer-defense', 'ETAT', 1, 1311359219, 1312359218);
INSERT INTO `Action` VALUES(151, 'renforcer-defense', 'ETAT', 1, 1311359286, 1312359285);
INSERT INTO `Action` VALUES(152, 'renforcer-defense', 'ETAT', 1, 1311359343, 1312359342);
INSERT INTO `Action` VALUES(153, 'renforcer-defense', 'ETAT', 1, 1311359357, 1312359356);
INSERT INTO `Action` VALUES(154, 'renforcer-defense', 'ETAT', 1, 1311359426, 1312359425);
INSERT INTO `Action` VALUES(155, 'renforcer-defense', 'ETAT', 1, 1311359461, 1312359460);
INSERT INTO `Action` VALUES(156, 'renforcer-defense', 'ETAT', 1, 1311359663, 1312359662);
INSERT INTO `Action` VALUES(157, 'renforcer-defense', 'ETAT', 1, 1311359684, 1312359683);
INSERT INTO `Action` VALUES(158, 'augmenter-croissance-population', 'ETAT', 1, 1311365929, 1311366529);
INSERT INTO `Action` VALUES(159, 'renforcer-defense', 'ETAT', 1, 1311366034, 1312366033);
INSERT INTO `Action` VALUES(160, 'entrainer-armee', 'ETAT', 1, 1311366688, 1312366687);
INSERT INTO `Action` VALUES(161, 'entrainer-armee', 'ETAT', 1, 1311367033, 1312367032);
INSERT INTO `Action` VALUES(162, 'creer-armee', 'ETAT', 1, 1311367978, 1311367978);
INSERT INTO `Action` VALUES(163, 'creer-armee', 'ETAT', 1, 1311368025, 1311368025);
INSERT INTO `Action` VALUES(164, 'creer-armee', 'ETAT', 1, 1311368235, 1311368235);
INSERT INTO `Action` VALUES(165, 'creer-armee', 'ETAT', 1, 1311368259, 1311368259);
INSERT INTO `Action` VALUES(166, 'creer-armee', 'ETAT', 1, 1311368301, 1311368301);
INSERT INTO `Action` VALUES(167, 'entrainer-armee', 'ETAT', 1, 1311368705, 1312368704);
INSERT INTO `Action` VALUES(168, 'entrainer-armee', 'ETAT', 1, 1311368709, 1312368708);
INSERT INTO `Action` VALUES(169, 'arreter-croissance-population', 'ETAT', 1, 1311368719, 1311369319);
INSERT INTO `Action` VALUES(170, 'entrainer-armee', 'ETAT', 1, 1311369045, 1312369044);
INSERT INTO `Action` VALUES(171, 'entrainer-armee', 'ETAT', 1, 1311369052, 1312369051);
INSERT INTO `Action` VALUES(172, 'entrainer-armee', 'ETAT', 1, 1311369418, 1312369417);
INSERT INTO `Action` VALUES(173, 'deplacer-armee', 'ETAT', 1, 1311369433, 1312369432);
INSERT INTO `Action` VALUES(174, 'augmenter-croissance-population', 'ETAT', 1, 1311369518, 1311370118);
INSERT INTO `Action` VALUES(175, 'renforcer-defense', 'ETAT', 1, 1311611892, 1311611892);
INSERT INTO `Action` VALUES(176, 'renforcer-defense', 'ETAT', 1, 1311611895, 1311611895);
INSERT INTO `Action` VALUES(177, 'renforcer-defense', 'ETAT', 1, 1311611950, 1311611950);
INSERT INTO `Action` VALUES(178, 'renforcer-defense', 'ETAT', 1, 1311611951, 1311611951);
INSERT INTO `Action` VALUES(179, 'renforcer-defense', 'ETAT', 1, 1311612007, 1311612007);
INSERT INTO `Action` VALUES(180, 'renforcer-defense', 'ETAT', 1, 1311612016, 1311612016);
INSERT INTO `Action` VALUES(181, 'renforcer-defense', 'ETAT', 1, 1311612072, 1311612072);
INSERT INTO `Action` VALUES(182, 'renforcer-defense', 'ETAT', 1, 1311612074, 1311612074);
INSERT INTO `Action` VALUES(183, 'renforcer-defense', 'ETAT', 1, 1311612087, 1311612087);
INSERT INTO `Action` VALUES(184, 'renforcer-defense', 'ETAT', 1, 1311612088, 1311612088);
INSERT INTO `Action` VALUES(185, 'deplacer-armee', 'ETAT', 1, 1311872941, 1312872940);
INSERT INTO `Action` VALUES(186, 'creer-armee', 'ETAT', 1, 1311873605, 1311873605);
INSERT INTO `Action` VALUES(187, 'supprimer-armee', 'ETAT', 1, 1311873681, 1311873681);
INSERT INTO `Action` VALUES(188, 'creer-armee', 'ETAT', 1, 1311874035, 1311874035);
INSERT INTO `Action` VALUES(189, 'creer-armee', 'ETAT', 1, 1311874036, 1311874036);
INSERT INTO `Action` VALUES(190, 'creer-armee', 'ETAT', 1, 1311875747, 1311875747);
INSERT INTO `Action` VALUES(191, 'supprimer-armee', 'ETAT', 1, 1311875757, 1311875757);
INSERT INTO `Action` VALUES(192, 'supprimer-armee', 'ETAT', 1, 1311875769, 1311875769);
INSERT INTO `Action` VALUES(193, 'supprimer-armee', 'ETAT', 1, 1311875782, 1311875782);
INSERT INTO `Action` VALUES(194, 'creer-armee', 'ETAT', 1, 1311957502, 1311957502);
INSERT INTO `Action` VALUES(195, 'deplacer-armee', 'ETAT', 1, 1311958854, 1312958853);
INSERT INTO `Action` VALUES(196, 'deplacer-armee', 'ETAT', 1, 1311958897, 1312958896);
INSERT INTO `Action` VALUES(197, 'deplacer-armee', 'ETAT', 1, 1311958911, 1312958910);
INSERT INTO `Action` VALUES(198, 'attaquer', 'ARMEE', 1, 1312216201, 1313216200);
INSERT INTO `Action` VALUES(199, 'attaquer', 'ARMEE', 1, 1312216282, 1313216281);
INSERT INTO `Action` VALUES(200, 'attaquer', 'ARMEE', 1, 1312216649, 1313216648);
INSERT INTO `Action` VALUES(201, 'attaquer', 'ARMEE', 1, 1312221559, 1313221558);
INSERT INTO `Action` VALUES(202, 'engager-armee', 'ETAT', 1, 1312227731, 1313227730);
INSERT INTO `Action` VALUES(203, 'engager-armee', 'ETAT', 1, 1312227749, 1313227748);
INSERT INTO `Action` VALUES(204, 'desengager-armee', 'ETAT', 1, 1312228053, 1313228052);
INSERT INTO `Action` VALUES(205, 'desengager-armee', 'ETAT', 1, 1312228231, 1313228230);
INSERT INTO `Action` VALUES(206, 'desengager-armee', 'ETAT', 1, 1312228236, 1313228235);
INSERT INTO `Action` VALUES(207, 'desengager-armee', 'ETAT', 1, 1312228290, 1313228289);
INSERT INTO `Action` VALUES(208, 'desengager-armee', 'ETAT', 1, 1312228293, 1313228292);
INSERT INTO `Action` VALUES(209, 'engager-armee', 'ETAT', 1, 1312228309, 1313228308);
INSERT INTO `Action` VALUES(210, 'engager-armee', 'ETAT', 1, 1312228310, 1313228309);
INSERT INTO `Action` VALUES(211, 'desengager-armee', 'ETAT', 1, 1312228312, 1313228311);
INSERT INTO `Action` VALUES(212, 'desengager-armee', 'ETAT', 1, 1312228312, 1313228311);
INSERT INTO `Action` VALUES(213, 'engager-armee', 'ETAT', 1, 1312300211, 1313300210);
INSERT INTO `Action` VALUES(214, 'desengager-armee', 'ETAT', 1, 1312300212, 1313300211);
INSERT INTO `Action` VALUES(215, 'creer-armee', 'ETAT', 1, 1312300748, 1312300748);
INSERT INTO `Action` VALUES(216, 'engager-armee', 'ETAT', 2, 1312300791, 1313300790);
INSERT INTO `Action` VALUES(217, 'deplacer-armee', 'ETAT', 1, 1312301512, 1313301511);
INSERT INTO `Action` VALUES(218, 'deplacer-armee', 'ETAT', 1, 1312301528, 1313301527);
INSERT INTO `Action` VALUES(219, 'deplacer-armee', 'ETAT', 1, 1312301545, 1313301544);
INSERT INTO `Action` VALUES(220, 'deplacer-armee', 'ETAT', 1, 1312301582, 1313301581);
INSERT INTO `Action` VALUES(221, 'deplacer-armee', 'ETAT', 1, 1312301589, 1313301588);
INSERT INTO `Action` VALUES(222, 'engager-armee', 'ETAT', 1, 1312302656, 1313302655);
INSERT INTO `Action` VALUES(223, 'desengager-armee', 'ETAT', 1, 1312302798, 1313302797);
INSERT INTO `Action` VALUES(224, 'engager-armee', 'ETAT', 1, 1312302848, 1313302847);
INSERT INTO `Action` VALUES(225, 'engager-armee', 'ETAT', 1, 1312302851, 1313302850);
INSERT INTO `Action` VALUES(226, 'creer-armee', 'ETAT', 1, 1312315699, 1312315699);
INSERT INTO `Action` VALUES(227, 'creer-armee', 'ETAT', 1, 1312315710, 1312315710);
INSERT INTO `Action` VALUES(228, 'deplacer-armee', 'ETAT', 1, 1312315723, 1313315722);
INSERT INTO `Action` VALUES(229, 'deplacer-armee', 'ETAT', 1, 1312315733, 1313315732);
INSERT INTO `Action` VALUES(230, 'creer-armee', 'ETAT', 1, 1312316219, 1312316219);
INSERT INTO `Action` VALUES(231, 'attaquer', 'ARMEE', 1, 1312401854, 1313401853);
INSERT INTO `Action` VALUES(232, 'engager-armee', 'ETAT', 1, 1312402081, 1313402080);
INSERT INTO `Action` VALUES(233, 'engager-armee', 'ETAT', 1, 1312402086, 1313402085);
INSERT INTO `Action` VALUES(234, 'engager-armee', 'ETAT', 1, 1312402128, 1313402127);
INSERT INTO `Action` VALUES(235, 'engager-armee', 'ETAT', 1, 1312402234, 1313402233);
INSERT INTO `Action` VALUES(236, 'engager-armee', 'ETAT', 1, 1312402235, 1313402234);
INSERT INTO `Action` VALUES(237, 'engager-armee', 'ETAT', 1, 1312402236, 1313402235);
INSERT INTO `Action` VALUES(238, 'engager-armee', 'ETAT', 1, 1312402287, 1313402286);
INSERT INTO `Action` VALUES(239, 'engager-armee', 'ETAT', 1, 1312402288, 1313402287);
INSERT INTO `Action` VALUES(240, 'engager-armee', 'ETAT', 1, 1312402298, 1313402297);
INSERT INTO `Action` VALUES(241, 'engager-armee', 'ETAT', 1, 1312402299, 1313402298);
INSERT INTO `Action` VALUES(242, 'engager-armee', 'ETAT', 1, 1312402300, 1313402299);
INSERT INTO `Action` VALUES(243, 'engager-armee', 'ETAT', 1, 1312402374, 1313402373);
INSERT INTO `Action` VALUES(244, 'engager-armee', 'ETAT', 1, 1312402441, 1313402440);
INSERT INTO `Action` VALUES(245, 'desengager-armee', 'ETAT', 1, 1312402490, 1313402489);
INSERT INTO `Action` VALUES(246, 'engager-armee', 'ETAT', 1, 1312402492, 1313402491);
INSERT INTO `Action` VALUES(247, 'engager-armee', 'ETAT', 1, 1312402494, 1313402493);
INSERT INTO `Action` VALUES(248, 'desengager-armee', 'ETAT', 1, 1312402968, 1313402967);
INSERT INTO `Action` VALUES(249, 'desengager-armee', 'ETAT', 1, 1312402969, 1313402968);
INSERT INTO `Action` VALUES(250, 'engager-armee', 'ETAT', 1, 1312402970, 1313402969);
INSERT INTO `Action` VALUES(251, 'desengager-armee', 'ETAT', 1, 1312403021, 1313403020);
INSERT INTO `Action` VALUES(252, 'engager-armee', 'ETAT', 1, 1312403022, 1313403021);
INSERT INTO `Action` VALUES(253, 'engager-armee', 'ETAT', 1, 1312403023, 1313403022);
INSERT INTO `Action` VALUES(254, 'desengager-armee', 'ETAT', 1, 1312403082, 1313403081);
INSERT INTO `Action` VALUES(255, 'desengager-armee', 'ETAT', 1, 1312403084, 1313403083);
INSERT INTO `Action` VALUES(256, 'engager-armee', 'ETAT', 1, 1312403085, 1313403084);
INSERT INTO `Action` VALUES(257, 'engager-armee', 'ETAT', 1, 1312403142, 1313403141);
INSERT INTO `Action` VALUES(258, 'desengager-armee', 'ETAT', 1, 1312403144, 1313403143);
INSERT INTO `Action` VALUES(259, 'engager-armee', 'ETAT', 1, 1312403145, 1313403144);
INSERT INTO `Action` VALUES(260, 'desengager-armee', 'ETAT', 1, 1312403157, 1313403156);
INSERT INTO `Action` VALUES(261, 'engager-armee', 'ETAT', 1, 1312403159, 1313403158);
INSERT INTO `Action` VALUES(262, 'desengager-armee', 'ETAT', 1, 1312403169, 1313403168);
INSERT INTO `Action` VALUES(263, 'engager-armee', 'ETAT', 1, 1312403171, 1313403170);
INSERT INTO `Action` VALUES(264, 'desengager-armee', 'ETAT', 1, 1312403196, 1313403195);
INSERT INTO `Action` VALUES(265, 'engager-armee', 'ETAT', 1, 1312403197, 1313403196);
INSERT INTO `Action` VALUES(266, 'desengager-armee', 'ETAT', 1, 1312403222, 1313403221);
INSERT INTO `Action` VALUES(267, 'desengager-armee', 'ETAT', 1, 1312403225, 1313403224);
INSERT INTO `Action` VALUES(268, 'engager-armee', 'ETAT', 1, 1312403227, 1313403226);
INSERT INTO `Action` VALUES(269, 'engager-armee', 'ETAT', 1, 1312403231, 1313403230);
INSERT INTO `Action` VALUES(270, 'desengager-armee', 'ETAT', 1, 1312403336, 1313403335);
INSERT INTO `Action` VALUES(271, 'engager-armee', 'ETAT', 1, 1312403337, 1313403336);
INSERT INTO `Action` VALUES(272, 'entrainer-armee', 'ETAT', 1, 1312403360, 1313403359);
INSERT INTO `Action` VALUES(273, 'desengager-armee', 'ETAT', 1, 1312403497, 1313403496);
INSERT INTO `Action` VALUES(274, 'engager-armee', 'ETAT', 1, 1312403499, 1313403498);
INSERT INTO `Action` VALUES(275, 'desengager-armee', 'ETAT', 1, 1312403508, 1313403507);
INSERT INTO `Action` VALUES(276, 'desengager-armee', 'ETAT', 1, 1312403510, 1313403509);
INSERT INTO `Action` VALUES(277, 'engager-armee', 'ETAT', 1, 1312403511, 1313403510);
INSERT INTO `Action` VALUES(278, 'desengager-armee', 'ETAT', 1, 1312403513, 1313403512);
INSERT INTO `Action` VALUES(279, 'engager-armee', 'ETAT', 1, 1312403593, 1313403592);
INSERT INTO `Action` VALUES(280, 'engager-armee', 'ETAT', 1, 1312403597, 1313403596);
INSERT INTO `Action` VALUES(281, 'desengager-armee', 'ETAT', 1, 1312478023, 1313478022);
INSERT INTO `Action` VALUES(282, 'desengager-armee', 'ETAT', 1, 1312478024, 1313478023);
INSERT INTO `Action` VALUES(283, 'engager-armee', 'ETAT', 1, 1312478026, 1313478025);
INSERT INTO `Action` VALUES(284, 'engager-armee', 'ETAT', 1, 1312478026, 1313478025);
INSERT INTO `Action` VALUES(285, 'desengager-armee', 'ETAT', 1, 1312478291, 1313478290);
INSERT INTO `Action` VALUES(286, 'engager-armee', 'ETAT', 1, 1312478293, 1313478292);
INSERT INTO `Action` VALUES(287, 'desengager-armee', 'ETAT', 1, 1312478296, 1313478295);
INSERT INTO `Action` VALUES(288, 'engager-armee', 'ETAT', 1, 1312478298, 1313478297);
INSERT INTO `Action` VALUES(289, 'desengager-armee', 'ETAT', 1, 1312478301, 1313478300);
INSERT INTO `Action` VALUES(290, 'engager-armee', 'ETAT', 1, 1312478344, 1313478343);
INSERT INTO `Action` VALUES(291, 'desengager-armee', 'ETAT', 1, 1312478360, 1313478359);
INSERT INTO `Action` VALUES(292, 'engager-armee', 'ETAT', 1, 1312478361, 1313478360);
INSERT INTO `Action` VALUES(293, 'desengager-armee', 'ETAT', 1, 1312478364, 1313478363);
INSERT INTO `Action` VALUES(294, 'engager-armee', 'ETAT', 1, 1312478418, 1313478417);
INSERT INTO `Action` VALUES(295, 'desengager-armee', 'ETAT', 1, 1312478436, 1313478435);
INSERT INTO `Action` VALUES(296, 'engager-armee', 'ETAT', 1, 1312478437, 1313478436);
INSERT INTO `Action` VALUES(297, 'desengager-armee', 'ETAT', 1, 1312478617, 1313478616);
INSERT INTO `Action` VALUES(298, 'engager-armee', 'ETAT', 1, 1312478619, 1313478618);
INSERT INTO `Action` VALUES(299, 'engager-armee', 'ETAT', 1, 1312478644, 1313478643);
INSERT INTO `Action` VALUES(300, 'desengager-armee', 'ETAT', 1, 1312478654, 1313478653);
INSERT INTO `Action` VALUES(301, 'engager-armee', 'ETAT', 1, 1312478655, 1313478654);
INSERT INTO `Action` VALUES(302, 'desengager-armee', 'ETAT', 1, 1312478810, 1313478809);
INSERT INTO `Action` VALUES(303, 'desengager-armee', 'ETAT', 1, 1312478811, 1313478810);
INSERT INTO `Action` VALUES(304, 'engager-armee', 'ETAT', 1, 1312478812, 1313478811);
INSERT INTO `Action` VALUES(305, 'engager-armee', 'ETAT', 1, 1312478828, 1313478827);
INSERT INTO `Action` VALUES(306, 'desengager-armee', 'ETAT', 1, 1312478881, 1313478880);
INSERT INTO `Action` VALUES(307, 'desengager-armee', 'ETAT', 1, 1312478882, 1313478881);
INSERT INTO `Action` VALUES(308, 'engager-armee', 'ETAT', 1, 1312478883, 1313478882);
INSERT INTO `Action` VALUES(309, 'desengager-armee', 'ETAT', 1, 1312478922, 1313478921);
INSERT INTO `Action` VALUES(310, 'engager-armee', 'ETAT', 1, 1312478923, 1313478922);
INSERT INTO `Action` VALUES(311, 'desengager-armee', 'ETAT', 1, 1312478939, 1313478938);
INSERT INTO `Action` VALUES(312, 'engager-armee', 'ETAT', 1, 1312478942, 1313478941);
INSERT INTO `Action` VALUES(313, 'desengager-armee', 'ETAT', 1, 1312478977, 1313478976);
INSERT INTO `Action` VALUES(314, 'engager-armee', 'ETAT', 1, 1312478978, 1313478977);
INSERT INTO `Action` VALUES(315, 'engager-armee', 'ETAT', 1, 1312479012, 1313479011);
INSERT INTO `Action` VALUES(316, 'desengager-armee', 'ETAT', 1, 1312479018, 1313479017);
INSERT INTO `Action` VALUES(317, 'desengager-armee', 'ETAT', 1, 1312479019, 1313479018);
INSERT INTO `Action` VALUES(318, 'engager-armee', 'ETAT', 1, 1312479022, 1313479021);
INSERT INTO `Action` VALUES(319, 'desengager-armee', 'ETAT', 1, 1312479054, 1313479053);
INSERT INTO `Action` VALUES(320, 'engager-armee', 'ETAT', 1, 1312479055, 1313479054);
INSERT INTO `Action` VALUES(321, 'engager-armee', 'ETAT', 1, 1312479061, 1313479060);
INSERT INTO `Action` VALUES(322, 'desengager-armee', 'ETAT', 1, 1312479078, 1313479077);
INSERT INTO `Action` VALUES(323, 'engager-armee', 'ETAT', 1, 1312479079, 1313479078);
INSERT INTO `Action` VALUES(324, 'desengager-armee', 'ETAT', 1, 1312479201, 1313479200);
INSERT INTO `Action` VALUES(325, 'desengager-armee', 'ETAT', 1, 1312479202, 1313479201);
INSERT INTO `Action` VALUES(326, 'engager-armee', 'ETAT', 1, 1312479204, 1313479203);
INSERT INTO `Action` VALUES(327, 'engager-armee', 'ETAT', 1, 1312479239, 1313479238);
INSERT INTO `Action` VALUES(328, 'desengager-armee', 'ETAT', 1, 1312479289, 1313479288);
INSERT INTO `Action` VALUES(329, 'desengager-armee', 'ETAT', 1, 1312479291, 1313479290);
INSERT INTO `Action` VALUES(330, 'engager-armee', 'ETAT', 1, 1312479292, 1313479291);
INSERT INTO `Action` VALUES(331, 'desengager-armee', 'ETAT', 1, 1312479329, 1313479328);
INSERT INTO `Action` VALUES(332, 'engager-armee', 'ETAT', 1, 1312479330, 1313479329);
INSERT INTO `Action` VALUES(333, 'desengager-armee', 'ETAT', 1, 1312479386, 1313479385);
INSERT INTO `Action` VALUES(334, 'desengager-armee', 'ETAT', 1, 1312479386, 1313479385);
INSERT INTO `Action` VALUES(335, 'engager-armee', 'ETAT', 1, 1312479387, 1313479386);
INSERT INTO `Action` VALUES(336, 'desengager-armee', 'ETAT', 1, 1312479420, 1313479419);
INSERT INTO `Action` VALUES(337, 'engager-armee', 'ETAT', 1, 1312479421, 1313479420);
INSERT INTO `Action` VALUES(338, 'desengager-armee', 'ETAT', 1, 1312479452, 1313479451);
INSERT INTO `Action` VALUES(339, 'engager-armee', 'ETAT', 1, 1312479453, 1313479452);
INSERT INTO `Action` VALUES(340, 'desengager-armee', 'ETAT', 1, 1312479472, 1313479471);
INSERT INTO `Action` VALUES(341, 'engager-armee', 'ETAT', 1, 1312479474, 1313479473);
INSERT INTO `Action` VALUES(342, 'desengager-armee', 'ETAT', 1, 1312479531, 1313479530);
INSERT INTO `Action` VALUES(343, 'engager-armee', 'ETAT', 1, 1312479533, 1313479532);
INSERT INTO `Action` VALUES(344, 'desengager-armee', 'ETAT', 1, 1312479572, 1313479571);
INSERT INTO `Action` VALUES(345, 'engager-armee', 'ETAT', 1, 1312479573, 1313479572);
INSERT INTO `Action` VALUES(346, 'desengager-armee', 'ETAT', 1, 1312479660, 1313479659);
INSERT INTO `Action` VALUES(347, 'engager-armee', 'ETAT', 1, 1312479661, 1313479660);
INSERT INTO `Action` VALUES(348, 'engager-armee', 'ETAT', 1, 1312480414, 1313480413);
INSERT INTO `Action` VALUES(349, 'desengager-armee', 'ETAT', 1, 1312480640, 1313480639);
INSERT INTO `Action` VALUES(350, 'desengager-armee', 'ETAT', 1, 1312489903, 1313489902);
INSERT INTO `Action` VALUES(351, 'engager-armee', 'ETAT', 1, 1312489906, 1313489905);
INSERT INTO `Action` VALUES(352, 'engager-armee', 'ETAT', 1, 1312489906, 1313489905);
INSERT INTO `Action` VALUES(353, 'engager-armee', 'ETAT', 1, 1312489908, 1313489907);
INSERT INTO `Action` VALUES(354, 'engager-armee', 'ETAT', 1, 1312489911, 1313489910);
INSERT INTO `Action` VALUES(355, 'engager-armee', 'ETAT', 2, 1312489945, 1313489944);
INSERT INTO `Action` VALUES(356, 'desengager-armee', 'ETAT', 1, 1312489963, 1313489962);
INSERT INTO `Action` VALUES(357, 'engager-armee', 'ETAT', 1, 1312489965, 1313489964);
INSERT INTO `Action` VALUES(358, 'cibler-armee', 'ETAT', 1, 1312490673, 1313490672);
INSERT INTO `Action` VALUES(359, 'decibler-armee', 'ETAT', 1, 1312490686, 1313490685);
INSERT INTO `Action` VALUES(360, 'cibler-armee', 'ETAT', 2, 1312490741, 1313490740);
INSERT INTO `Action` VALUES(361, 'cibler-armee', 'ETAT', 2, 1312490743, 1313490742);
INSERT INTO `Action` VALUES(362, 'decibler-armee', 'ETAT', 2, 1312490745, 1313490744);
INSERT INTO `Action` VALUES(363, 'cibler-armee', 'ETAT', 1, 1313008358, 1314008357);
INSERT INTO `Action` VALUES(364, 'decibler-armee', 'ETAT', 1, 1313008445, 1314008444);
INSERT INTO `Action` VALUES(365, 'cibler-armee', 'ETAT', 1, 1313008488, 1314008487);
INSERT INTO `Action` VALUES(366, 'decibler-armee', 'ETAT', 1, 1313008531, 1314008530);
INSERT INTO `Action` VALUES(367, 'cibler-armee', 'ETAT', 1, 1313008661, 1314008660);
INSERT INTO `Action` VALUES(368, 'decibler-armee', 'ETAT', 1, 1313008673, 1314008672);
INSERT INTO `Action` VALUES(369, 'cibler-armee', 'ETAT', 1, 1313008683, 1314008682);
INSERT INTO `Action` VALUES(370, 'creer-armee', 'ETAT', 1, 1313008729, 1313008729);
INSERT INTO `Action` VALUES(371, 'engager-armee', 'ETAT', 1, 1313008766, 1314008765);
INSERT INTO `Action` VALUES(372, 'cibler-armee', 'ETAT', 2, 1313008786, 1314008785);
INSERT INTO `Action` VALUES(373, 'decibler-armee', 'ETAT', 2, 1313008794, 1314008793);
INSERT INTO `Action` VALUES(374, 'decibler-armee', 'ETAT', 2, 1313008794, 1314008793);

-- --------------------------------------------------------

--
-- Structure de la table `Agent`
--

CREATE TABLE `Agent` (
  `AgentID` mediumint(5) NOT NULL AUTO_INCREMENT,
  `AgentNom` varchar(25) NOT NULL,
  `AgentEtat` smallint(3) NOT NULL,
  `AgentEtatOrigine` smallint(3) NOT NULL,
  `AgentStatut` smallint(1) NOT NULL DEFAULT '1',
  `AgentSecret` tinyint(1) NOT NULL DEFAULT '1',
  `AgentTerritoire` smallint(4) NOT NULL,
  `AgentCapaciteFurtivite` smallint(3) NOT NULL,
  `AgentCapaciteVitesse` smallint(3) NOT NULL,
  `AgentCapaciteReussite` smallint(3) NOT NULL,
  `AgentType` varchar(20) NOT NULL,
  `AgentTime` int(11) NOT NULL,
  PRIMARY KEY (`AgentID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `Agent`
--

INSERT INTO `Agent` VALUES(1, 'Rondont', 1, 1, 0, 0, 127, 10, 5, 20, 'GÃ©nÃ©ral', 1308755820);
INSERT INTO `Agent` VALUES(2, 'Rondont', 1, 1, 0, 0, 135, 10, 5, 20, 'GÃ©nÃ©ral', 1308843028);
INSERT INTO `Agent` VALUES(3, 'Rondont', 1, 1, 0, 0, 127, 10, 5, 20, 'GÃ©nÃ©ral', 1308864130);
INSERT INTO `Agent` VALUES(4, 'Rondont', 1, 1, 0, 0, 93, 10, 5, 20, 'GÃ©nÃ©ral', 1309982934);
INSERT INTO `Agent` VALUES(5, 'Rondont', 1, 1, 0, 0, 1, 10, 5, 20, 'GÃ©nÃ©ral', 1310493301);

-- --------------------------------------------------------

--
-- Structure de la table `Armee`
--

CREATE TABLE `Armee` (
  `ArmeeID` smallint(4) NOT NULL AUTO_INCREMENT,
  `ArmeeEtat` smallint(3) NOT NULL,
  `ArmeeNom` varchar(32) NOT NULL DEFAULT 'Nom',
  `ArmeeTerritoire` smallint(4) NOT NULL,
  `ArmeeType` varchar(30) NOT NULL DEFAULT 'infanterie',
  `ArmeeTaille` mediumint(5) NOT NULL DEFAULT '100',
  `ArmeeNombre` mediumint(5) NOT NULL,
  `ArmeeBlesses` mediumint(5) NOT NULL,
  `ArmeeXP` smallint(4) NOT NULL DEFAULT '0',
  `ArmeeLieu` smallint(3) NOT NULL DEFAULT '0',
  `ArmeeMoral` mediumint(5) NOT NULL,
  PRIMARY KEY (`ArmeeID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `Armee`
--

INSERT INTO `Armee` VALUES(1, 1, 'Attaque 1', 127, 'infanterie', 150, 0, 0, 0, 114, 50);
INSERT INTO `Armee` VALUES(2, 1, 'Attaque 2', 127, 'infanterie-legere', 100, 0, 0, 0, 114, 74);
INSERT INTO `Armee` VALUES(3, 2, 'DÃ©fense unique', 114, 'infanterie-lourde', 200, 0, 0, 15, 114, 120);
INSERT INTO `Armee` VALUES(4, 1, 'DeuxiÃ¨me def', 114, 'infanterie-legere', 100, 0, 0, 0, 114, 50);

-- --------------------------------------------------------

--
-- Structure de la table `Bataille`
--

CREATE TABLE `Bataille` (
  `BatailleID` mediumint(4) NOT NULL AUTO_INCREMENT,
  `BatailleTimeDebut` bigint(11) NOT NULL,
  `BatailleTimeDernier` bigint(11) NOT NULL,
  `BatailleAttaquant` smallint(4) NOT NULL,
  `BatailleDefenseur` smallint(5) NOT NULL,
  `BatailleMorts` mediumint(6) NOT NULL,
  `BatailleTitre` varchar(30) NOT NULL,
  `BatailleGuerre` smallint(3) NOT NULL,
  `BatailleTerritoire` smallint(5) NOT NULL,
  PRIMARY KEY (`BatailleID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `Bataille`
--

INSERT INTO `Bataille` VALUES(1, 60, 0, 1, 2, 0, 'Lol', 0, 114);

-- --------------------------------------------------------

--
-- Structure de la table `Combattant`
--

CREATE TABLE `Combattant` (
  `CombattantBataille` smallint(4) NOT NULL,
  `CombattantEtat` smallint(3) NOT NULL,
  `CombattantID` smallint(5) NOT NULL,
  `CombattantEquipe` tinyint(2) NOT NULL,
  `CombattantMorts` mediumint(6) NOT NULL,
  `CombattantProchaineAttaque` bigint(11) NOT NULL,
  PRIMARY KEY (`CombattantID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `Combattant`
--

INSERT INTO `Combattant` VALUES(1, 1, 2, 1, 0, 1313008699);
INSERT INTO `Combattant` VALUES(1, 1, 1, 1, 0, 1313008689);
INSERT INTO `Combattant` VALUES(1, 2, 3, 2, 0, 1313008829);
INSERT INTO `Combattant` VALUES(1, 1, 4, 1, 0, 1313008744);

-- --------------------------------------------------------

--
-- Structure de la table `CombattantCible`
--

CREATE TABLE `CombattantCible` (
  `CombattantCibleEtat` mediumint(5) NOT NULL,
  `CombattantCibleArmee` mediumint(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `CombattantCible`
--

INSERT INTO `CombattantCible` VALUES(1, 3);

-- --------------------------------------------------------

--
-- Structure de la table `Effet`
--

CREATE TABLE `Effet` (
  `EffetID` mediumint(6) NOT NULL AUTO_INCREMENT,
  `EffetAction` mediumint(6) NOT NULL,
  `EffetCibleType` varchar(15) NOT NULL COMMENT 'ETAT, TERRITOIRE, ',
  `EffetCibleID` mediumint(6) NOT NULL,
  `EffetTimeDebut` int(11) NOT NULL,
  `EffetTimeFin` int(11) NOT NULL,
  `EffetTable` varchar(30) NOT NULL,
  `EffetVariable` varchar(50) NOT NULL,
  `EffetType` varchar(20) NOT NULL COMMENT 'ADDITION, SOUSTRACTION, DIVISION, MULTIPLICATION',
  `EffetValeur` float NOT NULL,
  PRIMARY KEY (`EffetID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=199 ;

--
-- Contenu de la table `Effet`
--

INSERT INTO `Effet` VALUES(1, 1, 'TERRITOIRE', 127, 1308830683, 1308830743, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(2, 1, 'TERRITOIRE', 127, 1308830713, 1308830773, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(3, 2, 'TERRITOIRE', 127, 1308830738, 1308830798, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(4, 2, 'TERRITOIRE', 127, 1308830768, 1308830828, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(5, 3, 'TERRITOIRE', 127, 1308835883, 1308835943, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(6, 3, 'TERRITOIRE', 127, 1308835913, 1308835973, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(7, 4, 'TERRITOIRE', 127, 1308835974, 1308836034, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(8, 4, 'TERRITOIRE', 127, 1308836004, 1308836064, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(9, 5, 'TERRITOIRE', 127, 1308836029, 1308836089, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(10, 5, 'TERRITOIRE', 127, 1308836059, 1308836119, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(11, 6, 'TERRITOIRE', 114, 1308837118, 1308837178, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(12, 6, 'TERRITOIRE', 114, 1308837148, 1308837208, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(13, 7, 'TERRITOIRE', 114, 1308842034, 1308842094, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(14, 7, 'TERRITOIRE', 114, 1308842064, 1308842124, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(15, 8, 'TERRITOIRE', 135, 1308843056, 1308843116, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(16, 8, 'TERRITOIRE', 135, 1308843086, 1308843146, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(17, 9, 'TERRITOIRE', 135, 1308843057, 1308843117, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(18, 9, 'TERRITOIRE', 135, 1308843087, 1308843147, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(19, 10, 'TERRITOIRE', 135, 1308843059, 1308843119, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(20, 10, 'TERRITOIRE', 135, 1308843089, 1308843149, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(21, 11, 'TERRITOIRE', 135, 1308843064, 1308843124, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(22, 11, 'TERRITOIRE', 135, 1308843094, 1308843154, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(23, 12, 'TERRITOIRE', 127, 1308843076, 1308843136, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(24, 12, 'TERRITOIRE', 127, 1308843106, 1308843166, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(25, 13, 'TERRITOIRE', 127, 1308843089, 1308843149, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(26, 13, 'TERRITOIRE', 127, 1308843119, 1308843179, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(27, 14, 'TERRITOIRE', 127, 1308843383, 1308843443, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(28, 14, 'TERRITOIRE', 127, 1308843413, 1308843473, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(29, 15, 'TERRITOIRE', 127, 1308844558, 1308844618, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(30, 15, 'TERRITOIRE', 127, 1308844588, 1308844648, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(31, 18, 'TERRITOIRE', 127, 1308870268, 1308870328, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(32, 18, 'TERRITOIRE', 127, 1308870298, 1308870358, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(33, 19, 'TERRITOIRE', 127, 1308934806, 1308934866, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(34, 19, 'TERRITOIRE', 127, 1308934836, 1308934896, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(35, 20, 'TERRITOIRE', 114, 1309982418, 1309982478, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(36, 20, 'TERRITOIRE', 114, 1309982448, 1309982508, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(37, 21, 'TERRITOIRE', 132, 1310057857, 1310057917, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(38, 21, 'TERRITOIRE', 132, 1310057887, 1310057947, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(39, 22, 'TERRITOIRE', 114, 1310070334, 1310070394, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(40, 22, 'TERRITOIRE', 114, 1310070364, 1310070424, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(41, 23, 'TERRITOIRE', 42, 1310492637, 1310492697, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(42, 23, 'TERRITOIRE', 42, 1310492667, 1310492727, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(43, 24, 'TERRITOIRE', 1, 1310493331, 1310493391, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(44, 24, 'TERRITOIRE', 1, 1310493361, 1310493421, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(45, 25, 'TERRITOIRE', 132, 1310495033, 1310495093, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(46, 25, 'TERRITOIRE', 132, 1310495063, 1310495123, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(47, 26, 'TERRITOIRE', 135, 1310497572, 1310497632, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(48, 26, 'TERRITOIRE', 135, 1310497602, 1310497662, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(49, 27, 'TERRITOIRE', 132, 1310497626, 1310497686, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(50, 27, 'TERRITOIRE', 132, 1310497656, 1310497716, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(51, 28, 'TERRITOIRE', 132, 1310503264, 1310503324, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(52, 28, 'TERRITOIRE', 132, 1310503294, 1310503354, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(53, 29, 'TERRITOIRE', 132, 1311009146, 1311009206, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(54, 29, 'TERRITOIRE', 132, 1311009176, 1311009236, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(55, 30, 'TERRITOIRE', 132, 1311021809, 1311021869, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(56, 30, 'TERRITOIRE', 132, 1311021839, 1311021899, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(57, 31, 'TERRITOIRE', 127, 1311021956, 1311022016, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(58, 31, 'TERRITOIRE', 127, 1311021986, 1311022046, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(59, 32, 'TERRITOIRE', 127, 1311021960, 1311022020, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(60, 32, 'TERRITOIRE', 127, 1311021990, 1311022050, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(61, 33, 'TERRITOIRE', 127, 1311021961, 1311022021, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(62, 33, 'TERRITOIRE', 127, 1311021991, 1311022051, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(63, 1, 'TERRITOIRE', 127, 1311093311, 1311093371, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(64, 1, 'TERRITOIRE', 127, 1311093341, 1311093401, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(65, 2, 'TERRITOIRE', 127, 1311093399, 1311093459, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(66, 2, 'TERRITOIRE', 127, 1311093429, 1311093489, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(67, 3, 'TERRITOIRE', 127, 1311093403, 1311093463, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(68, 3, 'TERRITOIRE', 127, 1311093433, 1311093493, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(69, 4, 'TERRITOIRE', 127, 1311093404, 1311093464, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(70, 4, 'TERRITOIRE', 127, 1311093434, 1311093494, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(71, 5, 'TERRITOIRE', 127, 1311093426, 1311093486, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(72, 5, 'TERRITOIRE', 127, 1311093456, 1311093516, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(73, 6, 'TERRITOIRE', 127, 1311093447, 1311093507, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(74, 6, 'TERRITOIRE', 127, 1311093477, 1311093537, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(75, 7, 'TERRITOIRE', 127, 1311093486, 1311093546, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(76, 7, 'TERRITOIRE', 127, 1311093516, 1311093576, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(77, 8, 'TERRITOIRE', 127, 1311093505, 1311093565, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(78, 8, 'TERRITOIRE', 127, 1311093535, 1311093595, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(79, 9, 'TERRITOIRE', 127, 1311093541, 1311093601, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(80, 9, 'TERRITOIRE', 127, 1311093571, 1311093631, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(81, 10, 'TERRITOIRE', 127, 1311093560, 1311093620, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(82, 10, 'TERRITOIRE', 127, 1311093590, 1311093650, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(83, 11, 'TERRITOIRE', 127, 1311093599, 1311093659, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(84, 11, 'TERRITOIRE', 127, 1311093629, 1311093689, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(85, 12, 'TERRITOIRE', 127, 1311093600, 1311093660, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(86, 12, 'TERRITOIRE', 127, 1311093630, 1311093690, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(87, 13, 'TERRITOIRE', 127, 1311093807, 1311093867, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(88, 13, 'TERRITOIRE', 127, 1311093837, 1311093897, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(89, 14, 'TERRITOIRE', 127, 1311093899, 1311093959, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(90, 14, 'TERRITOIRE', 127, 1311093929, 1311093989, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(91, 15, 'TERRITOIRE', 127, 1311093958, 1311094018, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(92, 15, 'TERRITOIRE', 127, 1311093988, 1311094048, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(93, 16, 'TERRITOIRE', 127, 1311093986, 1311094046, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(94, 16, 'TERRITOIRE', 127, 1311094016, 1311094076, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(95, 17, 'TERRITOIRE', 127, 1311093987, 1311094047, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(96, 17, 'TERRITOIRE', 127, 1311094017, 1311094077, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(97, 18, 'TERRITOIRE', 127, 1311094015, 1311094075, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(98, 18, 'TERRITOIRE', 127, 1311094045, 1311094105, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(99, 19, 'TERRITOIRE', 127, 1311094016, 1311094076, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(100, 19, 'TERRITOIRE', 127, 1311094046, 1311094106, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(101, 20, 'TERRITOIRE', 127, 1311094101, 1311094161, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(102, 20, 'TERRITOIRE', 127, 1311094131, 1311094191, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(103, 21, 'TERRITOIRE', 127, 1311094102, 1311094162, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(104, 21, 'TERRITOIRE', 127, 1311094132, 1311094192, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(105, 22, 'TERRITOIRE', 127, 1311094139, 1311094199, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(106, 22, 'TERRITOIRE', 127, 1311094169, 1311094229, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(107, 23, 'TERRITOIRE', 127, 1311094141, 1311094201, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(108, 23, 'TERRITOIRE', 127, 1311094171, 1311094231, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(109, 24, 'TERRITOIRE', 127, 1311107717, 1311107777, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(110, 24, 'TERRITOIRE', 127, 1311107747, 1311107807, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(111, 25, 'TERRITOIRE', 127, 1311108005, 1311108065, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(112, 25, 'TERRITOIRE', 127, 1311108035, 1311108095, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(113, 26, 'TERRITOIRE', 127, 1311108027, 1311108087, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(114, 26, 'TERRITOIRE', 127, 1311108057, 1311108117, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(115, 27, 'TERRITOIRE', 127, 1311108030, 1311108090, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(116, 27, 'TERRITOIRE', 127, 1311108060, 1311108120, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(117, 28, 'TERRITOIRE', 127, 1311108031, 1311108091, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(118, 28, 'TERRITOIRE', 127, 1311108061, 1311108121, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(119, 29, 'TERRITOIRE', 127, 1311108045, 1311108105, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(120, 29, 'TERRITOIRE', 127, 1311108075, 1311108135, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(121, 30, 'TERRITOIRE', 127, 1311108047, 1311108107, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(122, 30, 'TERRITOIRE', 127, 1311108077, 1311108137, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(123, 31, 'TERRITOIRE', 132, 1311180484, 1311180544, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(124, 31, 'TERRITOIRE', 132, 1311180514, 1311180574, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(125, 32, 'TERRITOIRE', 132, 1311180487, 1311180547, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(126, 32, 'TERRITOIRE', 132, 1311180517, 1311180577, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(127, 33, 'TERRITOIRE', 132, 1311180464, 1311181064, 'Territoire', 'TerritoireCroissance', 'SUBSTITUTION', 0);
INSERT INTO `Effet` VALUES(128, 34, 'TERRITOIRE', 114, 1311180923, 1311181523, 'Territoire', 'TerritoireCroissance', 'SUBSTITUTION', 0);
INSERT INTO `Effet` VALUES(129, 35, 'TERRITOIRE', 127, 1311181359, 1311181959, 'Territoire', 'TerritoireCroissance', 'SUBSTITUTION', 0);
INSERT INTO `Effet` VALUES(130, 36, 'TERRITOIRE', 127, 1311181365, 1311181965, 'Territoire', 'TerritoireCroissance', 'SUBSTITUTION', 0);
INSERT INTO `Effet` VALUES(131, 37, 'TERRITOIRE', 26, 1311181422, 1311181482, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(132, 37, 'TERRITOIRE', 26, 1311181452, 1311181512, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(133, 38, 'TERRITOIRE', 93, 1311181462, 1311181522, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(134, 38, 'TERRITOIRE', 93, 1311181492, 1311181552, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(135, 39, 'TERRITOIRE', 93, 1311181467, 1311181527, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(136, 39, 'TERRITOIRE', 93, 1311181497, 1311181557, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(137, 40, 'TERRITOIRE', 26, 1311181488, 1311181548, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(138, 40, 'TERRITOIRE', 26, 1311181518, 1311181578, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(139, 41, 'TERRITOIRE', 26, 1311181530, 1311181590, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(140, 41, 'TERRITOIRE', 26, 1311181560, 1311181620, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(141, 42, 'TERRITOIRE', 26, 1311181531, 1311181591, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(142, 42, 'TERRITOIRE', 26, 1311181561, 1311181621, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(143, 43, 'TERRITOIRE', 26, 1311181535, 1311181595, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(144, 43, 'TERRITOIRE', 26, 1311181565, 1311181625, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(145, 44, 'TERRITOIRE', 26, 1311181507, 1311182107, 'Territoire', 'TerritoireCroissance', 'SUBSTITUTION', 0);
INSERT INTO `Effet` VALUES(146, 45, 'TERRITOIRE', 132, 1311182498, 1311183098, 'Territoire', 'TerritoireCroissance', 'ADDITION', 1);
INSERT INTO `Effet` VALUES(147, 46, 'TERRITOIRE', 114, 1311182518, 1311183118, 'Territoire', 'TerritoireCroissance', 'ADDITION', 1);
INSERT INTO `Effet` VALUES(148, 47, 'TERRITOIRE', 114, 1311182520, 1311183120, 'Territoire', 'TerritoireCroissance', 'ADDITION', 1);
INSERT INTO `Effet` VALUES(149, 48, 'TERRITOIRE', 114, 1311182622, 1311183222, 'Territoire', 'TerritoireCroissance', 'SOUSTRACTION', 2);
INSERT INTO `Effet` VALUES(150, 49, 'TERRITOIRE', 114, 1311182624, 1311183224, 'Territoire', 'TerritoireCroissance', 'SOUSTRACTION', 2);
INSERT INTO `Effet` VALUES(151, 50, 'TERRITOIRE', 127, 1311182703, 1311182763, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(152, 50, 'TERRITOIRE', 127, 1311182733, 1311182793, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(153, 51, 'TERRITOIRE', 127, 1311182714, 1311182774, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(154, 51, 'TERRITOIRE', 127, 1311182744, 1311182804, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(155, 52, 'TERRITOIRE', 127, 1311182822, 1311183122, 'Territoire', 'TerritoireDefense', 'SOUSTRACTION', 10);
INSERT INTO `Effet` VALUES(156, 53, 'TERRITOIRE', 127, 1311182824, 1311183124, 'Territoire', 'TerritoireDefense', 'SOUSTRACTION', 10);
INSERT INTO `Effet` VALUES(157, 54, 'TERRITOIRE', 77, 1311182864, 1311183464, 'Territoire', 'TerritoireCroissance', 'ADDITION', 1);
INSERT INTO `Effet` VALUES(158, 55, 'TERRITOIRE', 77, 1311182865, 1311183465, 'Territoire', 'TerritoireCroissance', 'ADDITION', 1);
INSERT INTO `Effet` VALUES(159, 56, 'TERRITOIRE', 77, 1311182866, 1311183466, 'Territoire', 'TerritoireCroissance', 'ADDITION', 1);
INSERT INTO `Effet` VALUES(160, 57, 'TERRITOIRE', 114, 1311182878, 1311183478, 'Territoire', 'TerritoireCroissance', 'SOUSTRACTION', 2);
INSERT INTO `Effet` VALUES(161, 58, 'TERRITOIRE', 114, 1311182879, 1311183479, 'Territoire', 'TerritoireCroissance', 'SOUSTRACTION', 2);
INSERT INTO `Effet` VALUES(162, 59, 'TERRITOIRE', 114, 1311182880, 1311183480, 'Territoire', 'TerritoireCroissance', 'SOUSTRACTION', 2);
INSERT INTO `Effet` VALUES(163, 60, 'TERRITOIRE', 127, 1311182970, 1311183030, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(164, 60, 'TERRITOIRE', 127, 1311183000, 1311183060, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(165, 92, 'TERRITOIRE', 127, 1311354508, 1311355108, 'Territoire', 'TerritoireCroissance', 'SUBSTITUTION', 0);
INSERT INTO `Effet` VALUES(166, 93, 'TERRITOIRE', 127, 1311354599, 1311355199, 'Territoire', 'TerritoireCroissance', 'SOUSTRACTION', 2);
INSERT INTO `Effet` VALUES(167, 101, 'TERRITOIRE', 77, 1311354967, 1311354967, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(168, 102, 'TERRITOIRE', 77, 1311354975, 1311354975, 'Territoire', 'TerritoireDefense', 'ADDITION', 20);
INSERT INTO `Effet` VALUES(169, 118, 'TERRITOIRE', 114, 1311355615, 1311356215, 'Territoire', 'TerritoireCroissance', 'SUBSTITUTION', 0);
INSERT INTO `Effet` VALUES(170, 119, 'TERRITOIRE', 114, 1311355706, 1311355706, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(171, 120, 'TERRITOIRE', 26, 1311355722, 1311355722, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(172, 126, 'TERRITOIRE', 127, 1311356184, 1311356784, 'Territoire', 'TerritoireCroissance', 'SOUSTRACTION', 2);
INSERT INTO `Effet` VALUES(173, 127, 'TERRITOIRE', 77, 1311356194, 1311356794, 'Territoire', 'TerritoireCroissance', 'SOUSTRACTION', 2);
INSERT INTO `Effet` VALUES(174, 128, 'TERRITOIRE', 127, 1311356205, 1311356805, 'Territoire', 'TerritoireCroissance', 'ADDITION', 1);
INSERT INTO `Effet` VALUES(175, 129, 'TERRITOIRE', 114, 1311356701, 1311357301, 'Territoire', 'TerritoireCroissance', 'SUBSTITUTION', 0);
INSERT INTO `Effet` VALUES(176, 139, 'TERRITOIRE', 114, 1311357153, 1311357153, 'Territoire', 'TerritoireDefense', 'ADDITION', 30);
INSERT INTO `Effet` VALUES(177, 140, 'TERRITOIRE', 114, 1311357137, 1311357437, 'Territoire', 'TerritoireDefense', 'SOUSTRACTION', 10);
INSERT INTO `Effet` VALUES(178, 141, 'TERRITOIRE', 77, 1311357178, 1311357178, 'Territoire', 'TerritoireDefense', 'ADDITION', 100);
INSERT INTO `Effet` VALUES(179, 142, 'TERRITOIRE', 127, 1311357302, 1311357302, 'Territoire', 'TerritoireDefense', 'ADDITION', 100);
INSERT INTO `Effet` VALUES(180, 143, 'TERRITOIRE', 26, 1311357346, 1311357346, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(181, 144, 'TERRITOIRE', 26, 1311357436, 1312357435, 'Territoire', 'TerritoireDefense', 'ADDITION', 100);
INSERT INTO `Effet` VALUES(182, 145, 'TERRITOIRE', 127, 1311357508, 2147483647, 'Territoire', 'TerritoireDefense', 'ADDITION', 100);
INSERT INTO `Effet` VALUES(183, 146, 'TERRITOIRE', 77, 1311357696, 2147483647, 'Territoire', 'TerritoireDefense', 'ADDITION', 100);
INSERT INTO `Effet` VALUES(184, 147, 'TERRITOIRE', 48, 1311357733, 2147483647, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(185, 148, 'TERRITOIRE', 48, 1311357853, 343, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(186, 149, 'TERRITOIRE', 38, 1311357978, 2147483647, 'Territoire', 'TerritoireDefense', 'ADDITION', 0);
INSERT INTO `Effet` VALUES(187, 150, 'TERRITOIRE', 38, 1311359219, 0, 'Territoire', 'TerritoireDefense', 'ADDITION', 0);
INSERT INTO `Effet` VALUES(188, 151, 'TERRITOIRE', 11, 34, 0, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(189, 153, 'TERRITOIRE', 127, 1311359357, 2147483647, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(190, 154, 'TERRITOIRE', 26, 1311359426, 2147483647, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(191, 155, 'TERRITOIRE', 48, 1311359461, 2147483647, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(192, 156, 'TERRITOIRE', 48, 1312359593, 2147483647, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(193, 157, 'TERRITOIRE', 26, 1312359614, 2147483647, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(194, 158, 'TERRITOIRE', 88, 1311365929, 1311366529, 'Territoire', 'TerritoireCroissance', 'ADDITION', 1);
INSERT INTO `Effet` VALUES(195, 159, 'TERRITOIRE', 88, 1311366034, 2147483647, 'Territoire', 'TerritoireDefense', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(196, 160, 'ARMEE', 34, 1311366688, 2147483647, 'Armee', 'ArmeeXP', 'ADDITION', 10);
INSERT INTO `Effet` VALUES(197, 169, 'TERRITOIRE', 127, 1311368719, 1311369319, 'Territoire', 'TerritoireCroissance', 'SUBSTITUTION', 0);
INSERT INTO `Effet` VALUES(198, 174, 'TERRITOIRE', 127, 1311369518, 1311370118, 'Territoire', 'TerritoireCroissance', 'ADDITION', 1);

-- --------------------------------------------------------

--
-- Structure de la table `Etat`
--

CREATE TABLE `Etat` (
  `EtatID` smallint(3) NOT NULL AUTO_INCREMENT COMMENT 'Identifiant de l''Etat',
  `EtatJoueur` smallint(3) NOT NULL,
  `EtatPartie` smallint(3) NOT NULL,
  `EtatNom` varchar(32) NOT NULL DEFAULT 'Nom de votre Etat',
  `EtatCouleur` varchar(15) NOT NULL DEFAULT 'blanche',
  `EtatTerritoires` smallint(3) NOT NULL DEFAULT '0' COMMENT 'Nb de territoires contollés',
  `EtatPopulation` mediumint(7) NOT NULL DEFAULT '0',
  `EtatCroissance` float NOT NULL DEFAULT '4',
  `EtatPointCivil` float NOT NULL DEFAULT '0',
  `EtatPointCommerce` float NOT NULL DEFAULT '0',
  `EtatPointMilitaire` float NOT NULL DEFAULT '0',
  `EtatPointReligion` float NOT NULL DEFAULT '0',
  `EtatPopulationCivil` smallint(2) NOT NULL DEFAULT '20',
  `EtatPopulationCommerce` smallint(2) NOT NULL DEFAULT '20',
  `EtatPopulationMilitaire` smallint(2) NOT NULL DEFAULT '20',
  `EtatPopulationReligion` smallint(2) NOT NULL DEFAULT '20',
  `EtatDerniereProduction` int(10) NOT NULL,
  `EtatFamine` mediumint(5) NOT NULL DEFAULT '0',
  `EtatTaxe` float NOT NULL DEFAULT '30',
  `EtatOr` float NOT NULL DEFAULT '10',
  PRIMARY KEY (`EtatID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Un Etat = Un joueur dans une partie' AUTO_INCREMENT=3 ;

--
-- Contenu de la table `Etat`
--

INSERT INTO `Etat` VALUES(1, 1, 1, 'The A state de la mort', 'bleue', 2, 7960, 0.15, 10521.2, 11221.3, 53164.7, 32614.8, 10, 11, 47, 32, 1313008875, 0, 30, 32631.6);
INSERT INTO `Etat` VALUES(2, 2, 1, 'The B State', 'rouge', 2, 8056, -0.2, 1396.01, 1396.01, 1598.01, 1396.01, 20, 20, 20, 20, 1313008814, 60, 30, 2154.01);

-- --------------------------------------------------------

--
-- Structure de la table `Joueur`
--

CREATE TABLE `Joueur` (
  `JoueurID` smallint(3) NOT NULL AUTO_INCREMENT,
  `JoueurMdp` varchar(40) NOT NULL,
  `JoueurNom` varchar(32) NOT NULL,
  `JoueurAdmin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`JoueurID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `Joueur`
--

INSERT INTO `Joueur` VALUES(1, '6dcd4ce23d88e2ee9568ba546c007c63d9131c1b', 'A', 1);
INSERT INTO `Joueur` VALUES(2, 'ae4f281df5a5d0ff3cad6371f76d5c29b6d953ec', 'B', 0);

-- --------------------------------------------------------

--
-- Structure de la table `Message`
--

CREATE TABLE `Message` (
  `MessageID` mediumint(6) NOT NULL AUTO_INCREMENT,
  `MessagePartie` smallint(3) NOT NULL,
  `MessageDestinataire` smallint(3) NOT NULL,
  `MessageExclus` varchar(20) NOT NULL,
  `MessageTitre` varchar(32) NOT NULL,
  `MessageTexte` text NOT NULL,
  `MessageSource` smallint(3) NOT NULL,
  `MessageTime` int(10) NOT NULL,
  `MessageTour` smallint(3) NOT NULL,
  `MessageLu` tinyint(1) NOT NULL DEFAULT '0',
  `MessageCouleur` varchar(10) NOT NULL DEFAULT 'noire',
  `MessageDuree` smallint(3) NOT NULL DEFAULT '10',
  PRIMARY KEY (`MessageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `Message`
--


-- --------------------------------------------------------

--
-- Structure de la table `MessageLu`
--

CREATE TABLE `MessageLu` (
  `MessageLuID` mediumint(6) NOT NULL,
  `MessageLuJoueur` smallint(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Comme certains messages sont publics, on doit spécifier qui ';

--
-- Contenu de la table `MessageLu`
--


-- --------------------------------------------------------

--
-- Structure de la table `Partie`
--

CREATE TABLE `Partie` (
  `PartieID` smallint(2) NOT NULL AUTO_INCREMENT,
  `PartieNom` varchar(30) NOT NULL,
  `PartieStatut` tinyint(2) NOT NULL DEFAULT '-1',
  `PartieBataille` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`PartieID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Données sur les Parties' AUTO_INCREMENT=2 ;

--
-- Contenu de la table `Partie`
--

INSERT INTO `Partie` VALUES(1, 'A', 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `Region`
--

CREATE TABLE `Region` (
  `RegionID` smallint(5) NOT NULL AUTO_INCREMENT,
  `RegionTerritoire` smallint(5) NOT NULL,
  `RegionPartie` smallint(5) NOT NULL,
  `RegionCoordonneeX` smallint(3) NOT NULL,
  `RegionCoordonneeY` smallint(3) NOT NULL,
  `RegionTerrain` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`RegionID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Une région est un segment de la carte. Une ou plusieurs régi' AUTO_INCREMENT=151 ;

--
-- Contenu de la table `Region`
--

INSERT INTO `Region` VALUES(1, 1, 1, 1, 1, 1);
INSERT INTO `Region` VALUES(2, 1, 1, 1, 2, 1);
INSERT INTO `Region` VALUES(3, 3, 1, 1, 3, 1);
INSERT INTO `Region` VALUES(4, 3, 1, 1, 4, 1);
INSERT INTO `Region` VALUES(5, 5, 1, 1, 5, 1);
INSERT INTO `Region` VALUES(6, 5, 1, 1, 6, 1);
INSERT INTO `Region` VALUES(7, 5, 1, 1, 7, 1);
INSERT INTO `Region` VALUES(8, 8, 1, 1, 8, 1);
INSERT INTO `Region` VALUES(9, 8, 1, 1, 9, 1);
INSERT INTO `Region` VALUES(10, 8, 1, 1, 10, 1);
INSERT INTO `Region` VALUES(11, 11, 1, 2, 1, 1);
INSERT INTO `Region` VALUES(12, 1, 1, 2, 2, 1);
INSERT INTO `Region` VALUES(13, 11, 1, 2, 3, 1);
INSERT INTO `Region` VALUES(14, 3, 1, 2, 4, 1);
INSERT INTO `Region` VALUES(15, 15, 1, 2, 5, 1);
INSERT INTO `Region` VALUES(16, 5, 1, 2, 6, 1);
INSERT INTO `Region` VALUES(17, 5, 1, 2, 7, 1);
INSERT INTO `Region` VALUES(18, 8, 1, 2, 8, 1);
INSERT INTO `Region` VALUES(19, 8, 1, 2, 9, 1);
INSERT INTO `Region` VALUES(20, 8, 1, 2, 10, 1);
INSERT INTO `Region` VALUES(21, 11, 1, 3, 1, 1);
INSERT INTO `Region` VALUES(22, 11, 1, 3, 2, 1);
INSERT INTO `Region` VALUES(23, 11, 1, 3, 3, 1);
INSERT INTO `Region` VALUES(24, 3, 1, 3, 4, 1);
INSERT INTO `Region` VALUES(25, 15, 1, 3, 5, 1);
INSERT INTO `Region` VALUES(26, 26, 1, 3, 6, 1);
INSERT INTO `Region` VALUES(27, 26, 1, 3, 7, 1);
INSERT INTO `Region` VALUES(28, 8, 1, 3, 8, 1);
INSERT INTO `Region` VALUES(29, 8, 1, 3, 9, 1);
INSERT INTO `Region` VALUES(30, 8, 1, 3, 10, 1);
INSERT INTO `Region` VALUES(31, 11, 1, 4, 1, 1);
INSERT INTO `Region` VALUES(32, 11, 1, 4, 2, 1);
INSERT INTO `Region` VALUES(33, 11, 1, 4, 3, 1);
INSERT INTO `Region` VALUES(34, 34, 1, 4, 4, 1);
INSERT INTO `Region` VALUES(35, 15, 1, 4, 5, 1);
INSERT INTO `Region` VALUES(36, 26, 1, 4, 6, 1);
INSERT INTO `Region` VALUES(37, 26, 1, 4, 7, 1);
INSERT INTO `Region` VALUES(38, 38, 1, 4, 8, 1);
INSERT INTO `Region` VALUES(39, 38, 1, 4, 9, 1);
INSERT INTO `Region` VALUES(40, 38, 1, 4, 10, 1);
INSERT INTO `Region` VALUES(41, 41, 1, 5, 1, 1);
INSERT INTO `Region` VALUES(42, 42, 1, 5, 2, 1);
INSERT INTO `Region` VALUES(43, 42, 1, 5, 3, 1);
INSERT INTO `Region` VALUES(44, 34, 1, 5, 4, 1);
INSERT INTO `Region` VALUES(45, 157, 1, 5, 5, 0);
INSERT INTO `Region` VALUES(46, 158, 1, 5, 6, 0);
INSERT INTO `Region` VALUES(47, 159, 1, 5, 7, 0);
INSERT INTO `Region` VALUES(48, 48, 1, 5, 8, 1);
INSERT INTO `Region` VALUES(49, 48, 1, 5, 9, 1);
INSERT INTO `Region` VALUES(50, 38, 1, 5, 10, 1);
INSERT INTO `Region` VALUES(51, 41, 1, 6, 1, 1);
INSERT INTO `Region` VALUES(52, 42, 1, 6, 2, 1);
INSERT INTO `Region` VALUES(53, 42, 1, 6, 3, 1);
INSERT INTO `Region` VALUES(54, 151, 1, 6, 4, 0);
INSERT INTO `Region` VALUES(55, 152, 1, 6, 5, 0);
INSERT INTO `Region` VALUES(56, 153, 1, 6, 6, 0);
INSERT INTO `Region` VALUES(57, 48, 1, 6, 7, 1);
INSERT INTO `Region` VALUES(58, 48, 1, 6, 8, 1);
INSERT INTO `Region` VALUES(59, 38, 1, 6, 9, 1);
INSERT INTO `Region` VALUES(60, 38, 1, 6, 10, 1);
INSERT INTO `Region` VALUES(61, 41, 1, 7, 1, 1);
INSERT INTO `Region` VALUES(62, 42, 1, 7, 2, 1);
INSERT INTO `Region` VALUES(63, 42, 1, 7, 3, 1);
INSERT INTO `Region` VALUES(64, 42, 1, 7, 4, 1);
INSERT INTO `Region` VALUES(65, 155, 1, 7, 5, 0);
INSERT INTO `Region` VALUES(66, 154, 1, 7, 6, 0);
INSERT INTO `Region` VALUES(67, 48, 1, 7, 7, 1);
INSERT INTO `Region` VALUES(68, 48, 1, 7, 8, 1);
INSERT INTO `Region` VALUES(69, 69, 1, 7, 9, 1);
INSERT INTO `Region` VALUES(70, 69, 1, 7, 10, 1);
INSERT INTO `Region` VALUES(71, 41, 1, 8, 1, 1);
INSERT INTO `Region` VALUES(72, 41, 1, 8, 2, 1);
INSERT INTO `Region` VALUES(73, 73, 1, 8, 3, 1);
INSERT INTO `Region` VALUES(74, 73, 1, 8, 4, 1);
INSERT INTO `Region` VALUES(75, 73, 1, 8, 5, 1);
INSERT INTO `Region` VALUES(76, 156, 1, 8, 6, 0);
INSERT INTO `Region` VALUES(77, 77, 1, 8, 7, 1);
INSERT INTO `Region` VALUES(78, 77, 1, 8, 8, 1);
INSERT INTO `Region` VALUES(79, 69, 1, 8, 9, 1);
INSERT INTO `Region` VALUES(80, 69, 1, 8, 10, 1);
INSERT INTO `Region` VALUES(81, 81, 1, 9, 1, 1);
INSERT INTO `Region` VALUES(82, 81, 1, 9, 2, 1);
INSERT INTO `Region` VALUES(83, 73, 1, 9, 3, 1);
INSERT INTO `Region` VALUES(84, 73, 1, 9, 4, 1);
INSERT INTO `Region` VALUES(85, 73, 1, 9, 5, 1);
INSERT INTO `Region` VALUES(86, 77, 1, 9, 6, 1);
INSERT INTO `Region` VALUES(87, 77, 1, 9, 7, 1);
INSERT INTO `Region` VALUES(88, 88, 1, 9, 8, 1);
INSERT INTO `Region` VALUES(89, 88, 1, 9, 9, 1);
INSERT INTO `Region` VALUES(90, 88, 1, 9, 10, 1);
INSERT INTO `Region` VALUES(91, 81, 1, 10, 1, 1);
INSERT INTO `Region` VALUES(92, 81, 1, 10, 2, 1);
INSERT INTO `Region` VALUES(93, 93, 1, 10, 3, 1);
INSERT INTO `Region` VALUES(94, 93, 1, 10, 4, 1);
INSERT INTO `Region` VALUES(95, 77, 1, 10, 5, 1);
INSERT INTO `Region` VALUES(96, 77, 1, 10, 6, 1);
INSERT INTO `Region` VALUES(97, 88, 1, 10, 7, 1);
INSERT INTO `Region` VALUES(98, 88, 1, 10, 8, 1);
INSERT INTO `Region` VALUES(99, 88, 1, 10, 9, 1);
INSERT INTO `Region` VALUES(100, 88, 1, 10, 10, 1);
INSERT INTO `Region` VALUES(101, 81, 1, 11, 1, 1);
INSERT INTO `Region` VALUES(102, 81, 1, 11, 2, 1);
INSERT INTO `Region` VALUES(103, 93, 1, 11, 3, 1);
INSERT INTO `Region` VALUES(104, 93, 1, 11, 4, 1);
INSERT INTO `Region` VALUES(105, 77, 1, 11, 5, 1);
INSERT INTO `Region` VALUES(106, 77, 1, 11, 6, 1);
INSERT INTO `Region` VALUES(107, 88, 1, 11, 7, 1);
INSERT INTO `Region` VALUES(108, 88, 1, 11, 8, 1);
INSERT INTO `Region` VALUES(109, 88, 1, 11, 9, 1);
INSERT INTO `Region` VALUES(110, 88, 1, 11, 10, 1);
INSERT INTO `Region` VALUES(111, 111, 1, 12, 1, 1);
INSERT INTO `Region` VALUES(112, 93, 1, 12, 2, 1);
INSERT INTO `Region` VALUES(113, 93, 1, 12, 3, 1);
INSERT INTO `Region` VALUES(114, 114, 1, 12, 4, 1);
INSERT INTO `Region` VALUES(115, 114, 1, 12, 5, 1);
INSERT INTO `Region` VALUES(116, 114, 1, 12, 6, 1);
INSERT INTO `Region` VALUES(117, 114, 1, 12, 7, 1);
INSERT INTO `Region` VALUES(118, 114, 1, 12, 8, 1);
INSERT INTO `Region` VALUES(119, 119, 1, 12, 9, 1);
INSERT INTO `Region` VALUES(120, 120, 1, 12, 10, 1);
INSERT INTO `Region` VALUES(121, 111, 1, 13, 1, 1);
INSERT INTO `Region` VALUES(122, 162, 1, 13, 2, 0);
INSERT INTO `Region` VALUES(123, 161, 1, 13, 3, 0);
INSERT INTO `Region` VALUES(124, 114, 1, 13, 4, 1);
INSERT INTO `Region` VALUES(125, 114, 1, 13, 5, 1);
INSERT INTO `Region` VALUES(126, 114, 1, 13, 6, 1);
INSERT INTO `Region` VALUES(127, 127, 1, 13, 7, 1);
INSERT INTO `Region` VALUES(128, 127, 1, 13, 8, 1);
INSERT INTO `Region` VALUES(129, 119, 1, 13, 9, 1);
INSERT INTO `Region` VALUES(130, 120, 1, 13, 10, 1);
INSERT INTO `Region` VALUES(131, 111, 1, 14, 1, 1);
INSERT INTO `Region` VALUES(132, 132, 1, 14, 2, 1);
INSERT INTO `Region` VALUES(133, 132, 1, 14, 3, 1);
INSERT INTO `Region` VALUES(134, 132, 1, 14, 4, 1);
INSERT INTO `Region` VALUES(135, 135, 1, 14, 5, 1);
INSERT INTO `Region` VALUES(136, 135, 1, 14, 6, 1);
INSERT INTO `Region` VALUES(137, 127, 1, 14, 7, 1);
INSERT INTO `Region` VALUES(138, 127, 1, 14, 8, 1);
INSERT INTO `Region` VALUES(139, 160, 1, 14, 9, 0);
INSERT INTO `Region` VALUES(140, 120, 1, 14, 10, 1);
INSERT INTO `Region` VALUES(141, 111, 1, 15, 1, 1);
INSERT INTO `Region` VALUES(142, 132, 1, 15, 2, 1);
INSERT INTO `Region` VALUES(143, 132, 1, 15, 3, 1);
INSERT INTO `Region` VALUES(144, 132, 1, 15, 4, 1);
INSERT INTO `Region` VALUES(145, 135, 1, 15, 5, 1);
INSERT INTO `Region` VALUES(146, 135, 1, 15, 6, 1);
INSERT INTO `Region` VALUES(147, 135, 1, 15, 7, 1);
INSERT INTO `Region` VALUES(148, 148, 1, 15, 8, 1);
INSERT INTO `Region` VALUES(149, 148, 1, 15, 9, 1);
INSERT INTO `Region` VALUES(150, 148, 1, 15, 10, 1);

-- --------------------------------------------------------

--
-- Structure de la table `Territoire`
--

CREATE TABLE `Territoire` (
  `TerritoireID` smallint(5) NOT NULL AUTO_INCREMENT,
  `TerritoireEtat` smallint(3) NOT NULL,
  `TerritoirePartie` smallint(3) NOT NULL,
  `TerritoireJoueur` smallint(3) NOT NULL,
  `TerritoireNom` varchar(55) NOT NULL,
  `TerritoirePopulation` mediumint(7) NOT NULL DEFAULT '500',
  `TerritoireCroissance` float NOT NULL DEFAULT '4',
  `TerritoireTerrain` tinyint(1) NOT NULL DEFAULT '1',
  `TerritoireDefense` smallint(3) NOT NULL DEFAULT '5',
  PRIMARY KEY (`TerritoireID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=163 ;

--
-- Contenu de la table `Territoire`
--

INSERT INTO `Territoire` VALUES(1, 0, 1, 0, 'En haut A gauche', 501, 2, 1, 5);
INSERT INTO `Territoire` VALUES(3, 0, 1, 0, '4HG', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(153, 0, 1, 0, '153', 500, 4, 0, 5);
INSERT INTO `Territoire` VALUES(5, 0, 1, 0, '5', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(8, 0, 1, 0, '8', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(11, 0, 1, 0, 'GrandeGauche', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(152, 0, 1, 0, '152', 500, 4, 0, 5);
INSERT INTO `Territoire` VALUES(15, 0, 1, 0, '15', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(159, 0, 1, 0, '159', 500, 4, 0, 5);
INSERT INTO `Territoire` VALUES(158, 0, 1, 0, '158', 500, 4, 0, 5);
INSERT INTO `Territoire` VALUES(157, 0, 1, 0, '157', 500, 4, 0, 5);
INSERT INTO `Territoire` VALUES(151, 0, 1, 0, '151', 500, 4, 0, 5);
INSERT INTO `Territoire` VALUES(26, 1, 1, 1, '26', 3980, 0.15, 1, 5);
INSERT INTO `Territoire` VALUES(156, 0, 1, 0, '156', 500, 4, 0, 5);
INSERT INTO `Territoire` VALUES(155, 0, 1, 0, '155', 500, 4, 0, 5);
INSERT INTO `Territoire` VALUES(154, 0, 1, 0, 'Mer154', 500, 4, 0, 5);
INSERT INTO `Territoire` VALUES(34, 0, 1, 0, '34', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(38, 0, 1, 0, '38', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(41, 0, 1, 0, '41', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(42, 0, 1, 0, '42', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(48, 0, 1, 0, '48', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(69, 0, 1, 0, '69', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(73, 0, 1, 0, '73', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(77, 2, 1, 2, '77', 4028, -0.2, 1, 5);
INSERT INTO `Territoire` VALUES(81, 0, 1, 0, '81', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(88, 0, 1, 0, 'Mastodonte', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(93, 0, 1, 0, '93', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(111, 0, 1, 0, '111', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(114, 2, 1, 2, '114', 4028, -0.2, 1, 5);
INSERT INTO `Territoire` VALUES(119, 0, 1, 0, '119', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(120, 0, 1, 0, '120', 500, 4, 1, 5);
INSERT INTO `Territoire` VALUES(127, 1, 1, 1, '127', 3980, 0.15, 1, 5);
INSERT INTO `Territoire` VALUES(162, 0, 1, 0, '162', 500, 4, 0, 5);
INSERT INTO `Territoire` VALUES(132, 0, 1, 0, '132', 500, 4, 1, 75);
INSERT INTO `Territoire` VALUES(135, 0, 1, 0, '135', 500, 4, 1, 35);
INSERT INTO `Territoire` VALUES(160, 0, 1, 0, '139', 500, 4, 0, 5);
INSERT INTO `Territoire` VALUES(161, 0, 1, 0, '161', 500, 4, 0, 5);
INSERT INTO `Territoire` VALUES(148, 0, 1, 0, '148', 500, 4, 1, 5);
