<?php

class verif
{
	//filtrer les inputs
	function test_input($data)
	{
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars(strip_tags($data));
		return $data;
	}
}