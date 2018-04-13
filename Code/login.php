<?php
	function redirect($url)
	{
		$string = '<script type="text/javascript">';
		$string .= 'window.location = "' . $url . '"';
		$string .= '</script>';

		echo $string;
	}
	
	
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$login = parse_ini_file('login.ini');
				
		if ($_POST['user'] == $login['username'] && $_POST['pass'] == $login['password']) {
			$_SESSION['login'] = $login['password'];
			redirect('admin.php?state=company');
			exit();
		} else {
			echo "<script type='text/javascript'>alert('Invalid username and password combination.');</script>";
		}
	}
?>

<html>
	<style>
		/* Bordered form */
		form {
			max-width: 1000px;
			margin: 0 auto;
			border: 3px solid #cccccc;
		}
		
		/* Full-width inputs */
		input[type=text], input[type=password] {
			width: 100%;
			padding: 12px 20px;
			margin: 8px 0;
			display: inline-block;
			border: 1px solid #ccc;
			box-sizing: border-box;
		}
		
		/* Set a style for all buttons */
		button {
			background-color: #000;
			color: white;
			padding: 14px 20px;
			margin: 8px 0;
			border: none;
			cursor: pointer;
			width: 100%;
		}
		
		/* Add a hover effect for buttons */
		button:hover {
			opacity: 0.7;
		}
		
		/* Center the login image inside this container */
		.imgcontainer {
			text-align: center;
		}
		
		/* Login image */
		img.login {
			border-radius: 50%;
		}
		
		/* Add padding to containers */
		.container {
			padding: 16px;
		}
	</style>
	
	<body>
		<form method = "post">
			<br>
			<div class="imgcontainer">
				<img src="loginKey.png" alt="Login" class="login">
			</div>
			
			<div class="container">
				<label for="uname"><b>Username:</b></label>
				<input type="text" placeholder="Enter Username" name="user" required>

				<label for="psw"><b>Password:</b></label>
				<input type="password" placeholder="Enter Password" name="pass" required>
				
				<button type="submit">Login</button>
			</div>
		</form>
	</body>
</html>