<h1>Announcements</h1>

<? foreach($announcements as $e) { //e is array(id, title, body, time) ?>
<h2><?= $e[1] ?> (<?= $e[3] ?>)</h2>
<?= $e[2] ?>
<? } ?>
