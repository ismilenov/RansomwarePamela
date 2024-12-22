<h3><?PHP echo $userpage_newreg['headline']; ?></h3>
<?PHP echo $userpage_newreg['alert']; ?>
<div class="userpage_text">
	<?PHP echo $userpage_newreg['regtext']; ?>
</div>
<form method="post" action="<?PHP echo $userpage_newreg['requesturi']; ?>">
	<div class="userpage_lines">
		<input type="text" name="uname" placeholder="<?PHP echo $userpage_newreg['username']; ?>" value="<?PHP echo $userpage_newreg['post']['uname']; ?>" class="userspage_input" />
	</div>
	<div class="userpage_lines">
		<input type="text" name="upass" placeholder="<?PHP echo $userpage_newreg['password']; ?>" value="" class="userspage_input" />
	</div>
	<div class="userpage_lines">
		<input type="text" name="umail" placeholder="<?PHP echo $userpage_newreg['mailaddress']; ?>" value="<?PHP echo $userpage_newreg['post']['umail']; ?>" class="userspage_input" />
	</div>
	<?PHP if(isset($userpage_newreg['regfields'])) {
	foreach($userpage_newreg['regfields'] as $name => $value) { ?>
		<div class="userpage_lines">
			<input type="text" placeholder="<?PHP echo $value; ?>" name="<?PHP echo $name; ?>" value="<?PHP echo $userpage_newreg['post'][$name]; ?>" class="userspage_input" />
		</div>
	<?PHP } } ?>
	<div class="userpage_lines">
		<input type="submit" name="registrieren" value="<?PHP echo $userpage_newreg['register']; ?>" class="userspage_button" />
	</div>
</form>