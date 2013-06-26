<h1>Log file</h1>

<? if(!empty($message)) { ?>
<p><b><i><?= htmlspecialchars($message) ?></i></b></p>
<? } ?>

<p>Your log file appears below.</p>

<?
if($log !== false) {
	$last_line = "";
	?>
	
	<textarea rows="30" id="log" name="content" class="field span12" readonly="true"><?
		foreach($log as $line) {
			echo htmlspecialchars($line) . "\n";
			$last_line = $line;
		}
	?></textarea>
	
	<script type="text/javascript">
	var textarea = document.getElementById('log');
	var last_line = '<?= htmlspecialchars(addslashes($last_line)) ?>';
	textarea.scrollTop = textarea.scrollHeight;
	
	window.setInterval(function(){
		var getvars = {};
		getvars['id'] = <?= $service_id ?>;
		getvars['last_line'] = last_line;
		
		$.get("log_fast.php", getvars, function(data) {
			var doScroll = textarea.scrollHeight - textarea.scrollTop <= textarea.offsetHeight;
			var lastTop = textarea.scrollTop;
			
			if(data.length != 0) {
				if(data[data.length - 1] != "\n") {
					data += "\n";
				}
			
				$('#log').append(data);
				var lines = data.split("\n");
				
				for(var i = lines.length - 1; i >= 0; i--) {
					if(lines[i] != "") {
						last_line = lines[i];
						break;
					}
				}
			
				if(doScroll) {
					textarea.scrollTop = textarea.scrollHeight;
				} else {
					textarea.scrollTop = lastTop;
				}
			}
		}, 'html');
	}, 5000);
	</script>
<? } else { ?>
	<p><b><i>Error while reading log: probably doesn't exist.</i></b></p>
<? } ?>

<form method="post" action="log.php" class="form-inline">
<input type="hidden" name="id" value="<?= $service_id ?>" />
<input type="text" name="command" class="input-xlarge" />
<input type="submit" value="Submit rcon command" />
</form>
