<?php

/**
 * @version    CVS: 0.1.0
 * @package    Com_Chirp
 * @author     Nathan Schneider <nathan@quirkable.io>
 * @copyright  2023 Quirkable
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;

$param = ComponentHelper::getParams('com_chirp');
$shop = $param->get('shops');

$clickData = $this->getModel()->clickdata($shop);

$clicksToday = 0;
$clicksThisWeek = 0;
$averageClicks = 0;
$currentDate = new DateTime;
$json = json_decode($clickData);

//["id"]=> int(1) ["click_id"]=> string(18) "lncj0z5rifravd67rj" ["order_id"]=> int(117) ["click_date"]=> string(19) "2023-10-05 01:56:32" ["shop_name"]=> string(5) "eshop" }
foreach ($json as $value)
{
	if (isset($value->click_date))
	{
		$date = new DateTime($value->click_date);

		if ($date->format('Y-m-d') === $currentDate->format('Y-m-d'))
		{
			$clicksToday++;
		}

		if ($date->format('Y-W') === $currentDate->format('Y-W'))
		{
			$clicksThisWeek++;
		}
	}
}



?>
<style>
	@import url(https://fonts.googleapis.com/css?family=Roboto);

	#chart {
		font-family: Roboto, sans-serif;
		max-width: 650px;
		margin: 35px auto;
	}

	#chirp_dash {
		display: grid;
		grid-template-columns: repeat(4, 1fr);
		grid-template-rows: 25% 75%;
	}

	#chirp_header {
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	#chirp_dash_top {
		grid-row: 1;
		grid-column: 1 /span 4;
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 20px;
		height: 275px;
	}

	#chirp_dash_left {
		grid-row: 2;
		grid-column: 1 /span 2;
	}

	#chirp_dash_right {
		grid-row: 2;
		grid-column: 3 /span 4;
	}

	.chirp_data_box {
		width: 25%;
		height: 275px;
		text-align: center;
		background-color: #1888ff;
		color: white;
		padding: 10px;
		position: relative;
		transition: .3s all;
	}

	.disabled {
		background-color: #c5c5c5;
	}

	.chirp_data_box:hover {
		box-shadow: 2px 2px 10px #707c89;
		transform: translateY(5px);
	}

	.chirp_data_box_title {
		font-size: 26px;
		border-bottom: 1px solid #ffffff61;
		padding: 5px 0px 10px;
		font-weight: 300;
		white-space: nowrap;
	}

	.chirp_data_box_clicks {
		position: relative;
		left: 0;
		right: 0;
		bottom: 30px;
		font-size: 11rem;
	}

	.tabs {
		display: flex;
	}

	.tab {
		padding: 10px 20px;
		cursor: pointer;
		border: 1px solid #ccc;
		background-color: #f0f0f0;
	}

	.tab:nth-of-type(2) {
		border-right: none;
		border-left: none;
	}

	.tab.active {
		background-color: #fff;
	}

	.tab_content {
		display: none;
	}

	.tab_content.active {
		display: block;
		animation: fadeIn 0.5s ease-in-out;
	}

	@keyframes fadeIn {
		0% {
			opacity: 0;
		}

		100% {
			opacity: 1;
		}
	}
</style>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<header id="chirp_header">
	<div class="tabs">
		<div class="tab" onclick="showScreen(0)">Dashboard</div>
		<div class="tab" onclick="showScreen(1)">Settings</div>
		<div class="tab" onclick="showScreen(2)">Customizations</div>
	</div>
	<div>
		<h1>Chirp 🐦 Your Ultimate Social Proof Solution for Joomla!</h1>
	</div>
</header>
<hr />
<div class="tab_content active" id="screen1">
	<div id="main_content">
		<section id="chirp_dash">
			<div id="chirp_dash_top">
				<div class="chirp_data_box">
					<div class="chirp_data_box_title">Clicks Today</div>
					<div class="chirp_data_box_clicks"><?php echo $clicksToday; ?></div>
				</div>
				<div class="chirp_data_box">
					<div class="chirp_data_box_title">Clicks This week</div>
					<div class="chirp_data_box_clicks"><?php echo $clicksThisWeek; ?></div>
				</div>
				<div class="chirp_data_box">
					<div class="chirp_data_box_title">Avg. Clicks per Order</div>
					<div class="chirp_data_box_clicks"><?php echo $averageClicks; ?></div>
				</div>
				<div class="chirp_data_box">
					<div class="chirp_data_box_title">Conversions</div>
					<div class="chirp_data_box_clicks"><?php echo $clicksToday; ?></div>
				</div>
			</div>
			<div id="chirp_dash_left">
				<div id="chart_left"></div>
				<script>
					var options = {
						chart: {
							type: 'bar'
						},
						series: [{
							name: 'sales',
							data: [30, 40, 45, 50, 49, 60, 70, 91, 125]
						}],
						xaxis: {
							categories: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998, 1999]
						}
					}
					var chartLeft = new ApexCharts(document.querySelector("#chart_left"), options);
					// chartLeft.render();
				</script>
			</div>
			<div id="chirp_dash_right">
				<div id="chart_right">
					<script>
						var options = {
							chart: {
								type: 'bar'
							},
							series: [{
								name: 'sales',
								data: [30, 40, 45, 50, 49, 60, 70, 91, 125]
							}],
							xaxis: {
								categories: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998, 1999]
							}
						}
						var chartRight = new ApexCharts(document.querySelector("#chart_right"), options);
						// chartRight.render();
					</script>
				</div>
			</div>
		</section>
	</div>
</div>
<div class="tab_content" id="screen2">
	<section class="white_bg">
		<h2>License Activation</h2>
		<input type="text" name="license" placeholder="License" />
		<input type="text" name="email" placeholder="Email" />
		<input type="button" name="Activate" />
	</section>
	<?php


	?>
</div>
<div class="tab_content" id="screen3">
	<h2>Screen 3</h2>
	<p>This is the content of Screen 3.</p>
</div>

<script>
	function showScreen(screenIndex) {
		// Hide all screens
		const screens = document.querySelectorAll('.tab_content');
		screens.forEach(screen => screen.classList.remove('active'));

		// Show the selected screen
		const selectedScreen = document.getElementById(`screen${screenIndex + 1}`);
		selectedScreen.classList.add('active');
	}
</script>
