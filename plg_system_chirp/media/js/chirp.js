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

        getSettings().then((data) => {
            settings = data;
        });

        const eventSource = new EventSource(
            "/plugins/behaviour/chirp/event.php"
        );

        eventSource.addEventListener("alert", (e) => {
            showChirp(JSON.parse(e.data));
        });

        const generateUniqueID = () => {
            const timestamp = Date.now().toString(36); // Convert current timestamp to base36
            const randomString = Math.random().toString(36).substring(2, 15); // Generate a random string

            return timestamp + randomString;
        };

        const trackClick = (returnData, uid) => {
            if (typeof returnData === "object" && typeof uid === "string") {
                const postData = {
                    returnData: returnData,
                    uniqId: uid,
                };

                const options = {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(postData),
                };

                try {
                    fetch(
                        "/index.php?option=com_chirp&task=api.track",
                        options
                    );
                } catch (e) {
                    console.error(e);
                }
            }
        };

        const setCookie = (name, value, daysToExpire) => {
            const date = new Date();
            date.setTime(date.getTime() + (daysToExpire * 24 * 60 * 60 * 1000)); // Calculate the expiration date
            const expires = "expires=" + date.toUTCString();
            document.cookie = name + "=" + value + ";" + expires + ";path=/";
        }

        const showChirp = (data) => {
            console.log(data);
            
            const clickId = generateUniqueID();

            setCookie('clickRef', clickId, 1);

            const alertSound = new Audio(
                `/media/plg_system_chirp/wav/${settings.notificationsound}.wav`
            );

            let userNameStr, locationStr, chirpMessage, aOrAn;

            let chirp = document.querySelector(".chirp");

            if (chirp === null) {
                chirp = document.createElement("div");
                chirp.classList.add("chirp");
                body.prepend(chirp);
            }

            const setChirpLocation = () => {
                chirp.style.left = "unset";
                chirp.style.right = "unset";
                chirp.style.top = "unset";
                chirp.style.bottom = "unset";
                switch (settings.notificationlocation) {
                    case "top-left":
                        chirp.style.top = "20px";
                        chirp.style.left = "20px";
                        break;
                    case "top-center":
                        chirp.style.top = "20px";
                        chirp.style.left = `calc(50vw - ${
                            chirp.getBoundingClientRect().width / 2
                        }px)`;
                        break;
                    case "top-right":
                        chirp.style.top = "20px";
                        chirp.style.right = "20px";
                        break;
                    case "center-right":
                        chirp.style.top = `${
                            window.innerHeight / 2 -
                            chirp.getBoundingClientRect().height / 2
                        }px`;
                        chirp.style.right = "20px";
                        break;
                    case "bottom-right":
                        chirp.style.bottom = "20px";
                        chirp.style.right = "20px";
                        break;
                    case "bottom-center":
                        chirp.style.bottom = "20px";
                        chirp.style.left = `calc(50vw - ${
                            chirp.getBoundingClientRect().width / 2
                        }px)`;
                        break;
                    case "bottom-left":
                        chirp.style.left = "20px";
                        chirp.style.bottom = "20px";
                        break;
                    case "center-left":
                        chirp.style.top = `${
                            window.innerHeight / 2 -
                            chirp.getBoundingClientRect().height / 2
                        }px`;
                        chirp.style.left = "20px";
                        break;
                }
            };

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

            chirpMessage = `${userNameStr}${locationStr} ordered ${aOrAn} <a href='${data.productLink}&clickRef=${clickId}'><strong>${data.productName}</strong></a>!`;

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

            chirp.addEventListener("click", () => {
                chirp.classList.remove("showChirp");
                trackClick(data, clickId);
            });
            // settings.notificationlocation.split(":")[0]
            setTimeout(() => {
                setChirpLocation();
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
