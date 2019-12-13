
-- SETTINGS
SET @iTypeId = (SELECT `ID` FROM `sys_options_types` WHERE `name` = 'bx_credits' LIMIT 1);
SET @iCategId = (SELECT `ID` FROM `sys_options_categories` WHERE `type_id` = @iTypeId LIMIT 1);
DELETE FROM `sys_options` WHERE `category_id` = @iCategId;
DELETE FROM `sys_options_categories` WHERE `type_id` = @iTypeId;
DELETE FROM `sys_options_types` WHERE `id` = @iTypeId;

-- PAGES
DELETE FROM `sys_objects_page` WHERE `module` = 'bx_credits';
DELETE FROM `sys_pages_blocks` WHERE `module` = 'bx_credits' OR `object` IN ('bx_credits_home', 'bx_credits_checkout', 'bx_credits_orders_common', 'bx_credits_orders_administration', 'bx_credits_history_common', 'bx_credits_history_administration');

-- MENU
DELETE FROM `sys_objects_menu` WHERE `module` = 'bx_credits';
DELETE FROM `sys_menu_sets` WHERE `module` = 'bx_credits';
DELETE FROM `sys_menu_items` WHERE `module` = 'bx_credits' OR `set_name` IN ('bx_credits_submenu', 'bx_credits_orders_submenu');

-- ACL
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Module` = 'bx_credits';
DELETE FROM `sys_acl_actions` WHERE `Module` = 'bx_credits';

-- GRIDS
DELETE FROM `sys_objects_grid` WHERE `object` LIKE 'bx_credits_%';
DELETE FROM `sys_grid_fields` WHERE `object` LIKE 'bx_credits_%';
DELETE FROM `sys_grid_actions` WHERE `object` LIKE 'bx_credits_%';

-- ALERTS
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'bx_credits' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- EMAIL TEMPLATES
DELETE FROM `sys_email_templates` WHERE `Module` = 'bx_credits';
