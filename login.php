<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" href="login.css">
	<?php
	function __autoload($class_name)
	{
		$file = "$class_name.php";
		require_once $file;
	}
	?>
</head>

<body>

	<form method="post" action="" name="Please Sign In">
		<div class="formElement">
			<label>Username</label>
			<input type="text" name="username" pattern="[a-zA-Z0-9]+" required />
		</div>
		<div class="formElement">
			<label>Password</label>
			<input type="password" name="password" required />
		</div>
		<button class="loginButton" type="submit" name="login" value="login">Log In</button>
	</form>

	<?php
	if (!isset($_POST['username'], $_POST['password'])) {
		exit('Please fill both login and password field');
	}
	if (isset($_POST['login'])) {
		$username = $_POST["username"];
		$password = $_POST["password"];

		$conn = DB::getConnection();

		$query = $conn->prepare("SELECT * FROM author WHERE USERNAME=:username");
		$query->bindParam("username", $username, PDO::PARAM_STR);
		$query->execute();

		$row = $query->fetch(PDO::FETCH_ASSOC);

		if (!$row) {
			echo "<p class='error'>Password or username is wrong.</p>";
		} else {
			if (hash('SHA224', $password) == $row['pass']) {
				$_SESSION['AuthorId'] = $row['AuthorId'];
				$_SESSION['editor'] = $row['editor'];
				echo '<p class="success">Welcome, you are now logged in!</p>';
				header('Location: List.php');
				exit();
			} else {
				echo "<p class='error'>Password or username is wrong.</p>";
			}
		}
	}

	?>

</body>

</html>