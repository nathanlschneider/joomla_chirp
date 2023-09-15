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

            let userNameStr, locationStr, chirpMessage, aOrAn;

            if (
                typeof data.userName === "string" &&
                data.userName.includes(" ")
            ) {
                userNameStr = data.userName.split(" ")[0];
            } else {
                userNameStr = data.userName;
            }

            if (typeof data.userCity !== "string") {
                locationStr = "";
            } else {
                locationStr = ` from ${data.userCity}`;
            }

            if (
                typeof data.productName === "string" &&
                ["a", "e", "i", "o", "u"].some(
                    (vowel) => data.productName[0] === vowel
                )
            ) {
                aOrAn = "an";
            } else {
                aOrAn = "a";
            }

            chirpMessage = `${userNameStr}${locationStr} ordered ${aOrAn} <a href='${data.productLink}'><strong>${data.productName}</strong></a>!`;

            chirp.innerHTML = `
            <div id='chirpProductImage'><img src='${data.productImage}' alt='' /></div>
            <div id='chirpTopLine'>${chirpMessage}</div>
            <div id='chirpBottomLine'>Just Now</div>
            <div class="wrapper" style="--animation-duration: ${settings.chirpshowlength}s">
            <div class="pie spinner"></div>
            <div class="pie filler"></div>
            <div class="mask"></div>
            </div> 
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
            }, settings.chirpshowlength * 1000);
        };
    });
})();
