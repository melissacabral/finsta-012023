<?php 
$feedback = '';
$feedback_class = '';

//logout behavior. The URL will have ?action=logout
if( isset( $_GET['action'] ) AND $_GET['action'] == 'logout' ){
	//expire all cookies
	setcookie( 'access_token', 0, time() - 9999 );
	setcookie( 'user_id', '', time() - 9999 );

	//unset all session vars
	$_SESSION = array();

	// https://www.php.net/manual/en/function.session-destroy
	// If it's desired to kill the session, also delete the session cookie.
	// Note: This will destroy the session, and not just the session data!
	if (ini_get("session.use_cookies")) {
	    $params = session_get_cookie_params();
	    setcookie(session_name(), '', time() - 42000,
	        $params["path"], $params["domain"],
	        $params["secure"], $params["httponly"]
	    );
	}

	//take care of the session ID
	session_destroy();

	//redirect
	header('Location:login.php');

} //end logout

//if the login form was submitted, parse the form
if( isset( $_POST['did_login'] )){
	//sanitize the values from the form
	$username = clean_string($_POST['username']);
	$password = clean_string($_POST['password']);

	//validate
	$valid = true;
	//username wrong length
	if( strlen($username) < USERNAME_MIN OR strlen($username) > USERNAME_MAX ){
		$valid = false;	
	}
	//password wrong length
	if( strlen($password) < PASSWORD_MIN ){
		$valid = false;
	}
	//if valid, look them up in the DB
	if( $valid ){
		//look up the username
		$result = $DB->prepare('SELECT user_id, password
								FROM users
								WHERE username = ?
								LIMIT 1');
		$result->execute( array( $username ) );
		//check - if one row found, check the password
		if( $result->rowCount() > 0 ){
			$row = $result->fetch();
			//check hashed password
			if( password_verify( $password, $row['password'] ) ){
				//SUCCESS
				$feedback = 'Success!';
				$feedback_class = 'success';

				//remember the user for 2 weeks
				//generate a secret key
				$access_token = bin2hex( random_bytes(30) );
				$expire = time() + 60 * 60 * 24 * 14;
				setcookie( 'access_token', $access_token, $expire );
				$_SESSION['access_token'] = $access_token;

				$hashed_id = password_hash( $row['user_id'], PASSWORD_DEFAULT );
				setcookie( 'user_id', $hashed_id, $expire );
				$_SESSION['user_id'] = $hashed_id;
		
				//store the access token in the database
				$result  = $DB->prepare('UPDATE users
										SET access_token = :token
										WHERE user_id = :id
										LIMIT 1');
				$result->execute( array(
									'token' => $access_token,
									'id' 	=> $row['user_id'],
								) );

				//redirect to the home page
				header('Location:index.php');
				
			}else{
				//bad password
				$feedback = 'Incorrect Password';
				$feedback_class = 'error';
			}
		}else{
			//username not found
			$feedback = 'User doesn\'t exist';
			$feedback_class = 'error';	
		}
	}else{
		//invalid
		$feedback = 'Invalid Login';
		$feedback_class = 'error';
	}
} //end form parse