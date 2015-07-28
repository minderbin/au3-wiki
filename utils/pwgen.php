<form action='#' method='post'>
	<input type='text' name='pw' value='<?php echo $_POST['pw'];?>'><label for='pw'>Password</label><br>
	<input type='submit' value='submit'>
</form>
<?php
if (isset($_POST['pw'])) echo '<h2>Generated hash:</h2><b>'.generateHash($_POST['pw']).'</b>';
function generateHash($password) {
    if (defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) {
	$salt = '$2y$11$' . substr(md5(uniqid(rand(), true)), 0, 22);
	return crypt($password, $salt);
    }
}
?>
