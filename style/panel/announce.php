<h1>Announcements</h1>

<? foreach($announcements as $e) { //e is array(id, title, body, time) ?>
<h2><?= htmlspecialchars($e['title']) ?> (<?= htmlspecialchars($e['time']) ?>)</h2>
<?= $e['body'] ?>
<? } ?>
