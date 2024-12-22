

<div class="newsoverview">
	<a href="index.php?page=news">&laquo; back to news list</a>
</div>

<div class="newsfull">
	<article>
		<div class="newsdate">
			<?PHP echo (isset($db_data['field_date']) && $db_data['field_date'] != '') ? strftime(_NEWSLANG_DATEFORMAT_, $db_data['field_date']) : ''; ?>
		</div>
		
		<h1><?PHP echo $db_data['field_headline']; ?></h1>

		<?PHP if($db_data['field_image'] != '') { ?>
			<div class="newsimage">
				<img src="<?PHP echo $db_data['field_image']; ?>" alt="<?PHP echo $db_data['field_imgalt']; ?>" />
			</div>
		<?PHP } ?>

		<div class="newstext">
			<?PHP echo $db_data['field_text']; ?>
		</div>
	</article>
</div>

<div class="newsprevnext">
	<a href="###LINK_PREV_DATA###">&laquo; <?PHP echo _NEWSLANG_PREV_DATA_; ?></a> <a href="###LINK_NEXT_DATA###"><?PHP echo _NEWSLANG_NEXT_DATA_; ?> &raquo;</a>
</div>
