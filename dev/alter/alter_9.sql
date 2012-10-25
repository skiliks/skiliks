delete from `excel_points_formula`;

ALTER TABLE `excel_points_formula` AUTO_INCREMENT =1;

insert into `excel_points_formula` (`id`, `formula`) values (1, '=SUM(Логистика!B6:M7)+SUM(Логистика!B10:M14)');
insert into `excel_points_formula` (`id`, `formula`) values (2, '=SUM(Производство!B6:M7)+SUM(Производство!B10:M14)');
insert into `excel_points_formula` (`id`, `formula`) values (3, '=SUM(Сводный!N6:Q7)+SUM(Сводный!N10:Q14)-SUM(Сводный!B6:M7)-SUM(Сводный!B10:M14)');
insert into `excel_points_formula` (`id`, `formula`) values (4, '=SUM(Сводный!R6:R7)+SUM(Сводный!R10:R14)-SUM(Сводный!B6:M7)-SUM(Сводный!B10:M14)');
insert into `excel_points_formula` (`id`, `formula`) values (5, '=SUM(Сводный!N16:Q16)-(SUM(Сводный!B8:M8)-SUM(Сводный!B15:M15))');
insert into `excel_points_formula` (`id`, `formula`) values (6, '=Сводный!R16-(SUM(Сводный!B8:M8)-SUM(Сводный!B15:M15))');
insert into `excel_points_formula` (`id`, `formula`) values (7, '=Сводный!R18');
insert into `excel_points_formula` (`id`, `formula`) values (8, '=SUM(Сводный!N19:Q19)');
insert into `excel_points_formula` (`id`, `formula`) values (9, '=SUM(Сводный!N20:Q20)');