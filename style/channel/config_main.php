<h1>Main configuration</h1>

<p>A full list of main configuration options appears below. When you are done making changes, press the submit button at the bottom of the page.</p>

<form method="post" action="config_main.php">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="id" value="<?= $service_id ?>" />

<table cellpadding="4">
<?
foreach($mconfig as $k => $v) {
	channelDisplayConfiguration($k, $v, $parameters);
}
?>
</table>

<input type="submit" value="Submit" />

</form>
