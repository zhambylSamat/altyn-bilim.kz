CREATE TRIGGER `remove_dupliates_on_progress_group_tbl` 
BEFORE INSERT ON `progress_group` 
FOR EACH ROW 
BEGIN IF (
	EXISTS(
		SELECT 1 
		FROM progress_group 
		WHERE progress_group_num = NEW.progress_group_num 
		AND group_info_num = NEW.group_info_num)) 
THEN SIGNAL SQLSTATE VALUE '45000' 
SET MESSAGE_TEXT = 'INSERT failed due to duplicate record'; 
END IF; 
END;