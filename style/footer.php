      <hr>

      <footer>
        <p>Powered by <a href="http://www.codelain.com/forum/index.php?topic=22480.0">uxpanel</a>, a web game server control panel developed by <a href="http://www.codelain.com/forum/index.php?action=profile;u=14501">uakf.b</a> and <a href="https://lunaghost.com/">Luna Ghost</a>.</p>
        <? if(isset($context) && $context == "admin") { ?>
		    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="U2ESYFVFEZ4BC">
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
        <? } ?>
      </footer>
    </div>
    
  </body>
</html>

