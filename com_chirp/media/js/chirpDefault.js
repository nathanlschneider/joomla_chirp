const allInputs = document.querySelectorAll("input");
const dialog = document.querySelector("dialog");

allInputs.forEach((input) => {
	input.addEventListener("change", () => {
		
		doChirpSaveConfig()
		
	});
});

doChirpSaveConfig = async () => {
	const configNames = document.querySelectorAll('[name^="config["]');
	
	dialog.showModal();

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
		} else if (param.checked === false) {
		} else {
			chirpConfigObj[param.id.split("config_")[1]] = JSON.stringify(param.value);
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
