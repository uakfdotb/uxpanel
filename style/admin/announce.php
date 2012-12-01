<h1>Announcements</h1>

<form method="post" action="announce.php?action=add">
Title: <input type="text" name="title" />
<br />Body:<br /><textarea rows="6" name="body" class="field span12"></textarea>
<br /><input type="submit" value="Create new announcement" />
</form>

<? foreach($announcements as $e) { //e is array(id, title, body, time) ?>
<h2><?= htmlspecialchars($e['title']) ?> (<?= htmlspecialchars($e['time']) ?>)</h2>
<?= $e['body'] ?>
<form method="post" action="announce.php">
<input type="hidden" name="action" value="delete" />
<input type="hidden" name="delete_id" value="<?= $e['id'] ?>" />
<input type="submit" value="(delete)" />
</form>
<? } ?>
