CREATE TABLE `excel_points_formula` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `formula` varchar(255),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Формулы для расчета оценки по экселю';

insert into `excel_points_formula` (`formula`) values ('=SUM(N6:Q7)+SUM(N10:Q14)');

insert into `excel_points_formula` (`formula`) values ('=SUM(N6:Q7)+SUM(N10:Q14)-SUM(N8:Q8)-SUM(N15:Q15)');
insert into `excel_points_formula` (`formula`) values ('=SUM(R6:R7)+SUM(R10:R14)');
insert into `excel_points_formula` (`formula`) values ('=SUM(R6:R7)+SUM(R10:R14)-R8-R15');
insert into `excel_points_formula` (`formula`) values ('=SUM(B6:M7)+SUM(B10:M14)');
insert into `excel_points_formula` (`formula`) values ('Производство!=SUM(B6:M7)+SUM(B10:M14)');
insert into `excel_points_formula` (`formula`) values ('=SUM(N6:Q7)+SUM(N10:Q14)-SUM(B6:M7)-SUM(B10:M14)');
insert into `excel_points_formula` (`formula`) values ('=SUM(R6:R7)+SUM(R10:R14)-SUM(B6:M7)-SUM(B10:M14)');
insert into `excel_points_formula` (`formula`) values ('=SUM(N16:Q16)-(SUM(B8:M8)-SUM(B15:M15))');
insert into `excel_points_formula` (`formula`) values ('=R16-(SUM(B8:M8)-SUM(B15:M15))');
insert into `excel_points_formula` (`formula`) values ('=R18');
insert into `excel_points_formula` (`formula`) values ('=SUM(N19:Q19)');
insert into `excel_points_formula` (`formula`) values ('=SUM(N20:Q20)');

alter table `simulations_excel_points` add column `formula_id` int(11);
alter table `simulations_excel_points` add CONSTRAINT `fk_simulations_excel_points_formula_id` 
FOREIGN KEY (`formula_id`) REFERENCES `excel_points_formula` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;







