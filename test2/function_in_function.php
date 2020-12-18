<?php

	f1(0);

	function f1($digit){
		$res = "";
		
		function get_positive() {
			return "this digit is positive";
		}

		function get_negative() {
			return "this digit is negative";
		}

		function get_zero() {
			return "this digit is 0";
		}

		function get_even() {
			return "this digit is even";
		}

		function get_odd() {
			return "this digit is odd";
		}

		if ($digit > 0) {
			$res .= get_positive();
		} else if ($digit < 0) {
			$res .= get_negative();
		} else {
			$res .= get_zero();
		}

		$res .= "<br>";

		if ($digit != 0) {
			if ($digit%2==0) {
				$res .= get_even();
			} else {
				$res .= get_odd();
			}
		}

		echo $res;
	}
?>