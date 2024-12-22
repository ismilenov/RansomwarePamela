<div class="userpage_text">
	<?PHP echo $userpage_accountedit['text']; ?>
</div>
<form method="post" action="<?PHP echo $userpage_accountedit['formurl']; ?>">
	<?PHP foreach($userpage_accountedit['fields'] as $field) { ?>
		<div class="userpage_lines">
			<?PHP echo ($field['name'] != 'uname' ? '<input type="text placeholder="'.$field['value'].'" name="'.$field['name'].'" value="'.$field['data'].'" class="userspage_input" />' : $field['data']); ?>
		</div>
	<?PHP } ?>
	<div class="userpage_lines">
		<input type="submit" name="neuedaten" value="<?PHP echo $userpage_accountedit['send']; ?>" class="userspage_button" />
	</div>
</form>