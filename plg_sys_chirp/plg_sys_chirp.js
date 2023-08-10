// (c) 2023 Quirkable. All Rights Reserved.
// https://quirkable.io/

(function () {
  window.addEventListener("DOMContentLoaded", function () {
    const body = document.querySelector("body");

    const fetched = async () => {
      const response = await fetch("/index.php?options=com_ajax&plugin=plg_ajax_chirp&view=raw", {
        method: "GET",
        mode: "same-origin",
        redirect: "follow",
      });
      const result = await response.json();

      return DOMPurify.sanitize(`${result.results[0].name.first} ${result.results[0].name.last.substring(0, 1)}`);
    };

    if (!body.classList.contains("admin")) {
      const div = document.createElement("div");
      div.id = "qrk_root";
      body.appendChild(div);
    }

    const popper = () => {
      const root = document.getElementById("qrk_root");
      const div =  document.createElement("div");
      div.classList.add('qrk_text');
      root.appendChild(div);
      fetched().then((name) => {
         div.innerText = `${name}`;
         div.classList.add('qrk_move_in');
        });
    }

    // time loop
    let timer = setTimeout(() => {
         

         

      if (root) {
        
      }
    }, 5000);

    //
  });
})();
