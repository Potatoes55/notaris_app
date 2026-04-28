import PerfectScrollbar from "perfect-scrollbar";
window.PerfectScrollbar = PerfectScrollbar;

// // require('./bootstrap');
// require("./custom");
import { Notyf } from "notyf";
import "notyf/notyf.min.css";

window.notyf = new Notyf({
    duration: 3000,
    position: { x: "right", y: "top" },
});


    function disableAutocomplete() {
        document.querySelectorAll('form').forEach(form => {
            form.setAttribute('autocomplete', 'off');
        });

        document.querySelectorAll('input, textarea').forEach(el => {
            el.setAttribute('autocomplete', 'off');
        });
    }

    document.addEventListener('DOMContentLoaded', disableAutocomplete);

    // penting kalau pakai Livewire / AJAX / dynamic DOM
    document.addEventListener('livewire:navigated', disableAutocomplete);
