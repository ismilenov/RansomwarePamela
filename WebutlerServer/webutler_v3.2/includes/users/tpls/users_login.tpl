<h3><?PHP echo $userpage_login['headline']; ?></h3>
<?PHP echo $userpage_login['alert']; ?>
<div class="userpage_text">
	<?PHP echo $userpage_login['logtext']; ?>
</div>
<form method="post" action="<?PHP echo $userpage_login['requesturi']; ?>">
	<div class="userpage_lines">
		<input type="text" name="username" placeholder="<?PHP echo $userpage_login['username']; ?>" value="<?PHP echo $userpage_login['post']['username']; ?>" class="userspage_input" />
	</div>
	<div class="userpage_lines">
		<input type="password" placeholder="<?PHP echo $userpage_login['password']; ?>" name="userpass" value="" class="userspage_input" />
	</div>
	<div class="userpage_lines">
		<input type="submit" name="userslogin" value="<?PHP echo $userpage_login['logging']; ?>" class="userspage_button" />
	</div>
</form>