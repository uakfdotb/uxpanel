<h1>Cron configuration</h1>

<p>Cron is the name for a scheduler that runs a periodic task. uxpanel can use a cron job to handle things such as updating scores and statistics.</p>

<p>Here, you can configure what the cron job does. Some tasks may be essential to keeping your bot's statistics and storage updated, but others may be optional.</p>

<form method="post" action="cron.php">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="id" value="<?= $service_id ?>" />

<table cellpadding="4">
<?
foreach($cconfig as $k => $v) {
	databaseDisplayConfiguration($k, $v, $parameters);
}
?>
</table>

<input type="submit" value="Submit" />

</form>
