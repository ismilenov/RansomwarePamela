<div id="userspage_layer">
<?PHP if(isset($userscontent['islogged'])) { ?>
	<div id="userspage_logged"><?PHP echo $userscontent['islogged']; ?></div>
<?PHP } ?>
<div id="userspage_links">
	<?PHP echo implode(' | ', $userscontent['links']); ?>
</div>
<?PHP echo $userscontent['page']; ?>
</div>