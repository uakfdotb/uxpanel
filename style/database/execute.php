<h1>Execute command</h1>

<? if(isset($message) && $message != "") { ?>
<p><b><i><?= htmlspecialchars($message) ?></i></b></p>
<? } ?>

<p>Fill out the form below to execute a command on your bot.</p>

<form method="post" action="execute.php">
<input type="hidden" name="id" value="<?= $service_id ?>" />
Bot ID: <input type="text" name="botid" />
<br />Command: <input type="text" name="command" />
<br /><input type="submit" value="Execute" />
</form>
