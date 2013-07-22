CREATE TABLE `owmonitoring_report` (
 `identifier` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
 `date` datetime DEFAULT NULL,
 `serialized_data` longtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.minute',  '2013-07-17 12:10:00',  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');
INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.minute',  '2013-07-17 12:20:00',  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');
INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.minute',  '2013-07-17 12:30:59',  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');
INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.minute',  NOW(),  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');

INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.quarter_hour',  '2013-07-17 00:00:00',  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');
INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.quarter_hour',  '2013-07-17 00:15:00',  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');
INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.quarter_hour',  '2013-07-17 00:30:00',  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');
INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.quarter_hour',  '2013-07-17 00:45:00',  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');
INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.quarter_hour',  NOW(),  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');


INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.houly',  '2013-07-17 00:00:00',  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');
INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.houly',  '2013-07-17 01:00:00',  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');
INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.houly',  NOW(),  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');

INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.daily',  '2013-07-17 00:00:00',  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');
INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.daily',  '2013-07-18 00:00:00',  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');
INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.daily',  NOW(),  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');

INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.weekly',  '2013-07-10 00:00:00',  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');
INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.weekly',  '2013-07-17 00:00:00',  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');

INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.monthly',  '2013-07-01 00:00:00',  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');
INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.monthly',  '2013-08-01 00:00:00',  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');
INSERT INTO  `owmonitoring_report` ( `identifier` , `date` , `serialized_data` )
VALUES ( 'test.monthly',  NOW(),  'a:2:{s:4:"test";a:1:{i:0;a:1:{s:4:"data";s:4:"test";}}s:6:"test_2";a:1:{i:0;a:1:{s:4:"data";s:6:"test_1";}}}');
