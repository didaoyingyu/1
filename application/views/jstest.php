<!DOCTYPE html>
<html>
    <head>
        <title>Flash Card Game</title>
        <meta name="viewport" content="width=device-width" />

        <!-- Card handling logic -->
        <script type="text/javascript" src="<?php echo base_url() ?>js/deckHandler.js"></script>
        <script type="text/javascript">
			var dh = new DeckHandler();

			function numtest() {
				for (var i = 0; i < 100; i++) {
					console.log(dh.getNextCardReviewMode());
				}
			}
        </script>
    </head>
    <body>
        <input type="button" onclick="numtest()" value="Num"/>
    </body>
</html>