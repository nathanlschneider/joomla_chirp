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
use Joomla\CMS\Session\Session;

JHtml::_('behavior.formvalidator');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
// $wa->registerAndUseScript('chirp', 'media/com_chirp/js/chirpDefault.min.js', [], ['defer' => true], ['core']);
$wa->registerAndUseStyle('chirp', 'media/com_chirp/css/chirpDefault.min.css', [], [], []);

$param = ComponentHelper::getParams('com_chirp');
$shop = $param->get('shops');
$configForm = Form::getInstance("config", JPATH_COMPONENT_ADMINISTRATOR . "/forms/config.xml", array("control" => "config"));
$configForm->bind($param);
$anyShops = $this->getModel()->checkForInstalledShops();

if ($anyShops)
{
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

	<header id="chirp_header">
		<div class="chirp_tabs">
			<div id="chirp_tab_a" class="chirp_tab active">Control Panel</div>
			<div id="chirp_tab_b" class="chirp_tab">Chirp Settings</div>
			<div id="chirp_tab_c" class="chirp_tab">Customizations</div>
		</div>
	</header>
	<div class="chirp_tab_content active" id="chirp_screen1">
		<div id="chirp_main_content">
			<div id="chirp_dash_top">
				<div class="chirp_dash_item">
					<h2><?php echo $clicksToday; ?></h2>
					<p>Clicks Today</p>
				</div>
				<div class="chirp_dash_item">
					<h2><?php echo $clicksThisWeek; ?></h2>
					<p>This Week</p>
				</div>
				<div class="chirp_dash_item">
					<h2><?php echo $averageClicks; ?></h2>
					<p>Click Avg</p>
				</div>
			</div>
			<div id="chirp_activator">
				<h2>Activation</h2>
				<?php echo $configForm->renderFieldset('activation') ?>
				<div role="button" class="chirp_tab" name="chirp_activator_btn" id="chirp_activator_btn">Activate</div>
			</div>
		</div>
	</div>
	<div class="chirp_tab_content" id="chirp_screen2">
		<form action="" method="post" class="chirpForm form-validate" name="chirpSettings" id="chirpSettings" enctype="multipart/form-data">
			<?php echo $configForm->renderFieldset('chirpSettings') ?>
		</form>
	</div>

	<div class="chirp_tab_content" id="chirp_screen3">
		<form action="" method="post" class="chirpForm" name="customSettings" id="customSettings" enctype="multipart/form-data">
			<div id="chirpCustomPreview">
				<div class="chirp" id="chirpTestAlert">
					<div id='chirpProductImage' style="font-size:3rem">🐦</div>
					<div id='chirpTopLine'>Adrienne G. from New Orleans<br />just purchased a <a href="#">product</a>!</div>
					<div id='chirpBottomLine'>Just Now</div>
					<div class="wrapper">
						<div class="pie spinner"></div>
						<div class="pie filler"></div>
						<div class="mask"></div>
					</div>
				</div>
			</div>
			<div id="chirpCustomColors">
				<?php echo $configForm->renderFieldset('customSettings'); ?>
				<label for="backgroundcolor">Background Color</label>
				<input type="color" id="backgroundcolor" name="backgroundcolor" value="#ff0000">
				<label for="textcolor">Text Color</label>
				<input type="color" id="textcolor" name="textcolor" value="#ff0000">
				<label for="bordercolor">Border Color</label>
				<input type="color" id="bordercolor" name="bordercolor" value="#ff0000">
				<label for="linkcolor">Link Color</label>
				<input type="color" id="linkcolor" name="linkcolor" value="#ff0000">
			</div>
		</form>
	</div>
	<script>
		const root = document.querySelector(':root');
		// root.style.setProperty('--font-family', 'Verdana');
		// --chirp_height: 100px;
		// --chirp_width: 400px;
		// --chirp_border-radius: 10px;
		// --chirp_background-color: #fff;
		// --chirp_color: #000;
		// --chirp_border-color: rgba(0, 0, 0, 0.22);

		function initializeColorPicker(colorPickerId, configFieldId, cssVar) {
			const colorPicker = document.querySelector(`#${colorPickerId}`);
			const colorHidden = document.querySelector(`#${configFieldId}`);

			colorPicker.value = `#${colorHidden.value}`;
			root.style.setProperty(cssVar, colorPicker.value);

			colorPicker.addEventListener('input', function(event) {
				colorHidden.value = event.target.value;
				root.style.setProperty(cssVar, event.target.value);
			});
		}

		initializeColorPicker("backgroundcolor", "config_backgroundcolor", "--chirp_background-color");
		initializeColorPicker("bordercolor", "config_bordercolor", "--chirp_border-color");
		initializeColorPicker("textcolor", "config_textcolor", "--chirp_color");
		initializeColorPicker("linkcolor", "config_linkcolor", "--chirp_link-color");


		const allInputs = document.querySelectorAll("input");
		const allSelects = document.querySelectorAll("select");
		const dialog = document.querySelector("dialog");

		allSelects.forEach(select => {
			select.addEventListener("change", () => {
				doChirpSaveConfig();
			})
		})

		allInputs.forEach((input) => {
			input.addEventListener("change", () => {
				doChirpSaveConfig()
			});
		});

		let doChirpSaveConfig = async () => {
			const configNames = document.querySelectorAll('[name^="config["]');

			let chirpConfigObj = {};

			configNames.forEach((param) => {
				if (param.multiple) {
					let paramArray = [];
					[...param.children].forEach((child) => {
						if (child.selected) {
							paramArray.push(child.value);
						}
					});
					chirpConfigObj[param.id.split("config_")[1]] = paramArray;
				} else if (param.checked === true) {
					chirpConfigObj[param.id.split("config_")[1].slice(0, -1)] = param.value;
				} else {
					chirpConfigObj[param.id.split("config_")[1]] = param.value;
				}
			});

			const chirpConfig = {
				data: chirpConfigObj,
			};

			console.log(chirpConfig);

			const fetched = await fetch("/administrator/index.php?option=com_chirp&task=controlpanels.saveConfig", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					"X-CSRF-Token": "<?php echo Session::getFormToken(); ?>",
				},
				body: JSON.stringify(chirpConfig),
			});

			const res = await fetched.text();
			console.log(res);

			if (typeof res === "string") {
				setTimeout(() => {
					dialog.close();
				}, 1000);
			}
		};

		const chirpTabs = document.querySelectorAll(".chirp_tab"); //
		const chirpTabsParent = document.querySelector(".chirp_tabs");

		chirpTabsParent.addEventListener("click", (e) => {
			const chirpTabsParentChildren = Array.from(chirpTabsParent.children);

			chirpTabsParentChildren.forEach((child) => child.classList.remove("active"));

			e.target.classList.add("active");

			showScreen(chirpTabsParentChildren.indexOf(e.target));
		});

		function showScreen(screenIndex) {
			// Hide all screens
			const screens = document.querySelectorAll(".chirp_tab_content");
			screens.forEach((screen) => screen.classList.remove("active"));

			// Show the selected screen
			const selectedScreen = document.getElementById(`chirp_screen${screenIndex + 1}`);
			selectedScreen.classList.add("active");
		}
	</script>
<?php
}
else
{
?>

	<div id="noShopNotice">
		<h1>Sorry! You have no compatible shop extentions installed.</h2>
			<p>
				Chirp only current supports <a href="">Eshop</a>, <a href="">Easyshop</a>, <a href="">Hikashop</a> and <a href="">Phocacart</a>.
			</p>
			<p>Please contact us <a href="">here</a> if you need to have a different shop extention conisdered.</p>
			<p>
			<a href=""><h3>quirkable.io/chirp</h3></a>
			</p>
	</div>
<?php
}
