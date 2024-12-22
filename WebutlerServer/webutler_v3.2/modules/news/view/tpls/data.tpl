
<div class="newsview">
	<article>
		<?PHP if(isset($db_data['field_date']) && $db_data['field_date'] != '') { ?>
			<div class="newsdate">
				<?PHP echo strftime(_NEWSLANG_DATEFORMAT_, $db_data['field_date']); ?>
			</div>
		<?PHP } ?>
		
		<h2><a href="###LINK_FULLDATA###"><?PHP echo $db_data['field_headline']; ?></a></h2>
		
		<div class="newsteaser">
			<?PHP echo $db_data['field_teaser']; ?>
		</div>

		<div class="newsopen">
			<a href="###LINK_FULLDATA###"><?PHP echo _NEWSLANG_TODATA_; ?> &raquo;</a>
		</div>
	</article>
</div>
