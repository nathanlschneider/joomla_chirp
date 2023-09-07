// (c) 2023 Quirkable. All Rights Reserved.
// https://quirkable.io/

(function () {
    window.addEventListener("DOMContentLoaded", function () {
        const body = document.querySelector("body");

        if (body.classList.contains("admin")) return;

        let settings = {};
        const chirpNest = document.querySelector("#chirp_nest");
        const options = { method: "GET" };
        const div = document.createElement("div");
        div.id = "chirp_nest";
        body.appendChild(div);

        async function getSettings() {
            try {
                const fetched = await fetch(
                    "/index.php?option=com_chirp&task=api.settings",
                    options
                );
                return await fetched.json();
            } catch (e) {
                // e for error - we'll need to try again later or load a backup config.
                console.log(e);
            }
        }

        getSettings().then((data) => (settings = data));

        const eventSource = new EventSource(
            "/plugins/behaviour/chirp/event.php"
        );
        let lastevent;

        eventSource.addEventListener("alert", (e) => {
            if (lastevent !== e.type) {
                console.log(e);
            }
            lastevent = e.type;
        });
    });
})();
