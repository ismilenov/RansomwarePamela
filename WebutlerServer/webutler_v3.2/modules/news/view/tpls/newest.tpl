
<article>
	<?PHP if(isset($db_data['field_date']) && $db_data['field_date'] != '') { ?>
		<div class="newsdate">
			<?PHP echo strftime(_NEWSLANG_DATEFORMAT_, $db_data['field_date']); ?>
		</div>
	<?PHP } ?>
	<h3>
		<a href="###LINK_NEWEST###"><?PHP echo $db_data['field_headline']; ?></a>
	</h3>
	<p>
		<?PHP echo substr($db_data['field_teaser'], 0, 190); ?>...
	</p>
</article>
