ALTER TABLE  `dialogs` CHANGE  `ch_to`  `ch_to` INT( 11 ) NOT NULL DEFAULT  '0' COMMENT  'персонаж которому должен исходить текст';


ALTER TABLE  `dialogs` CHANGE  `ch_to`  `ch_to` INT( 11 ) NULL DEFAULT NULL COMMENT  'персонаж которому должен исходить текст'


INSERT INTO  `skiliks`.`dialog_subtypes` (
`id` ,
`type_id` ,
`title`
)
VALUES (
'5',  '2',  'Стук в дверь'
);

ALTER TABLE  `dialogs` add column `sound` varchar(32);

ALTER TABLE  `dialogs` add column `excel_id` int(11);