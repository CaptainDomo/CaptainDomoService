USE `CaptainDomo`;

TRUNCATE TABLE `member`;
INSERT INTO `member`
(`id`, `number`, `firstname`, `lastname`)
VALUES
  ('1', '23', 'Philipp', 'Feustel'),
  ('2', '353', 'Helena', 'Borowski'),
  ('3', '748', 'Helena', 'August');

TRUNCATE TABLE `precheck_member`;
INSERT INTO `precheck_member`
(`precheck_id`, `number`, `firstname`, `lastname`)
VALUES
  ('1453479764', '23', 'Philipp', 'Feustel');

TRUNCATE TABLE `subscription`;
INSERT INTO `subscription`
(`id`, `email`, `number`, `firstname`, `lastname`)
VALUES
  ('1', 'phfeustel@gmx.de', '23', 'Philipp', 'Feustel'),
  ('9', 'lenchen.zone@gmx.de', '353', 'Helena', 'Borowski'),
  ('12', 'puck.August@gmx.com', '748', 'Helena', 'August'),
  ('11', 'lenchen.zone@gmx.de', '748', 'Helena', 'August');

TRUNCATE TABLE `suspect`;