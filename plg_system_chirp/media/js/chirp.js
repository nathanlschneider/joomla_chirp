// (c) 2023 Quirkable. All Rights Reserved.
// https://quirkable.io/

(function () {
    window.addEventListener("DOMContentLoaded", function () {
        const body = document.querySelector("body");

        if (body.classList.contains("admin")) return;

        let settings = {};
        const options = { method: "GET" };

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
                showChirp(JSON.parse(e.data));
            }
            lastevent = e.type;
        });

        const showChirp = (data) => {
            const alertSound = new Audio(
                `/media/plg_system_chirp/wav/${settings.notificationsound}.wav`
            );

            const chirp = document.createElement("div");
            chirp.classList.add("chirp");

            chirp.innerHTML = `
            <div id='chirpProductImage'><img src='${data.productImage}' alt='' /></div>
            <div id='chirpTopLine'>${data.userName} from ${data.userCity} just ordered a <strong>${data.productName}</strong>!</div>
            <div id='chirpBottomLine'>Just Now</div>
            `;

            body.prepend(chirp);

            chirp.addEventListener("click", () => {
                chirp.classList.remove("showChirp");
            });

            setTimeout(() => {
                chirp.classList.add("showChirp");
                if (settings.sound) alertSound.play();
                // https://freesound.org/people/hollandm/sounds/692819/
                // https://jazzy.junggle.net/
            }, 1000);

            setTimeout(() => {
                chirp.classList.remove("showChirp");
                // chirp.innerHTML = "";
            }, settings.chirpshowlength * 1000);
        };
    });
})();
