<!DOCTYPE html>
<html>
    <head>
        <title>Flash Card Game - Mistakes Report</title>
        <meta name="viewport" content="width=device-width" />
        <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
        <meta content="utf-8" http-equiv="encoding">
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
    </head>
    <body>
        <div class="header">
            <div class="headerText">Mistakes Report</div>
        </div>
        <div class="container">

            <table border="1" style="width:100%" id="table" class="top-margin">

                <thead>
                    <tr>
                        <th>
                            si
                        </th>
                        <th>
                            Question
                        </th>
                        <th >
                            Answer  
                        </th>
                        <th >
                            User Answer
                        </th>
                        <th >
                            History  
                        </th>
                        <th >
                            Rank  
                        </th>
                        <th >
                            Test History  
                        </th>
                        <th>
                            Last Seen
                        </th>
                        <th>
                            Last Shown
                        </th>
                        <th>
                            Last Date
                        </th>
                        <th>
                            Last Utp
                        </th>
                        </tr>
                </thead>
                <?php
                $type = '';
                $si = 1;
                $deck_id = 0;
                $game_date = '0';

                foreach ($allCards as $card) {
                    if ($deck_id != $card->deck_id || $game_date != $card->game_date) {
                        ?>
                        <tr>
                            <td colspan="4" class="blue-bg">
                                Date: <?= date("d-m-Y", strtotime($card->game_date)) ?>&nbsp;&nbsp;
                                Time: <?= date("H:i:s", strtotime($card->game_date)) ?>
                            </td>
                            <td  colspan="9"class="blue-bg">
                                Deck:   <?=
                $card->deck_name;
                $deck_id = $card->deck_id;
                $game_date = $card->game_date
                        ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tbody>
                        <tr>
                            <td>
                                <?= $si++; ?>
                            </td>
                            <td>
                                <?= $card->question ?>
                            </td>
                            <td>
                                <?= $card->answer ?> 
                            </td>
                            <td>
                               
                            </td>
                            <td>
                                <?= $card->history ?> 
                            </td>
                            <td>
                                <?= $card->rank ?> 
                            </td>
                            <td>
								<?= $card->test_history ?> 		 
                            </td>
                            <td>
                                <script>
                                    var diffDays ='';
                                    var date1 = "<?php echo date("Y-m-d\TH:i:s\Z", strtotime($card->itp)); ?>";
                                    var date = "<?php echo date("Y-m-d\TH:i:s\Z", strtotime($card->utp)); ?>";
                                                
                                    var date = new Date(date);
                                    var date1 = new Date(date1);
                                    var timeDiff = Math.abs(date1.getTime() - date.getTime());
                                                    
                                    diffDays = (timeDiff / (1000 *3600*24)).toFixed(5); 
                                    document.write(diffDays+" Days");
                                </script>
                            </td>
                            <td>
                                    <script>
                                    var last_shown = "<?php echo date("Y-m-d\TH:i:s\Z", strtotime($card->last_shown)); ?>";
                                    document.write(last_shown+" Days");
                                </script>
                            </td>
                            <td>
                                <script>
                                    var last_date = "<?php echo date("Y-m-d\TH:i:s\Z", strtotime($card->game_date)); ?>";
                                    document.write(last_date+" Days");
                                </script>
                            </td>
                            <td>
                                <script>
                                    var utp = "<?php echo date("Y-m-d\TH:i:s\Z", strtotime($card->utp)); ?>";
                                    document.write(utp+" Days");
                                </script>
                            </td>
                            <td>

                            </td>
                        </tr>
                    </tbody>
                    <?php
                }
                ?>
            </table>
        </div>
    </body>
</html>