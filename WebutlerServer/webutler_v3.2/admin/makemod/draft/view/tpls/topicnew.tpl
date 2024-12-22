
<?PHP echo $postalert; ?>

<form method="post" action="###INPUT_FORMURL###"###ENCTYPE###>

<?PHP echo _MODMAKERLANG_CREATE_NEWTOPIC_; ?><br />

<?PHP echo _MODMAKERLANG_NEWTOPIC_; ?> <input type="text" name="topic" value="<?PHP echo $post_data['topic']; ?>" /><br />

###LOAD_INPUTDATA_TPL###

<input type="submit" name="<?PHP echo $module->modname; ?>postsave" value="<?PHP echo _MODMAKERLANG_SAVE_; ?>" />

</form>
