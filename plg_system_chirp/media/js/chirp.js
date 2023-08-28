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
    });

    /**
     *  timerTrigger function - returns a random integer for the chirp timer
     *
     */
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

    /**
     *  options object - Fetch options
     *
     */
    const options = {
        method: "GET",
    };

    /**
     * fetch settings and set settings object
     *
     */
    let settings = {};

    async function getSettings() {
        try {
            const fetched = await fetch(
                "/index.php?option=com_chirp&task=api.getSettings",
                options
            );
            settings = await fetched.json();
        } catch (e) {
            // e for error - we'll need to try again later or load a backup config.
        }
    }
    getSettings();

    /**
     * chirp function - fetches chirp from Joomla and inserts it into the DOM
     *
     */
    async function chirp() {
        try {
            const chirpNest = document.querySelector("#chirp_nest");
            const fetched = await fetch(
                "/index.php?option=com_chirp&task=api.getChirp",
                options
            );

            const json = await fetched.json();
            chirpNest.innerText = json;
            chirpEngine(chirp, 10000, 30000);
        } catch (e) {
            // I am error. Maybe we should tell the dev I did a broken?
        }
    }
    chirp();
})();
