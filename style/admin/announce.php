<h1>Announcements</h1>

<form method="post" action="announce.php?action=add">
Title: <input type="text" name="title" />
<br />Body:<br /><textarea rows="6" name="body" class="field span12"></textarea>
<br /><input type="submit" value="Create new announcement" />
</form>

<? foreach($announcements as $e) { //e is array(id, title, body, time) ?>
<h2><?= $e[1] ?> (<?= $e[3] ?>)</h2>
<?= $e[2] ?>
<p><a href="announce.php?action=delete&delete_id=<?= $e[0] ?>">(delete)</a></p>
<? } ?>
