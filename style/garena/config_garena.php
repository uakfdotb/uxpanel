<h1>Garena configuration</h1>

<p>A full list of configuration options appears below. When you are done making changes, press the submit button at the bottom of the page.</p>

<form method="post" action="config_garena.php">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="id" value="<?= $service_id ?>" />

<table cellpadding="4">
<?
foreach($gconfig as $k => $v) {
	garenaDisplayConfiguration($k, $v, $parameters);
}
?>
</table>

<input type="submit" value="Submit" />

</form>
