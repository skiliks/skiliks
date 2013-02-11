
INSERT INTO `users` (`id`, `password`, `email`, `is_active`) VALUES
(5, '0e169a21c8c70b6ef485a28c77cb5631', 'vika.marquez@gmail.com', 0),
(6, 'b959d09adbc2d9f4cc5ac5105b82a3df', 'a.levina@gmail.com', 1),
(8, '098f6bcd4621d373cade4e832627b4f6', 'andrey@kostenko.name', 1),
(9, '202cb962ac59075b964b07152d234b70', 'doriangray@ukr.net', 1),
(10, '098f6bcd4621d373cade4e832627b4f6', 'andrey1@kostenko.name', 1),
(11, '202cb962ac59075b964b07152d234b70', 'tony_perfectus@mail.ru', 1),
(12, '202cb962ac59075b964b07152d234b70', 'tonypr1982@gmail.com', 1);


INSERT INTO `users_activation_code` (`id`, `uid`, `code`) VALUES
(2, 5, '19db70951611a9a1b465fbdd775c51ff'),
(3, 6, 'fb578af11eb4d5bcfe088a940c49c48a'),
(5, 8, '4ff19622c68af3d0590590f7bc400cc7'),
(6, 9, '807579f9481f0f56c14aac28dcb44f06'),
(7, 10, '1d7301f605cb1c602599eb715e77ee05'),
(8, 11, 'aa22bc8497e3f3a4fca9578659ce7be6'),
(9, 12, '8224d287b72b04bc20853ad60c59d7fe');

INSERT INTO `user_groups` (`id`, `uid`, `gid`) VALUES
(8, 9, 1),
(9, 10, 1),
(10, 11, 1),
(11, 12, 1);