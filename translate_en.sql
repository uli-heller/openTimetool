-- phpMyAdmin SQL Dump
-- version 2.11.9.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 02, 2009 at 02:27 PM
-- Server version: 5.0.45
-- PHP Version: 5.2.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `opentimetool`
--

-- --------------------------------------------------------

--
-- Table structure for table `translate_en`
--

CREATE TABLE IF NOT EXISTS `translate_en` (
  `id` int(11) NOT NULL auto_increment,
  `string` mediumtext NOT NULL,
  `numSubPattern` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=295 ;

--
-- Dumping data for table `translate_en`
--

INSERT INTO `translate_en` (`id`, `string`, `numSubPattern`) VALUES
(1, 'Customer', 0),
(2, 'Project', 0),
(3, 'Task', 0),
(4, 'save', 0),
(5, 'save as new', 0),
(6, 'update', 0),
(7, 'edit', 0),
(8, 'Comment', 0),
(9, 'first name', 0),
(10, 'date', 0),
(11, 'Duration', 0),
(12, 'current project:', 0),
(13, 'current task:', 0),
(14, '- since', 0),
(15, 'today', 0),
(16, 'Sum', 0),
(17, 'Logout', 0),
(18, 'Overview', 0),
(19, 'now!', 0),
(20, 'all ', 0),
(21, 'needs project?', 0),
(22, 'calculate time?', 0),
(23, 'HTML-color', 0),
(24, 'yes', 0),
(25, 'no', 0),
(26, '''no'' if this task doesnt require a specific project', 0),
(27, '''yes'' if the time for this task shall be calculated', 0),
(29, 'color', 0),
(30, 'Hello (.*)!', 1),
(31, 'log now', 0),
(32, 'Profit:', 0),
(33, 'internal', 0),
(34, 'external', 0),
(35, 'Projects', 0),
(36, 'price', 0),
(37, 'prices', 0),
(38, 'user', 0),
(39, 'time', 0),
(40, 'users', 0),
(41, 'Prices', 0),
(42, 'Users', 0),
(43, 'task', 0),
(44, 'from', 0),
(45, 'until', 0),
(46, 'valid', 0),
(47, 'through', 0),
(48, 'fixed price', 0),
(49, 'start', 0),
(50, 'end', 0),
(51, 'delete', 0),
(214, 'You are not a team member of any project, please contact your admin!', 0),
(53, 'show', 0),
(54, 'print view', 0),
(55, 'valid from', 0),
(56, 'make', 0),
(57, 'Edit/Add Price', 0),
(58, 'comment', 0),
(59, 'Description', 0),
(60, 'leave empty if the price is always valid', 0),
(61, 'Thanks', 0),
(62, 'Add Project', 0),
(63, 'Move Project', 0),
(64, 'Move', 0),
(65, 'under', 0),
(66, 'Parent', 0),
(67, 'Logging', 0),
(68, 'Holiday', 0),
(69, 'Imprint', 0),
(72, 'Summary', 0),
(71, 'by date', 0),
(193, 'next &gt;&gt;', 0),
(192, 'print', 0),
(76, 'round', 0),
(77, 'close', 0),
(78, 'add project', 0),
(79, 'move project', 0),
(80, 'rounding', 0),
(81, 'minutes', 0),
(82, 'days', 0),
(83, 'specials', 0),
(84, 'Only needs to be given if this project has a fixed price,(.*)if not leave empty.\r\n', 1),
(85, 'The number of work days after which the old months data(.*)are still editable.', 1),
(86, 'projects', 0),
(87, 'Specials', 0),
(88, 'edit project', 0),
(89, 'new name', 0),
(90, 'Cancel', 0),
(91, 'Languages', 0),
(92, '(dd.mm.yyyy)', 0),
(94, 'tasks', 0),
(95, 'edit task', 0),
(96, 'add task', 0),
(100, 'Admin', 0),
(101, 'time overview and filter', 0),
(99, 'User:', 0),
(102, 'filter', 0),
(188, 'using Admin mode!', 0),
(187, 'current date:', 0),
(186, 'extended filter OFF', 0),
(185, 'extended filter', 0),
(184, 'Use this page to log times for a longer period (i.e. 2 weeks for your holidays, etc.).', 0),
(122, 'go to current month', 0),
(123, 'today is', 0),
(124, 'Wk', 0),
(125, 'Christmas', 0),
(126, 'New Years Eve', 0),
(127, 'refresh', 0),
(128, 'Use this page to log multiple times. It uses the (.*) functionality for helping you to fill in the fields.', 1),
(129, 'surname', 0),
(130, 'email', 0),
(131, 'add price', 0),
(132, 'edit price', 0),
(133, 'add user', 0),
(135, 'Tasks', 0),
(202, 'Messages', 0),
(180, 'Manual', 0),
(183, 'is admin', 0),
(182, 'login', 0),
(181, 'licensed for: (.{0,50}) user', 1),
(143, 'name', 0),
(144, 'project - team members', 0),
(145, 'manager(s)', 0),
(146, 'member(s)', 0),
(190, 'Admin mode - ON', 0),
(148, 'activate', 0),
(149, 'deactivate', 0),
(150, 'Admin mode', 0),
(151, 'Data successfully saved.', 0),
(152, 'Sorry, but the Entry you chose was removed meanwhile. Log failed!', 0),
(153, 'Error saving time for the (.*)!', 1),
(154, 'Please define a start and end date for your holiday period!', 0),
(155, 'Please correct the data of the shown entries!', 0),
(156, 'Could not make directory ''(.*)''!', 1),
(157, 'Sorry, this task has already been used (.*) times, it cant be removed!', 1),
(158, '(.{0,30}) (.{0,30}) is not a team member of project: ''(.*)''!', 3),
(159, 'You are not a team member of project: ''(.*)''!', 1),
(160, 'The project is not available anymore, you must not remove the entry!', 0),
(161, 'You can not remove this user, because there are still times logged for him/her!', 0),
(162, 'This project is not available at the date you specified or not modifyable anymore without admin permissions!', 0),
(163, 'Please enter a valid email-address!', 0),
(164, 'You can not switch to admin-mode!', 0),
(165, 'A price for this entry does already exist', 0),
(166, 'Could not write into the OpenOffice-Template directory!', 0),
(167, 'Error retreiving the template, please try again!', 0),
(168, 'Error saving file in export directory (''(.*)'')!', 1),
(169, '(.{0,30}) (.{0,30}) has already booked times on this project, user can''t be removed!', 2),
(170, 'There are already (\\d+) entries saved outside the new period, you want to specify!', 1),
(171, 'Your start time was later than the end time, they were switched!', 0),
(172, 'Your start date was after the end date, they were switched!', 0),
(173, 'Time(s) saved.', 0),
(174, '(\\d+) datasets successfully saved.', 1),
(175, 'start task', 0),
(176, 'end task', 0),
(177, 'Are you sure to delete this entry?', 0),
(178, 'Are you sure you want to delete this user?\\n\\nAttention! All data of this user will be irrevocably deleted!', 0),
(179, 'Help', 0),
(189, 'logged in as User', 0),
(194, 'source', 0),
(195, 'destination format', 0),
(196, 'project(s)', 0),
(197, 'template', 0),
(198, 'type', 0),
(199, 'last exports', 0),
(200, 'export', 0),
(201, 'to PDF', 0),
(203, 'edit user', 0),
(204, 'Please specify a valid time and date!', 0),
(205, 'Your passwords don''t match!', 0),
(206, 'Your license only allows (\\d+) users!', 1),
(207, 'This is not a valid account name!', 0),
(208, 'Authentication modules could not be loaded properly, please contact your vendor!', 0),
(209, 'Your account is not active, please contact your vendor!', 0),
(210, 'Please enter your account name!', 0),
(211, 'Account name', 0),
(212, 'account name', 0),
(213, 'enter', 0),
(215, 'update page', 0),
(216, 'show all columns', 0),
(217, 'Admin mode - OFF', 0),
(218, 'You have switched to admin mode!', 0),
(219, 'Admin mode has been turned off, now you can work again as if you were a standard user!', 0),
(220, 'edit entry', 0),
(221, 'change', 0),
(222, 'file', 0),
(223, 'use template (upload)', 0),
(224, 'new template (upload and store)', 0),
(225, 'export data', 0),
(226, '\\* If you don''t have the OpenOffice-Suite installed on your system,(.*)you can download it for free from (.*)www.openoffice.org(.*)\\.\r\n', 3),
(227, 'Log for the project', 0),
(228, 'for:', 0),
(229, 'Are you sure?', 0),
(230, 'your account expires:', 0),
(231, 'account name:', 0),
(232, 'You don''t have the permission to edit this entry!', 0),
(235, 'You are not allowed to edit users!', 0),
(234, 'You don''t have the permission to remove this entry!', 0),
(236, 'The passwords don''t match!', 0),
(237, 'Please enter a valid login!', 0),
(238, 'This login is not available anymore!', 0),
(239, 'Please enter a password in order to change the username!', 0),
(240, 'Please enter the complete name!', 0),
(241, 'Please enter a password!', 0),
(242, 'password', 0),
(257, 'quick code', 0),
(244, 'reset', 0),
(255, 'remove', 0),
(253, 'authentication mode:', 0),
(252, 'open', 0),
(251, 'repeat password', 0),
(258, 'all users', 0),
(259, 'project managers', 0),
(260, 'team members', 0),
(262, 'Hello (.*),\r\n\r\n(.*) has registered you for openTimetool.\r\nYour access data are:\r\n\r\nusername:   (.*)\r\npassword:   (.*)\r\n(Please change your password right away!)\r\n\r\nYou can login here\r\n    (.*)\r\n\r\nbest regards\r\n\r\n(.*)', 0),
(263, 'Your timetool registration', 0),
(264, 'Time was rounded from (\\d+:\\d+) to (\\d+:\\d+) \\((\\d+) min. rounding\\).', 3),
(265, 'Info e-mail sent to ''(.*)''.', 1),
(266, 'Error copying template file!', 0),
(267, 'Could not open template file, please try again!', 0),
(268, 'Could not read template file, please try again!', 0),
(269, 'Could not copy uploaded file, please try again!', 0),
(270, 'Error sending the mail to ''(.*)''!', 1),
(271, 'You have not given an e-mail to send the info mail to!', 0),
(272, 'by week', 0),
(273, 'by project', 0),
(274, 'Overview from (.*) through (.*)', 2),
(275, 'task legend', 0),
(276, 'overview by week', 0),
(277, 'effort', 0),
(278, 'max. effort', 0),
(279, 'effort in %', 0),
(280, 'overview by project', 0),
(281, 'hours', 0),
(282, 'Edit/Add entry', 0),
(283, 'You are not a project manager of any project!', 0),
(284, 'No data for export', 0),
(285, 'If you check the subsequent option, the user gets a new random password and will be notified by mail.', 0),
(286, 'reset password', 0),
(287, 'Inherit team from parent project ''(.*)''', 1),
(290, 'User', 0),
(291, 'Password', 0),
(292, 'change', 0),
(293, 'date, time', 0),
(294, 'Are you sure you want to delete this project, all its sub projects and all  logged times ?\\n\\nAttention! All data of these projects will be irrevocably deleted!', 0);
