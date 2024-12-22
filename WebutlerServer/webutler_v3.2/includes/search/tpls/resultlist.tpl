<div class="searchpage">
	<?PHP echo $wbsearch_resultlist['searchbox']; ?>
	<div class="searchpage_resultlist">
		<h2><?PHP echo $wbsearch_resultlist['headline']; ?></h2>
		<?PHP foreach($wbsearch_resultlist['result'] as $count) { ?>
		<div class="searchpage_result">
			<h3><a href="<?PHP echo $count['link']; ?>"><?PHP echo $count['title']; ?></a></h3>
			<p><?PHP echo $count['contents']; ?><br />
			<a href="<?PHP echo $count['link']; ?>"><?PHP echo $count['url']; ?></a></p>
		</div>
		<?PHP } ?>
	</div>
	<?PHP echo $wbsearch_resultlist['pager']; ?>
</div>