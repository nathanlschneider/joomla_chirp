// (c) 2023 Quirkable. All Rights Reserved.
// https://quirkable.io/

(function () {
    window.addEventListener("DOMContentLoaded", function () {
        console.log("loaded");
        const body = document.querySelector("body");

        if (!body.classList.contains("admin")) {
            const div = document.createElement("div");
            div.id = "chirp_nest";
            body.appendChild(div);
        }

        const chirpNest = document.querySelector("#chirp_nest");
        let settings = {};
        const randoMinMax = (min, max) => {
            return min + Math.random() * (max - min);
        };

        const sleep = (time) => {
            return new Promise((resolve) => setTimeout(resolve, time));
        };

        async function chirpEngine(callback, mTime, MTime) {
            let randomTime = randoMinMax(mTime, MTime);
            await sleep(randomTime);
            callback();
        }

        const options = {
            method: "GET",
        };

        async function getSettings() {
            try {
                const fetched = await fetch(
                    "/index.php?option=com_chirp&task=api.settings",
                    options
                );
                return await fetched.json();
            } catch (e) {
                // e for error - we'll need to try again later or load a backup config.
            }
        }

        async function chirp() {
            try {
                const fetched = await fetch(
                    "/index.php?option=com_chirp&task=api.name",
                    options
                );
                return await fetched.json();
            } catch (e) {
                // I am error. Maybe we should tell the dev I did a broken?
            }
        }

        getSettings().then((data) => (settings = data));

        chirp().then((json) => {
            if (json.status === "success") {
                chirpNest.innerText = json.name;
                chirpEngine(chirp, settings.minTime, settings.maxTime);
            }
        });
    });
})();
