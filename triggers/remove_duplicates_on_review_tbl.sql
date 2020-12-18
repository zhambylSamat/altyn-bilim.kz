CREATE TRIGGER `remove_dupliates_on_review_tbl` 
	BEFORE INSERT ON `review` 
	FOR EACH ROW 
	BEGIN 
		IF (EXISTS(SELECT 1 
					FROM review 
					WHERE review_info_num = NEW.review_info_num 
						AND group_student_num = NEW.group_student_num)) 
			THEN SIGNAL SQLSTATE VALUE '45000' 
				SET MESSAGE_TEXT = 'INSERT failed due to duplicate record'; 
		END IF; 
	END;