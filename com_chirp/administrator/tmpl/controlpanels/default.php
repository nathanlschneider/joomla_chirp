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

use Joomla\CMS\Form\Form;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;

$input = Factory::getApplication()->input;
$requestData = $input->request;
$paramValue = $input->get('myform[customFallback]', '');

if ($paramValue !== '')
{
	echo 'eat it!';
	die;
}

$param = ComponentHelper::getParams('com_chirp');
$shop = $param->get('shops');

$clickData = $this->getModel()->clickdata($shop);
$rowCount = $this->getModel()->getRowCount($shop);

$clicksToday = 0;
$clicksThisWeek = 0;
$allClicks = count(json_decode($clickData, true));

if ($allClicks === 0)
{
	$averageClicks = 0;
}
else
{
	$averageClicks = $rowCount / $allClicks;
}

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

	#chirp_tagline {
		text-align: right;
	}

	#chirp_dash {
		display: grid;
		grid-template-columns: repeat(4, 1fr);
		grid-template-rows: 25% 75%;
	}

	#chirp_header {
		display: flex;
		align-items: center;
		justify-content: center;
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
		border-radius: 5px;
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

	.chirp_tabs {
		display: flex;
		box-shadow: 0 2px 10px -8px #4d79b3;
		margin-top: 40px;
		margin-bottom: 40px;
	}

	.chirp_tab {
		padding: 10px 20px;
		cursor: pointer;
		border: 1px solid #ccc;
		background-color: #fff;
		text-align: center;
		display: flex;
		align-items: center;
		justify-content: center;
		width: 160px;
	}

	.chirp_tab:nth-of-type(2) {
		border-right: none;
		border-left: none;
	}

	.chirp_tab.active {
		background-color: #457d54;
		color: #fff;
		font-weight: 500;
	}

	.chirp_tab_content {
		display: none;
	}

	.chirp_tab_content.active {
		display: block;
		animation: chirpFadeIn 0.5s ease-in-out;
	}

	#subhead-container {
		display: none;
	}

	.chirp_form,
	#chirp_dash {
		box-shadow: 0 2px 10px -8px #4d79b3;
		background-color: #fff;
		padding: 40px;
		border-radius: 5px;
	}

	#chirp_tab_a {
		border-top-left-radius: 5px;
		border-bottom-left-radius: 5px;
	}

	#chirp_tab_c {
		border-top-right-radius: 5px;
		border-bottom-right-radius: 5px;
	}

	@keyframes chirpFadeIn {
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
	<div class="chirp_tabs">
		<div id="chirp_tab_a" class="chirp_tab active">Dashboard</div>
		<div id="chirp_tab_b" class="chirp_tab">Shop Settings</div>
		<div id="chirp_tab_c" class="chirp_tab">Customizations</div>
	</div>
</header>
<div class="chirp_tab_content active" id="chirp_screen1">
	<h2>Dashboard</h2>
	<!-- <h2 id="chirp_tagline">Chirp! 🐦 Your Ultimate Social Proof Solution for Joomla!</h2> -->
	<div id="chirp_main_content">
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
					<div class="chirp_data_box_title">All time clicks</div>
					<div class="chirp_data_box_clicks"><?php echo $allClicks; ?></div>
				</div>
			</div>
			<div id="chirp_dash_left">
				<div id="chart_left"></div>

			</div>
			<div id="chirp_dash_right">
				<div id="chirp_chart_right">

				</div>
			</div>
		</section>
	</div>
</div>
<div class="chirp_tab_content" id="chirp_screen2">
	<h2>Shop Settings</h2>
	<form action="" method="post" class="chirp_form" name="shopSettingsForm" id="shopSettingsForm" enctype="multipart/form-data">

		<nav aria-label="Toolbar">
			<div class="btn-toolbar d-flex" role="toolbar" id="toolbar">
				<joomla-toolbar-button id="toolbar-apply" task="component.apply" form-validation="">
					<button class="button-apply btn btn-success" type="button">
						<span class="icon-apply" aria-hidden="true"></span>
						Save</button>
				</joomla-toolbar-button>
				<joomla-toolbar-button id="toolbar-help" class="ms-auto">
					<button class="button-help btn btn-info js-toolbar-help-btn" data-url="help/en-GB/.html" data-title="Chirp! Help" data-width="700" data-height="500" data-scroll="1" type="button" title="Opens in a new window">
						<span class="icon-question" aria-hidden="true"></span>
						Help</button>
				</joomla-toolbar-button>
			</div>
		</nav>
	</form>
</div>

<div class="chirp_tab_content" id="chirp_screen3">
	<h2>Customizations</h2>
	<form action="" method="post" class="chirp_form" name="customizationForm" id="customizationForm" enctype="multipart/form-data">
		<?php $form = Form::getInstance("shopSettingsForm", JPATH_COMPONENT_ADMINISTRATOR . "/forms/shopSettingsForm.xml", array("control" => "myform")) ?>

		<div class="chirp_section_row">
			<?php echo $form->renderField('myimage') ?>
		</div>
		<div class="chirp_section_row">
		</div>
		<div class="chirp_section_row">
		</div>
		<div class="chirp_section_row">
		</div>

		<nav aria-label="Toolbar">
			<div class="btn-toolbar d-flex" role="toolbar" id="toolbar">
				<joomla-toolbar-button id="toolbar-apply" task="component.apply" form-validation="">
					<button id="chirpSaveBtn" class="button-apply btn btn-success" type="button">
						<span class="icon-apply" aria-hidden="true"></span>
						Save</button>
				</joomla-toolbar-button>
				<joomla-toolbar-button id="toolbar-help" class="ms-auto">
					<button class="button-help btn btn-info js-toolbar-help-btn" data-url="help/en-GB/.html" data-title="Chirp! Help" data-width="700" data-height="500" data-scroll="1" type="button" title="Opens in a new window">
						<span class="icon-question" aria-hidden="true"></span>
						Help</button>
				</joomla-toolbar-button>
			</div>
		</nav>
	</form>
</div>

<script>
	const chirpSaveBtn = document.querySelector("#chirpSaveBtn");
	const chirpTabs = document.querySelectorAll('.chirp_tab');
	const chirpTabsParent = document.querySelector('.chirp_tabs');

	chirpTabsParent.addEventListener('click', (e) => {

		const chirpTabsParentChildren = Array.from(chirpTabsParent.children);

		chirpTabsParentChildren.forEach(child => child.classList.remove('active'));

		e.target.classList.add('active');

		showScreen(chirpTabsParentChildren.indexOf(e.target));
	})

	chirpSaveBtn.addEventListener('click', (e) => {
		doChirpForm();
	})

	async function doChirpForm() {
		const fetched = await fetch('/administrator/index.php?option=com_chirp&view=controlpanels', {
			method: 'POST'
		});
		const text = await fetched.text();
		alert(text);
	}

	function showScreen(screenIndex) {
		// Hide all screens
		const screens = document.querySelectorAll('.chirp_tab_content');
		screens.forEach(screen => screen.classList.remove('active'));

		// Show the selected screen
		const selectedScreen = document.getElementById(`chirp_screen${screenIndex + 1}`);
		selectedScreen.classList.add('active');
	}
</script>
