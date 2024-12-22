<div class="userpage_text">
	<?PHP echo $userpage_accountshow['text']; ?>
</div>
<?PHP foreach($userpage_accountshow['fields'] as $field) { ?>
	<div class="userpage_lines">
		<strong><?PHP echo $field['value']; ?>:</strong> <?PHP echo $field['data']; ?>
	</div>
<?PHP } ?>