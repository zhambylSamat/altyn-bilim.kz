<style type="text/css">
	.beautiful-btn {
		border: none;
		font-family: 'Lato';
		font-size: inherit;
		color: inherit;
		background: none;
		cursor: pointer;
		padding: 25px 80px;
		display: inline-block;
		margin: 15px 0px;
		text-transform: uppercase;
		letter-spacing: 1px;
		font-weight: 700;
		outline: none;
		position: relative;
		-webkit-transition: all 0.3s;
		-moz-transition: all 0.3s;
		transition: all 0.3s;
	}

	.beautiful-btn:after {
		content: '';
		position: absolute;
		z-index: -1;
		-webkit-transition: all 0.3s;
		-moz-transition: all 0.3s;
		transition: all 0.3s;
	}

	/* Pseudo elements for icons */
	.beautiful-btn:before {
		font-family: 'FontAwesome';
		speak: none;
		font-style: normal;
		font-weight: normal;
		font-variant: normal;
		text-transform: none;
		line-height: 1;
		position: relative;
		-webkit-font-smoothing: antialiased;
	}


	/* Icon separator */
	.btn-sep {
		padding: 25px 40px 25px 100px;
	}

	.btn-sep:before {
		background: rgba(0,0,0,0.15);
	}

	/* Button 2 */
	.btn-play {
		border-radius: 10px;
		background: #3498db;
		color: #fff;
	}

	.btn-play:hover {
		border-radius: 10px;
		background: #2980b9;
	}

	.btn-play:active {
		border-radius: 10px;
		background: #2980b9;
		top: 2px;
	}

	.btn-play:before {
		border-radius: 10px;
		position: absolute;
		height: 100%;
		left: 0;
		top: 0;
		line-height: 3.5;
		font-size: 140%;
		width: 60px;
	}

	/* Icons */

	.icon-cart:before {
		/*content: "\f04b";*/
		/*content: "\f144";*/
		content: "\f11b";
	}

	.game-text {
		font-size: 18px;
		text-align: center;
		font-family: 'FontAwesome';
		font-weight: bold;
	}
</style>

<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
<link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>

<!-- <div style='background-color: #76EBBD; box-shadow: 0px 0px 10px #5FBD97; margin: 1%;'> -->
	<div class='container'>
		<div class='row'>
			<div style='padding: 1% 2%; border-radius: 10px; border: 1px solid #4FA5BF; background-color: rgba(91,192,222, 0.2); margin-top: 1%;'>
				<!-- #D6E9F3 -->
				<!-- #B4DCEE -->
				<table>
					<tr>
						<td style='width: 50%;' class='hidden-xs'>
							<center>
								<img src="game_card/img/card_sheet.jpg" class='img-responsive' style='max-width: 250px;'>
							</center>	
						</td>
						<td>
							<div style='padding: 10px 20px;'>
								<p class='game-text'>Алгебра, Геометрия және Физика пәндерінен теориялық сұрақтарға ФЛИП-КАРТОЧКАЛАР</p>
								<center>
									<button class="beautiful-btn btn-play btn-sep icon-cart" onclick="window.location.href='game_card'">Ойынды бастау</button>
								</center>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
<!-- </div> -->

<script type="text/javascript">
	(function () {
	  var removeSuccess;

	  removeSuccess = function () {
	    return $('.button').removeClass('success');
	  };

	  $(document).ready(function () {
	    return $('.button').click(function () {
	      $(this).addClass('success');
	      return setTimeout(removeSuccess, 3000);
	    });
	  });

	}).call(this);

</script>