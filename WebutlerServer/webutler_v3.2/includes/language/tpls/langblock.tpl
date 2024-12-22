<div class="language_block">
	<?PHP foreach($wblangtplvars as $var) { ?>
		<a href="<?PHP echo $var['href']; ?>">
			<img src="<?PHP echo $var['src']; ?>" alt="<?PHP echo $var['lang']; ?>" title="<?PHP echo $var['lang']; ?>" />
		</a>
	<?PHP } ?>
</div>