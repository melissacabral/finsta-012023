<?php
$feedback = '';
$feedback_class = '';
$errors = array();

//if the form was submitted, parse it
if( isset($_POST['did_register']) ){
	//sanitize every field
	$username = clean_string( $_POST['username'] );
	$email = filter_var( $_POST['email'], FILTER_SANITIZE_EMAIL );
	$password = clean_string( $_POST['password'] );
	if( isset( $_POST['policy'] ) ){
		$policy = 1;
	}else{
		$policy = 0;
	}
	//validate
	$valid = true;
	//username isnt 5-30 chars long
	if( strlen($username) < USERNAME_MIN OR strlen($username) > USERNAME_MAX ){
		$valid = false;
		$errors['username'] = 'Username must be between ' . USERNAME_MIN . ' and ' . USERNAME_MAX . ' characters long';
	}else{
		//username must be unique
		$result = $DB->prepare('SELECT username 
								FROM users
								WHERE username = ?
								LIMIT 1');
		$result->execute( array( $username ) );
		//if one row found, username is already taken
		if( $result->rowCount() > 0 ){
			$valid = false;
			$errors['username'] = 'That username is already taken';
		}
	} //end of username checks
	
	//invalid email address
	if( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ){
		$valid = false;
		$errors['email'] = 'Invalid Email Address';
	}else{
		//email must be unique
		$result = $DB->prepare('SELECT email 
								FROM users
								WHERE email = ?
								LIMIT 1');
		$result->execute( array( $email ) );
		//if one row found, email is already taken
		if( $result->rowCount() > 0 ){
			$valid = false;
			$errors['email'] = 'That email is already taken. Try logging in.';
		}		
	}//end email checks
	
	//password too short ( < 8 )
	if( strlen($password) < PASSWORD_MIN ){
		$valid = false;
		$errors['password'] = 'Your password needs to be at least ' . PASSWORD_MIN . 
								' characters';
	}
	//policy not checked
	if( ! $policy ){
		$valid = false;
		$errors['policy'] = 'You must agree to the Terms of Service to sign up.';
	}
	//if valid, add the user to the DB
	if( $valid ){
		//get the first letter for the profile pic
		$letter = $username[0];
		$profile_pic = make_letter_avatar($letter, 100);

		$result = $DB->prepare('INSERT INTO users
								(username, email, password, profile_pic, is_admin, join_date)
								VALUES
								( :username, :email, :pass, :pic, 0, now() )
								');
		$result->execute( array(
							'username' 	=> $username,
							'email' 	=> $email,
							'pass'		=> password_hash( $password, PASSWORD_DEFAULT ),
							'pic'		=> $profile_pic
						) );
		if( $result->rowCount() > 0 ){
			$feedback = 'Success! You can now log in.';
			$feedback_class = 'success';
		}else{
			$feedback = 'Database Error';
			$feedback_class = 'error';
		}
	} //end if valid
	else{
		$feedback = 'Sorry, your registration is incomplete. Fix the following:';
		$feedback_class = 'error';
	}
}//end parser