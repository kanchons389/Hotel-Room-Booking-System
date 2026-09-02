document.addEventListener("DOMContentLoaded", function () {

    var roomStatusBody = document.getElementById("roomStatusBody");
    var roomSearch = document.getElementById("roomSearch");

    if (!roomStatusBody) {
        return;
    }

    var allRooms = [];

    function renderRooms(rooms) {

        if (!rooms || rooms.length === 0) {
            roomStatusBody.innerHTML = "<tr><td colspan='5'>No rooms found.</td></tr>";
            return;
        }

        var html = "";

        rooms.forEach(function (room) {
            html += "<tr>";
            html += "<td>" + escapeHtml(room.room_number) + "</td>";
            html += "<td>" + escapeHtml(room.floor) + "</td>";
            html += "<td>" + escapeHtml(room.room_type) + "</td>";
            html += "<td><span class='status-badge status-" + escapeHtml(room.status) + "'>" + escapeHtml(capitalize(room.status)) + "</span></td>";
            html += "<td>" + (room.notes ? escapeHtml(room.notes) : "-") + "</td>";
            html += "</tr>";
        });

        roomStatusBody.innerHTML = html;
    }

    function capitalize(str) {
        if (!str) {
            return "";
        }
        return String(str).charAt(0).toUpperCase() + String(str).slice(1).replace(/_/g, " ");
    }

    function escapeHtml(value) {
        if (value === null || value === undefined) {
            return "";
        }

        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function loadRooms() {

        roomStatusBody.innerHTML = "<tr><td colspan='5'>Loading rooms...</td></tr>";

        fetch("../../api/housekeeping_rooms.php")
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                allRooms = data;
                renderRooms(allRooms);
            })
            .catch(function () {
                roomStatusBody.innerHTML = "<tr><td colspan='5'>Failed to load rooms.</td></tr>";
            });
    }

    if (roomSearch) {
        roomSearch.addEventListener("input", function () {
            var keyword = roomSearch.value.trim().toLowerCase();

            if (keyword === "") {
                renderRooms(allRooms);
                return;
            }

            var filtered = allRooms.filter(function (room) {
                return (
                    String(room.room_number).toLowerCase().indexOf(keyword) !== -1 ||
                    String(room.room_type).toLowerCase().indexOf(keyword) !== -1 ||
                    String(room.floor).toLowerCase().indexOf(keyword) !== -1 ||
                    String(room.status).toLowerCase().indexOf(keyword) !== -1
                );
            });

            renderRooms(filtered);
        });
    }

    loadRooms();
});