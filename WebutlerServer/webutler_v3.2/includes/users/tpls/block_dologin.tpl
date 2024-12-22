<div id="loginblock_frame">
	<form action="<?PHP echo $userscontent['requesturi']; ?>" method="post">
		<div class="loginblock_lines">
			<strong><?PHP echo $userscontent['headline']; ?></strong>
		</div>
		<div class="loginblock_lines">
			<input type="text" name="username" placeholder="<?PHP echo $userscontent['username']; ?>" value="" class="loginblock_input" />
		</div>
		<div class="loginblock_lines">
			<input type="password" name="userpass" placeholder="<?PHP echo $userscontent['userpass']; ?>" value="" class="loginblock_input" />
		</div>
		<div class="loginblock_lines">
			<input type="submit" name="userlogin" value="<?PHP echo $userscontent['userlogin']; ?>" class="loginblock_button" />
		</div>
	</form>
</div>