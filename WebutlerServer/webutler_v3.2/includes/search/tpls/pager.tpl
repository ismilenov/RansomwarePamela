<div class="searchresult_pager">
	<?PHP if(isset($wbsearch_pager['prevlink'])) { ?>
		<a href="<?PHP echo $wbsearch_pager['prevlink']; ?>">&laquo; <?PHP echo $wbsearch_pager['prevtext']; ?></a> | 
	<?PHP } ?>
	<?PHP echo $wbsearch_pager['searchpage']; ?>
	<?PHP if(isset($wbsearch_pager['nextlink'])) { ?>
		| <a href="<?PHP echo $wbsearch_pager['nextlink']; ?>"><?PHP echo $wbsearch_pager['nexttext']; ?> &raquo;</a>
	<?PHP } ?>
</div>