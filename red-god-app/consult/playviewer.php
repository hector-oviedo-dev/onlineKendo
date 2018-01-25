<!DOCTYPE html>
<html lang="en">
  <head>
    <title>JUGADA ANTERIOR</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <?php
  $reels = $_POST["reels"];
  $reelsPos = $_POST["reelsPos"];
  $lines = $_POST["lines"];

  $credits = $_POST["credits"];
  $creditsFinal = $_POST["creditsFinal"];
  $win = $_POST["win"];
  $totalbet = $_POST["totalbet"];

  $fs = $_POST["fs"];
  $gameid = $_POST["gameid"];
  ?>
  <body class="container" bgcolor="#cccccc">
    <canvas id="canvas" width="1280" height="1024"></canvas>

  <script type="text/javascript">

  var xoff = 64;
  var yoff = 104;

  var wdt = 224 + 8;
  var hgt = 168;

  var reels =  JSON.parse(<?php echo json_encode($reels); ?>);
  var reelsPos =  JSON.parse(<?php echo json_encode($reelsPos); ?>);
  var lines =  JSON.parse(<?php echo json_encode($lines); ?>);

  var credits =  <?php echo json_encode($credits); ?>;
  var creditsFinal =  <?php echo json_encode($creditsFinal); ?>;
  var win =  <?php echo json_encode($win); ?>;
  var totalbet =  <?php echo json_encode($totalbet); ?>;

  var fs =  <?php echo json_encode($fs); ?>;
  var gameid =  <?php echo json_encode($gameid); ?>;

  var circle_canvas = document.getElementById("canvas");
  var context = circle_canvas.getContext("2d");

  function drawCanvas() {
    var img = new Image();

    var path;
    if (!fs) path = "images/<?php echo $gameid; ?>/layout.png";
    else path = "images/<?php echo $gameid; ?>/fs/layout.png";

    img.src = path;
    img.onload = function() {
      context.drawImage(img,0,0);
      loadGrid();
    };
  }
  function loadGrid() {
    var symbols = [];
    for (var reel = 0; reel < reels.length; reel++) {

      for (var symbol = 0; symbol < reels[reel].length; symbol++) {
        var xpos = xoff + reel * wdt;
        var ypos = yoff + symbol * hgt;

        var symbolid = reel + symbol;

        symbols[symbolid] = new Image();

        if (!fs) symbols[symbolid].src = "images/<?php echo $gameid; ?>/" + reels[reel][symbol] + ".png";
        else symbols[symbolid].src = "images/<?php echo $gameid; ?>/fs/" + reels[reel][symbol] + ".png";

        symbols[symbolid].onload = (function(xpos,ypos) {
          return function() {
            context.drawImage(this, xpos, ypos);
          };
        })(xpos,ypos);
      }
    }
    loadTexts();
  }
  function loadTexts() {
    context.font="40px verdana";
    context.fillStyle = "white";
    context.fillText(totalbet, 330, 980);
    context.fillText(creditsFinal,470, 980);
    context.fillText(win,805, 980);
  }

  drawCanvas();
  </script>
  </body>
 </html>
