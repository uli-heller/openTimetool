-- phpMyAdmin SQL Dump
-- version 2.11.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 21. Oktober 2008 um 09:59
-- Server Version: 5.0.45
-- PHP-Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `opentimetool`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `translate_de`
--

CREATE TABLE IF NOT EXISTS `translate_de` (
  `id` int(11) NOT NULL auto_increment,
  `string` mediumtext NOT NULL,
  `convertHtml` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=294 ;

--
-- Daten für Tabelle `translate_de`
--

INSERT INTO `translate_de` (`id`, `string`, `convertHtml`) VALUES
(55, 'g&uuml;ltig ab', '0'),
(46, 'g&uuml;ltig', '0'),
(188, 'Admin Modus aktiviert!', '0'),
(42, 'Benutzer', '0'),
(40, 'Benutzer', '0'),
(99, 'Benutzer:', '0'),
(38, 'Benutzer', '0'),
(184, 'Hier k&ouml;nnen Sie Buchungen f&uuml;r einen l&auml;ngeren Zeitraum durchf&uuml;hren (z.B. 2 Wochen Urlaub, usw.).', '0'),
(128, 'Hier k&ouml;nnen Sie mit Unterst&uuml;tzung der $1 Funktion (Eingabehilfe) mehrere Zeiten erfassen.', '0'),
(223, 'Vorlage verwenden (Hochladen)', '0'),
(215, 'Seite neu laden', '0'),
(6, 'Update', ''),
(45, 'bis', '0'),
(123, 'heute ist', '0'),
(198, 'Typ', '0'),
(65, 'unter', '0'),
(201, 'nach PDF', '0'),
(15, 'heute', '0'),
(101, 'Zeit&uuml;bersicht und Filter', '0'),
(173, 'Zeit(en) gespeichert.', '0'),
(47, 'bis', '0'),
(39, 'Zeit', '0'),
(162, 'Diese Projekt ist an diesem Datum nicht mehr verf&uuml;gbar oder nur noch mit Adminberechtigungen zu ver&auml;ndern!', '0'),
(238, 'Dieses Login ist schon vergeben!', '0'),
(207, 'Das ist kein g&uuml;ltiger Zugangsname!', '0'),
(236, 'Die Passw&ouml;rter stimmen nicht &uuml;berein!', '0'),
(170, 'Es wurden schon $1 Zeiten ausserhalb des neuen Projekt-G&uuml;ltigkeitszeitraumes gebucht, der neue Zeitraum muss diese Buchungen beinhalten!', '0'),
(160, 'Das Projekt ist nicht mehr verf&uuml;gbar, der Eintrag kann nicht gel&ouml;scht werden!', '0'),
(85, 'Die Anzahl Arbeitstage nach denen $1 die Zeiten nicht mehr editierbar sind.', '0'),
(61, 'Dankesch&ouml;n', '0'),
(197, 'Vorlage', '0'),
(135, 'T&auml;tigkeiten', '0'),
(94, 'T&auml;tigkeiten', '0'),
(43, 'T&auml;tigkeit', '0'),
(3, 'T&auml;tigkeit', '0'),
(129, 'Nachname', '0'),
(72, 'gesamt', '0'),
(16, 'Summe', '0'),
(175, 'Start-T&auml;tigkeit', '0'),
(87, 'Extras', '0'),
(49, 'Beginn', '0'),
(83, 'Extras', '0'),
(194, 'Quelle', '0'),
(157, 'Sorry, diese T&auml;tigkeit wurde schon $1 mal benutzt und kann nicht gel&ouml;scht werden!', '0'),
(152, 'Sorry, der von Ihnen gew&auml;hlte Eintrag wurde in der Zwischenzeit gel&ouml;scht!', '0'),
(216, 'Alle Spalten anzeigen', '0'),
(53, 'Anzeigen', '0'),
(5, 'Als neuen speichern', ''),
(4, 'Speichern', '0'),
(80, 'Runden', '0'),
(76, 'Runden', '0'),
(244, 'Zur&uuml;cksetzen', '0'),
(251, 'Passwort-Wiederholung', ''),
(35, 'Projekte', '0'),
(86, 'Projekte', '0'),
(127, 'aktualisieren', '0'),
(196, 'Projekt(e)', '0'),
(32, 'Gewinn:', '0'),
(2, 'Projekt', '0'),
(144, 'Projekt - Team', '0'),
(37, 'Preise', '0'),
(41, 'Preise', '0'),
(192, 'Drucken', '0'),
(54, 'Druckansicht', '0'),
(36, 'Preis', '0'),
(204, 'Bitte geben Sie ein g&uuml;ltiges Format f&uuml;r Zeit und Datum ein!', '0'),
(210, 'Bitte geben Sie ihren Zugangsnamen ein!', '0'),
(240, 'Bitte den kompletten Namen eingeben!', '0'),
(237, 'Bitte ein g&uuml;ltiges Login eingeben!', '0'),
(163, 'Bitte geben Sie eine g&uuml;ltige E-Mail Adresse an!', '0'),
(241, 'Bitte Passwort eingeben!', '0'),
(239, 'Zum &Auml;ndern des Logins wird das Passwort ben&ouml;tigt!', '0'),
(154, 'Bitte geben Sie ein Start- und Enddatum an!', '0'),
(155, 'Bitte korrigieren Sie die Daten!', '0'),
(242, 'Passwort', '0'),
(66, 'Vorg&auml;nger', '0'),
(18, '&Uuml;bersicht', '0'),
(252, '&Ouml;ffnen', '0'),
(84, 'Braucht nur angegeben werden, wenn dieses Projekt$1einen Festpreis hat.', '0'),
(193, 'weiter &gt;&gt;', '0'),
(25, 'nein', '0'),
(19, 'jetzt!', '0'),
(224, 'Neue Vorlage (Hochladen und Speichern)', '0'),
(126, 'Neujahr', '0'),
(89, 'neuer Name', '0'),
(21, 'ben&ouml;tigt Projekt?', '0'),
(143, 'Name', '0'),
(79, 'Projekt verschieben', '0'),
(63, 'Projekt verschieben', '0'),
(64, 'Verschieben', '0'),
(81, 'Minuten', '0'),
(202, 'Meldungen', '0'),
(146, 'Mitarbeiter', '0'),
(182, 'Login', '0'),
(17, 'Abmelden', '0'),
(56, 'erstellen', '0'),
(145, 'Projektleiter', '0'),
(180, 'Handbuch', '0'),
(67, 'Erfassung', '0'),
(189, 'Als Benutzer angemeldet', '0'),
(181, 'Lizenziert f&uuml;r: $1 Benutzer', '0'),
(227, 'Zeiten erfassen für das Projekt', '0'),
(31, 'jetzt loggen', '0'),
(183, 'ist Admin', '0'),
(91, 'Sprachen', '0'),
(199, 'Zuletzt exportiert', '0'),
(60, 'Leerlassen falls der Preis immer g&uuml;ltig ist', '0'),
(33, 'intern', '0'),
(69, 'Impressum', '0'),
(23, 'HTML-Farbe', '0'),
(68, 'Feiertag', '0'),
(179, 'Hilfe', '0'),
(30, 'Hallo $1!', '0'),
(122, 'zum aktuellen Monat', '0'),
(44, 'von', '0'),
(228, 'für:', '0'),
(9, 'Vorname', '0'),
(48, 'Festpreis', '0'),
(34, 'extern', '0'),
(222, 'Datei', '0'),
(102, 'Filter', '0'),
(200, 'Export', '0'),
(225, 'Exportierte Daten', '0'),
(185, 'erweiterter Filter', '0'),
(186, 'erweiterter Filter AUS', '0'),
(168, 'Fehler beim Speichern in das Export-Verzeichnis (''$1'')!', '0'),
(153, 'Fehler beim Speichern der Zeit f&uuml;r den $1!', '0'),
(167, 'Fehler beim Empfangen des Templates, bitte versuchen Sie es erneut!', '0'),
(130, 'Email', '0'),
(50, 'Ende', '0'),
(176, 'End-T&auml;tigkeit', '0'),
(213, 'eingeben', '0'),
(57, '&Auml;ndern/Hinzuf&uuml;gen eines Preises', '0'),
(95, 'T&auml;tigkeit &auml;ndern', '0'),
(203, 'Benutzer &auml;ndern', '0'),
(88, 'Projekt &auml;ndern', '0'),
(132, 'Preis &auml;ndern', '0'),
(220, 'Eintrag &auml;ndern', '0'),
(7, '&Auml;ndern', '0'),
(11, 'Dauer', '0'),
(195, 'Zielformat', '0'),
(59, 'Beschreibung', '0'),
(51, 'l&ouml;schen', '0'),
(149, 'deaktivieren', '0'),
(82, 'Tage', '0'),
(151, 'Daten gespeichert.', '0'),
(10, 'Datum', '0'),
(13, 'aktuelle T&auml;tigkeit:', '0'),
(1, 'Kunde', '0'),
(187, 'aktuelles Datum:', '0'),
(12, 'aktuelles Projekt:', '0'),
(166, 'Es konnte nicht in das OpenOffice-Template Verzeichnis geschrieben werden!', '0'),
(156, 'Verzeichnis ''$1'' konnte nicht erstellt werden!', '0'),
(58, 'Kommentar', '0'),
(8, 'Kommentar', '0'),
(29, 'Farbe', '0'),
(77, 'Schliessen', '0'),
(125, 'Weihnachten', '0'),
(221, 'wechseln', '0'),
(90, 'Abbrechen', '0'),
(22, 'Zeit berechnen?', '0'),
(71, 'nach Datum', '0'),
(208, 'Authentifizierungs-Modul konnte nicht geladen werden - bitte verst&auml;ndigen Sie ihren Anbieter!', '0'),
(253, 'Auth-Modus:', '0'),
(229, 'Sind Sie sicher?', '0'),
(178, 'Sind Sie sicher, dass Sie diesen Benutzer löschen wollen?', '0'),
(177, 'Sind Sie sicher, dass Sie diesen Eintrag löschen wollen?', '0'),
(20, 'alle', '0'),
(219, 'Der Admin Modus wurde ausgeschaltet, Sie arbeiten nun wieder wie ein Standard-Benutzer!', '0'),
(190, 'Admin Modus EIN', '0'),
(217, 'Admin Modus AUS', '0'),
(100, 'Administrator', '0'),
(150, 'Admin Modus', '0'),
(133, 'Benutzer hinzuf&uuml;gen', '0'),
(96, 'T&auml;tigkeit hinzuf&uuml;gen', '0'),
(62, 'Projekt hinzuf&uuml;gen', '0'),
(78, 'Projekt hinzuf&uuml;gen', '0'),
(131, 'Preis hinzuf&uuml;gen', '0'),
(211, 'Zugangsname', '0'),
(212, 'Zugangsname', '0'),
(231, 'Zugangsname:', '0'),
(148, 'aktivieren', '0'),
(14, '- seit', '0'),
(165, 'Es wurde schon ein Preis f&uuml;r diesen Eintrag gespeichert!', '0'),
(174, '$1 Datens&auml;tze gespeichert.', '0'),
(92, '(tt.mm.jjjj)', '0'),
(158, '$1 $2 ist kein Team-Mitglied im Projekt ''$3''!', '0'),
(169, '$1 $2 hat schon Zeiten auf dieses Projekt gebucht, der Benutzer kann nicht gel&ouml;scht werden!', '0'),
(27, '''ja'' wenn f&uuml;r diese T&auml;tigkeit die Zeit berechnet werden soll', '0'),
(26, '''nein'' wenn diese T&auml;tigkeit keinem Projekt zugeordnet werden braucht', '0'),
(124, 'KW', '0'),
(24, 'ja', '0'),
(214, 'Sie sind kein Team-Mitglied eines Projektes, bitte verst&auml;ndigen Sie ihren Administrator!', '0'),
(159, 'Sie sind kein Team-Mitglied im Projekt: ''$1''!', '0'),
(235, 'Sie haben keine Berechtigung Benutzer zu &auml;ndern!', '0'),
(161, 'Dieser Benutzer kann nicht gel&ouml;scht werden, es sind schon Zeiten f&uuml;r ihn/sie gebucht!', '0'),
(164, 'Sie k&ouml;nnen nicht in den Administrator-Modus schalten!', '0'),
(232, 'Sie haben keine Berechtigung diesen Eintrag zu &auml;ndern!', '0'),
(234, 'Sie haben keine Berechtigung diesen Eintrag zu l&ouml;schen!', '0'),
(218, 'Sie haben den Admin Modus eingeschaltet!', '0'),
(230, 'Zugang g&uuml;ltig bis:', '0'),
(209, 'Ihr Zugang ist nicht aktiv - bitte verst&auml;ndigen Sie ihren Anbieter!', '0'),
(206, 'Ihre Lizenz erlaubt nur $1 Benutzer!', '0'),
(205, 'Die Passw&ouml;rter stimmen nicht &uuml;berein!', '0'),
(172, 'Ihr Start-Datum lag nach dem End-Datum, sie wurden getauscht.', '0'),
(171, 'Ihre Start-Zeit lag nach der End-Zeit, sie wurden getauscht.', '0'),
(226, '* Wenn Sie die OpenOffice.org-Suite nicht auf Ihrem System installiert haben,$1k&ouml;nnen Sie diese kostenlos unter $2www.openoffice.org$3 herunterladen.', '0'),
(255, 'l&ouml;schen', '1'),
(257, 'K&uuml;rzel', '1'),
(258, 'Alle Benutzer', '1'),
(259, 'Projektleiter', '1'),
(260, 'Teammitglieder', '1'),
(262, 'Hallo $1,\r\n\r\n$2 hat Sie als User f&uuml;r openTimetool angemeldet.\r\nIhre Zugangsdaten sind wie folgt:\r\n\r\nUsername:\\\\t$3\r\nPasswort:\\\\t$4\r\n\\(Bitte &auml;ndern Sie Ihr Passwort sofort!\\)\r\n\r\nHier k?nnen Sie sich einloggen\r\n\\\\t$5\r\n\r\nMit freundlichen Gr??en\r\n$6', '1'),
(263, 'Ihre timetool Registration', '1'),
(270, 'Fehler beim Versenden der E-Mail an ''$1''!', '1'),
(265, 'Infomail wurde an ''$1'' gesendet.', '1'),
(264, 'Die Zeit wurde gerundet von $1 auf $2 ($3 Minuten Rundung).', '1'),
(271, 'Sie haben keine E-Mail angegeben an die die Infomail geschickt werden soll!', '1'),
(273, 'nach Projekt', '1'),
(272, 'nach Woche', '1'),
(276, 'Wochen&uuml;bersicht', '1'),
(275, 'Legende (T&auml;tigkeiten)', '1'),
(274, 'Wochen&uuml;bersicht vom $1 bis $2', '1'),
(277, 'Aufwand', '1'),
(279, 'Aufwand in %', '1'),
(278, 'max. Aufwand', '1'),
(280, 'Projekt&uuml;bersicht', '1'),
(281, 'Stunden', '1'),
(282, 'Eintrag &Auml;ndern/Hinzuf&uuml;gen', '1'),
(283, 'Sie sind kein Projektmanager eines Projekts', '0'),
(284, 'Nichts selektiert zum exportieren', '0'),
(285, 'Wenn die nachstehende Option ausgew&auml;hlt ist, erh&auml;lt der Benutzer ein neues Zufallspasswort und wird per Mail dar&uuml;ber informiert.', '0'),
(286, 'Password r&uuml;cksetzen', '0'),
(287, 'Team aus ''$1'' &uuml;bernehmen', '0'),
(290, 'Benutzer', '0'),
(291, 'Passwort', '0'),
(292, '&auml;ndern', '0'),
(293, 'Datum, Uhrzeit', '0');
