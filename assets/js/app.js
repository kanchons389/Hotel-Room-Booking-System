document.addEventListener("DOMContentLoaded", function () {

    const searchInputs = document.querySelectorAll(".table-search");

    searchInputs.forEach(function (input) {

        input.addEventListener("keyup", function () {

            let filter = input.value.toLowerCase();

            let table = input
                .closest(".dashboard-table")
                .querySelector("table");

            let rows = table.querySelectorAll("tbody tr");

            rows.forEach(function (row) {

                let text = row.innerText.toLowerCase();

                if (text.includes(filter)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }

            });

        });

    });

});