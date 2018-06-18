-- Insert default villages
INSERT INTO `village`(`id`, `name`) VALUES (1, 'Brno');
INSERT INTO `village`(`id`, `name`) VALUES (2, 'Praha');

-- Insert default rights
INSERT INTO `right`(`id`, `name`) VALUES (1, 'addressBook');
INSERT INTO `right`(`id`, `name`) VALUES (2, 'search');

-- Insert demo users
INSERT INTO `user`(`id`, `name`) VALUE (1, 'Adam');
INSERT INTO `user`(`id`, `name`) VALUE (2, 'Bob');
INSERT INTO `user`(`id`, `name`) VALUE (3, 'Cyril ');
INSERT INTO `user`(`id`, `name`) VALUE (4, 'Derek');
INSERT INTO `user`(`id`, `name`) VALUE (5, 'Fred');

-- Insert demo admins
INSERT INTO `user_admin`(`id`, `user_id`)  VALUE (1, 1);
INSERT INTO `user_admin`(`id`, `user_id`)  VALUE (2, 2);
INSERT INTO `user_admin`(`id`, `user_id`)  VALUE (3, 3);
INSERT INTO `user_admin`(`id`, `user_id`)  VALUE (4, 5);

INSERT INTO `user_village_right`(`user_admin_id`, `village_id`, `right_id`) VALUES (1, 2, 1);
INSERT INTO `user_village_right`(`user_admin_id`, `village_id`, `right_id`) VALUES (1, 2, 2);

INSERT INTO `user_village_right`(`user_admin_id`, `village_id`, `right_id`) VALUES (2, 1, 1);
INSERT INTO `user_village_right`(`user_admin_id`, `village_id`, `right_id`) VALUES (2, 2, 2);

INSERT INTO `user_village_right`(`user_admin_id`, `village_id`, `right_id`) VALUES (3, 1, 1);
INSERT INTO `user_village_right`(`user_admin_id`, `village_id`, `right_id`) VALUES (3, 1, 2);
INSERT INTO `user_village_right`(`user_admin_id`, `village_id`, `right_id`) VALUES (3, 2, 1);
