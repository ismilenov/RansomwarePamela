<div class="searchpage_form">
	<form action="index.php?page=<?PHP echo $wbsearch_box['getpage']; ?>" method="post">
		<h2><?PHP echo $wbsearch_box['searchname']; ?></h2>
		<div>
			<input type="text" name="query" value="" class="searchpage_word" />
			<input type="submit" name="search" value="<?PHP echo $wbsearch_box['searchbutton']; ?>" class="searchpage_button" />
		</div>
    </form>
</div>