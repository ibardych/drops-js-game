<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<style type="text/css">
	body {padding: 4px; margin: 2px; background-color: #444;}
</style>
</head>
<body>

<style type="text/css">
			.cell {
				float: left;
				position: absolute;
				color: #ccc;
				background-color: #333;
				width: 60px;
				height: 60px;
				border-radius: 5px;
				box-sizing: border-box;
				padding: 5px;
				z-index: 1;
			}
			.cell.life {
				background-color: #541e4a;
			}
			.cellhover {
				-webkit-transition: 0.2s;
				opacity: 0.8;
			}
			.cellbutton {
				float: left;
				position: absolute;
				width: 60px;
				height: 60px;
				z-index: 10000000;
			}
			.drop {
				float: left;
				position: absolute;
				width: 40px;
				height: 40px;
				border-radius: 50px;
				z-index: 1;
			}

			.drop.type1 {
				width: 16px;
				height: 16px;
				margin: 12px 0 0 12px;
				background-color: #ffd005;
			}
			.drop.type2 {
				width: 24px;
				height: 24px;
				margin: 8px 0 0 8px;
				background-color: #ff6e2e;
			}
			.drop.type3 {
				width: 32px;
				height: 32px;
				margin: 4px 0 0 4px;
				background-color: #ff0000;
			}
			.drop.monster {
				width: 40px;
				height: 40px;
				margin: 0px 0 0 0px;
				background: url(monster.png) no-repeat center #ffffff;
				background-size: 36px 36px;
			}

			.drop.monster div {
				float: left;
				top: -5px;
				right: -5px;
				position: absolute;
				width: 20px;
				height: 20px;
				border-radius: 20px;
				font-size: 15px;
				font-weight: bold;
				text-align: center;
				line-height: 20px;
				background-color: #30b099;
				box-shadow: 1px 1px 5px 0px rgba(0,0,0,0.5);
				color: #fff;
			}
			
			#gameover {
				float: left;
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				color: #333;
				text-align: center;
				font-size: 50px;
				font-weight: bold;
				background-color: rgba(255,255,255,0.8);
				display: none;
				z-index: 1000;
				align-items: center;
			}

			#gameover div {
				float: left;
				width: 100%;
				text-align: center;
			}
		</style>

		<div style="float: left; width: 100%; text-align: left; padding: 0px 50px 50px 50px; background-color: #fff; margin-top: 30px; padding-top: 30px; box-sizing: border-box;">

			<?php $cellwidth = 60; ?>
			<?php $cellnumber = 7; ?>

			<div style="float: left; width: <?php echo ($cellwidth + 1) * $cellnumber + 80; ?>px;">
				<div id="score" onclick="start=1; startexploding()" style="float: left; width: 150px; height: 50px; background-color: #f5f5f5; margin-bottom: 30px; cursor: pointer; color: #444; font-size: 30px; text-align: center; line-height: 50px; text-align: right; padding: 0px 20px;">0</div>
				<div class="fa fa-sync-alt" onclick="startgame()" style="float: left; height: 50px; background-color: #f5f5f5; margin-bottom: 30px; margin-left: 20px; cursor: pointer; color: #666; font-size: 30px; text-align: center; line-height: 50px; text-align: right; padding: 0px 20px;">Reload</div>
				<div id="lives" onclick="start=1; startexploding()" style="float: left; height: 50px; background-color: red; margin-right: 30px; cursor: pointer; color: #fff; font-size: 40px; margin-left: 30px; text-align: center; line-height: 50px; padding: 0 10px;">5</div>
				
			</div>

			<div style="float: left; width: <?php echo ($cellwidth + 1) * $cellnumber + 80; ?>px; clear: both;">
				<div style="float: left; height: 50px; margin-bottom: 30px; cursor: pointer; color: #444; font-size: 40px; text-align: left; line-height: 50px; padding: 0px 20px;">Level:</div>
				<div id="round" style="float: left; height: 50px; margin-bottom: 30px; margin-left: 30px; cursor: pointer; color: #444; font-size: 40px; font-weight: bold; text-align: left; line-height: 50px; padding: 0px 20px;">1</div>
			</div>

			
			<div style="float: left; clear: both;">
				
				
				<div id="drops" style="float: left; position: relative; width: <?php echo $cellnumber * ($cellwidth + 1); ?>px; height: <?php echo $cellnumber * ($cellwidth + 1); ?>px; background-color: #fff;">
					
					<?php for ($x=0; $x <= $cellnumber-1; $x++) { ?>
						<?php for ($y=0; $y <= $cellnumber-1; $y++) { ?>
							<div class="cell cell<?= $x ?><?= $y ?>" style="left: <?php echo $x * $cellwidth + $x; ?>px; top: <?php echo $y * $cellwidth + $y; ?>px;"></div>
						<?php }?>
					<?php }?>
					<?php for ($x=0; $x <= $cellnumber-1; $x++) { ?>
						<?php for ($y=0; $y <= $cellnumber-1; $y++) { ?>
							<div data-x="<?= $x ?>" data-y="<?= $y ?>" class="cellbutton" style="left: <?php echo $x * $cellwidth + $x; ?>px; top: <?php echo $y * $cellwidth + $y; ?>px;"></div>
						<?php }?>
					<?php }?>

					<div id="gameover"><div>Game Over</div></div>
				</div>
			</div>
			

		</div>

		<script type="text/javascript">
			var test = 0;
			var celltime = 300;
			var cellnumber = <?= $cellnumber ?>;
			var cellpadleft = 10;
			var cellwidht = 60;
			var zindex = 2;
			var cell = 1;
			var cells = [];
			var start = 0;
			var movedcells = [];
			var exploded = 0;
			var round = 1;
			var lives = 5;
			var score = 0;


			$(document).ready(function(){
				startgame();
			});


			function startgame() {
				score = 0; $("#score").html(score);
				round = 1; $("#round").html(round);
				lives = 5; $("#lives").html(lives);
				exploded = 0;
				movedcells = [];

				$("#gameover").css({"display" : "none"});

				for (var x = 0; x < cellnumber; x++) {
					for (var y = 0; y < cellnumber; y++) {
						var num = Math.floor(Math.random() * 4);
						cells[x][y]["v"] = num;
						cells[x][y]["l"] = [];
						cells[x][y]["r"] = [];
						cells[x][y]["t"] = [];
						cells[x][y]["b"] = [];
						cells[x][y]["life"] = 0;
						cells[x][y]["monster"] = 0;
						$("#drops .drop" + x + y).remove();
						updatecell(x, y);
					}
				}
			}


			for (var x = 0; x < cellnumber; x++) {
				cells[x] = [];
				for (var y = 0; y < cellnumber; y++) {
					cells[x][y] = [];
					cells[x][y]["v"] = 0;
					cells[x][y]["l"] = [];
					cells[x][y]["r"] = [];
					cells[x][y]["t"] = [];
					cells[x][y]["b"] = [];
					cells[x][y]["life"] = 0;
					cells[x][y]["monster"] = 0;
				}
			}

			$("#drops .cellbutton").mouseover(function() {
			    var x = $(this).attr("data-x");
				var y = $(this).attr("data-y");
				$("#drops .cell" + x + y).addClass("cellhover");

			})
			.mouseout(function() {
				var x = $(this).attr("data-x");
				var y = $(this).attr("data-y");
				$("#drops .cell" + x + y).removeClass("cellhover");
			});

			$("#drops .cellbutton").click(function() {
				var x = $(this).attr("data-x");
				var y = $(this).attr("data-y");
				
				if(lives > 0) {
					if(cells[x][y]["v"] != -1){
						exploded = 0;
						cells[x][y]["v"] = cells[x][y]["v"] + 1;
						pop(1);
						checkcells();
						lives = lives - 1;
						$("#lives").html(lives);
					}else{
						pop(3);
					}
				}else{
					$("#gameover").css({"display" : "flex"});
				}
			});


			function checkcells() {
				start = 0;
				for (var x = 0; x < cellnumber; x++) {
					for (var y = 0; y < cellnumber; y++) {
						var xvalue = x * cellwidht + x * 1 + cellpadleft;
						var yvalue = y * cellwidht + y * 1 + cellpadleft;
						var val = cells[x][y]["v"];

						updatecell(x, y);

						if(val == 4) {
							start = 1;
							exploded = exploded + 1;
							setscore(10);
							startexploding();
						}
					}
				}
			}


			function setscore(value) {
				score = score + value;
				$("#score").html(score);
			}


			async function startexploding() {

				if(start) {
					for (var x = 0; x < cellnumber; x++) {
						for (var y = 0; y < cellnumber; y++) {

							var val = cells[x][y]["v"];

							var newval = val;

							var directions = ["l","r","t","b"];

							if(val > 0) {
								for (let direction=0; direction<directions.length; direction++) {
									var d = directions[direction];

									for (let i=0; i < cells[x][y][d].length; i++) {
										if(newval < 4) {
											newval = newval + 1;
											$("#drops .drop" + d + cells[x][y][d][i]).remove();
											cells[x][y][d].splice(i,1);
										}
									}
								}
							}

							cells[x][y]["v"] = newval;

							if(val == -1){
								var numdrops = 0;
								for (let direction=0; direction<directions.length; direction++) {
									var d = directions[direction];

									for (let i=0; i < cells[x][y][d].length; i++) {
										numdrops = numdrops + 1;
										$("#drops .drop" + d + cells[x][y][d][i]).remove();
										cells[x][y][d].splice(i,1);
									}
								}

								cells[x][y]["monster"] = cells[x][y]["monster"] + numdrops;
								if(cells[x][y]["monster"]) {
									$("#drops .drop" + x + y + ".monster div").html(cells[x][y]["monster"]);
								}
								if(cells[x][y]["monster"] >= 5){
									pop(5);
									cells[x][y]["v"] = 0;
									cells[x][y]["monster"] = 0;
									setTimeout('$("#drops .drop' + x + y + '.monster").remove();', 50);
									lives = lives - 5;
									$("#lives").html(lives);
								}
							}

							updatecell(x, y);

							var val = cells[x][y]["v"];

							if(val == 4){
								explodecell(x, y);
								if(cells[x][y]["life"] == 1) {
									pop(4);
									lives = lives + 3;
									$("#lives").html(lives);
									// $("#drops .cell" + x + y).removeClass("life");
									// cells[x][y]["life"] = 0;
								}

							}

							movecell("l", x, y, -1);
							movecell("r", x, y, 1);
							movecell("t", x, y, -1);
							movecell("b", x, y, 1);
							
						}
					}

					//startexploding()
					setTimeout('startexploding()', celltime);

					var forward = 0;
					$("#drops .movingdrop").each(function(){
						forward = 1;
					});

					if(forward == 0){
						start = 0;
						// setTimeout('alert("END")', celltime);

						var last = 0;

						for (var x = 0; x < cellnumber; x++) {
							for (var y = 0; y < cellnumber; y++) {
								if(cells[x][y]["v"] > 0) {
									last = last + 1;
								}
							}
						}

						if(last == 0) {

							$("#drops .drop.monster").remove();
							$("#drops .cell").removeClass("life");

							for (var x = 0; x < cellnumber; x++) {
								for (var y = 0; y < cellnumber; y++) {
									var num1 = Math.floor(Math.random() * 2);
									i = 4;
									if(num1 == 0 && round > 5) {i = 3;}
									if(num1 == 0 && round > 10) {i = 2;}
									var num = Math.floor(Math.random() * i);
									cells[x][y]["v"] = num;
									cells[x][y]["life"] = 0;
									cells[x][y]["monster"] = 0;
									updatecell(x, y);
								}
							}

							round = round + 1;
							$("#round").html(round);

							if(round >= 2){
								addMonster();
								addLife();
							}
							if(round >= 4){
								addMonster();
								addLife();
							}
							if(round >= 6){
								addMonster();
							}
							if(round >= 8){
								addMonster();
							}
							if(round >= 10){
								addMonster();
							}
							if(round >= 12){
								addMonster();
							}

							lives = lives + 1;
							$("#lives").html(lives);
						}
					}
				}
			}


			function addMonster() {
				var randomx = Math.floor(Math.random() * (cellnumber - 2));
				var randomy = Math.floor(Math.random() * (cellnumber - 2));

				var randomxvalue = randomx * cellwidht + randomx * 1 + cellpadleft;
				var randomyvalue = randomy * cellwidht + randomy * 1 + cellpadleft;

				cells[randomx][randomy]["v"] = -1;
				$("#drops .drop" + randomx + randomy).remove();
				$("#drops").append('<div class="drop monster drop' + randomx + randomy + '" style="left: ' + randomxvalue + 'px; top: ' + randomyvalue + 'px;"><div></div></div>');
			}


			function addLife() {
				var randomx = Math.floor(Math.random() * (cellnumber - 2));
				var randomy = Math.floor(Math.random() * (cellnumber - 2));
				$("#drops .cell" + randomx + randomy).addClass("life");
				cells[randomx][randomy]["life"] = 1;
			}


			function movecell(direction, x, y, step) {
				for (let i=0; i<cells[x][y][direction].length; i++) {
					//var xvalue = (x + step) * cellwidht + (x + step) * 1 + cellpadleft;
					//var yvalue = (y + step) * cellwidht + (y + step) * 1 + cellpadleft;

					if(direction == "l") {
						var time = celltime * (x + 1);
						var xvalue = -1 * (cellwidht - cellpadleft);
					}
					if(direction == "r") {
						var time = celltime * (cellnumber - x);
						var xvalue = cellnumber * (cellwidht + 1) + cellpadleft;
					}
					if(direction == "t") {
						var time = celltime * (y + 1);
						var yvalue = -1 * (cellwidht - cellpadleft);
					}
					if(direction == "b") {
						var time = celltime * (cellnumber - y);
						var yvalue = cellnumber * (cellwidht + 1) + cellpadleft;
					}


					var cell = cells[x][y][direction][i];

					cells[x][y][direction].splice(i,1);

					

					if (direction == "l") {
						//$("#drops").animate({left: "0px"}, celltime, "linear", function() {});

						setTimeout('checkcell(' + (x-1) + ', ' + y + ', ' + cell + ', "' + direction + '", ' + i + ');', celltime);

						if(movedcells[cell] == 0) {
							$("#drops .dropl" + cell).animate({left: xvalue + "px"}, time, "linear", function() {});
						}else{
							movedcells[cell] = 1;
						}
						
					}
					if (direction == "r") {
						//$("#drops").animate({left: "0px"}, celltime, "linear", function() {checkcell(x+1, y, cell, direction, i);});

						setTimeout('checkcell(' + (x+1) + ', ' + y + ', ' + cell + ', "' + direction + '", ' + i + ');', celltime);

						if(movedcells[cell] == 0) {
							$("#drops .dropr" + cell).animate({left: xvalue + "px"}, time, "linear", function() {});
						}else{
							movedcells[cell] = 1;
						}
						
					}
					if (direction == "t") {
						// $("#drops").animate({left: "0px"}, celltime, "linear", function() {checkcell(x, y-1, cell, direction, i);});

						setTimeout('checkcell(' + x + ', ' + (y-1) + ', ' + cell + ', "' + direction + '", ' + i + ');', celltime);

						if(movedcells[cell] == 0) {
							$("#drops .dropt" + cell).animate({top: yvalue + "px"}, time, "linear", function() {});
						}else{
							movedcells[cell] = 1;
						}
						
					}
					if (direction == "b") {
						// $("#drops").animate({left: "0px"}, celltime, "linear", function() {checkcell(x, y+1, cell, direction, i);});

						setTimeout('checkcell(' + x + ', ' + (y+1) + ', ' + cell + ', "' + direction + '", ' + i + ');', celltime);

						if(movedcells[cell] == 0) {
							$("#drops .dropb" + cell).animate({top: yvalue + "px"}, time, "linear", function() {});
						}else{
							movedcells[cell] = 1;
						}
						
					}
				}
			}


			function checkcell(x, y, cell, direction, i) {
				if(x >= 0 && y >= 0 && x < cellnumber && y < cellnumber) {
					cells[x][y][direction].push(cell);
				}else{
					$("#drops .drop" + direction + cell).remove();
				}

				startexploding();
			}


			function updatecell(x, y) {
				var xvalue = x * cellwidht + x * 1 + cellpadleft;
				var yvalue = y * cellwidht + y * 1 + cellpadleft;

				var val = cells[x][y]["v"];

				if(val > 0 && val < 4) {
					$("#drops .drop" + x + y).remove();
					$("#drops").append('<div class="drop type' + val + ' drop' + x + y + '" style="left: ' + xvalue + 'px; top: ' + yvalue + 'px;"></div>');
				}
			}


			function explodecell(x, y) {
				pop();
				exploded = exploded + 1;
				setscore(exploded * 2);

				// var value = (Math.ceil(exploded / 16) - 1);
				// if(value >= 1){pop(2);}
				// lives = lives + value;
				lives = lives + 1;
				$("#lives").html(lives);
				

				var xvalue = x * cellwidht + x * 1 + cellpadleft;
				var yvalue = y * cellwidht + y * 1 + cellpadleft;

				$("#drops .drop" + x + y).remove();
				$("#drops").append('<div class="drop movingdrop type1 dropl' + cell + '" style="left: ' + xvalue + 'px; top: ' + yvalue + 'px;"></div>');
				cells[x][y]["l"].push(cell);
				movedcells[cell] = 0; cell = cell + 1;
				$("#drops").append('<div class="drop movingdrop type1 dropr' + cell + '" style="left: ' + xvalue + 'px; top: ' + yvalue + 'px;"></div>');
				cells[x][y]["r"].push(cell);
				movedcells[cell] = 0; cell = cell + 1;
				$("#drops").append('<div class="drop movingdrop type1 dropt' + cell + '" style="left: ' + xvalue + 'px; top: ' + yvalue + 'px;"></div>');
				cells[x][y]["t"].push(cell);
				movedcells[cell] = 0; cell = cell + 1;
				$("#drops").append('<div class="drop movingdrop type1 dropb' + cell + ' " style="left: ' + xvalue + 'px; top: ' + yvalue + 'px;"></div>');
				cells[x][y]["b"].push(cell);
				movedcells[cell] = 0; cell = cell + 1;

				cells[x][y]["v"] = 0;
			}


			function pop(i = ""){
				var audio=document.getElementById('popaudio' + i);
				try{if(audio.canPlayType){audio.currentTime=0; audio.play();}}
				catch(ex){}
			}


		</script>
		


		<audio id="popaudio" style="display:none;">
			<source src="pop.mp3" type="audio/mpeg" />
		</audio>
		<audio id="popaudio1" style="display:none;">
			<source src="pop1.mp3" type="audio/mpeg" />
		</audio>
		<audio id="popaudio2" style="display:none;">
			<source src="pop2.mp3" type="audio/mpeg" />
		</audio>
		<audio id="popaudio3" style="display:none;">
			<source src="pop3.mp3" type="audio/mpeg" />
		</audio>
		<audio id="popaudio4" style="display:none;">
			<source src="pop4.mp3" type="audio/mpeg" />
		</audio>
		<audio id="popaudio5" style="display:none;">
			<source src="pop5.mp3" type="audio/mpeg" />
		</audio>
		

</body>
</html>
