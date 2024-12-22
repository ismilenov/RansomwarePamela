<h3><?PHP echo $userpage_newpass['headline']; ?></h3>
<?PHP echo $userpage_newpass['alert']; ?>
<div class="userpage_text">
	<?PHP echo $userpage_newpass['passtext']; ?>
</div>
<form method="post" action="<?PHP echo $userpage_newpass['requesturi']; ?>">
	<div class="userpage_lines">
		<input type="text" name="username" placeholder="<?PHP echo $userpage_newpass['username']; ?>" value="<?PHP echo $userpage_newpass['post']['username']; ?>" class="userspage_input" />
	</div>
	<div class="userpage_lines">
		<input type="text" name="usermail" placeholder="<?PHP echo $userpage_newpass['mailaddress']; ?>" value="<?PHP echo $userpage_newpass['post']['usermail']; ?>" class="userspage_input" />
	</div>
	<div class="userpage_lines">
		<input type="submit" name="sendnewpass" value="<?PHP echo $userpage_newpass['requestpass']; ?>" class="userspage_button" />
	</div>
</form>