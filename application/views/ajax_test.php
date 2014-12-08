<html>
    <head>
        <script type="text/javascript" src="<?php echo base_url() ?>js/ajax.js"></script>
        <script type="text/javascript">
			var ajax = new AjaxRequest();
			function ajax_test() {
				var ajaxPath = "<?php echo base_url() ?>index.php/game/load_cards";
				alert(ajaxPath);
				ajax.doTextRequest(ajaxPath, 'GET', '', showResults, true);
			}
			function showResults() {
				document.getElementById('tst').innerHTML = ajax.getValue();
			}
        </script>
    </head>
    <body>
        <h2>Ajax lib Test</h2>
        <button  onclick="ajax_test()">Click</button>
        <div id="tst"></div>
    </body>
</html>