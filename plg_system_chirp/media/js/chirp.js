// (c) 2023 Quirkable. All Rights Reserved.
// https://quirkable.io/

(function () {
    window.addEventListener("DOMContentLoaded", function () {
        console.log('loaded')
        const body = document.querySelector("body");

        if (!body.classList.contains("admin")) {
            const div = document.createElement("div");
            div.id = "chirp_nest";
            body.appendChild(div);
        }
    });
})();
