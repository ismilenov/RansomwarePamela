<div id="loginblock_frame">
	<form action="<?PHP echo $userscontent['requesturi']; ?>" method="post">
		<div class="loginblock_lines">
			<?PHP echo $userscontent['headline']; ?>:
		</div>
		<div class="loginblock_lines">
			<strong><?PHP echo $userscontent['username']; ?></strong>
		</div>
		<div class="loginblock_lines">
			<input type="submit" name="userlogout" value="<?PHP echo $userscontent['userlogout']; ?>" class="loginblock_button" />
		</div>
	</form>
</div>