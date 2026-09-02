<?php

require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../models/roomModel.php";

$rooms = getAllRooms();
$roomTypes = getAllRoomTypes();

$editData = null;

if (isset($_GET["edit"])) {
    $editData = getRoomById($_GET["edit"]);
}

require_once "../../app/layouts/header.php";
require_once "../../app/layouts/sidebar.php";
require_once "../../app/layouts/navbar.php";

?>

<div class="main-content">

    <div class="dashboard-title">
        <h1>Room Management</h1>
        <p>Manage hotel rooms, status, and room assignments.</p>
    </div>

    <?php
    if (isset($_SESSION["success"])) {
        echo "<div class='success-message'>" . $_SESSION["success"] . "</div>";
        unset($_SESSION["success"]);
    }

    if (isset($_SESSION["error"])) {
        echo "<div class='error'>" . $_SESSION["error"] . "</div>";
        unset($_SESSION["error"]);
    }
    ?>

    <div class="form-box">

        <h2><?php echo $editData ? "Edit Room" : "Add New Room"; ?></h2>

        <form action="../../controllers/roomController.php" method="POST">

            <input type="hidden"
                   name="action"
                   value="<?php echo $editData ? "update_room" : "add_room"; ?>">

            <?php if ($editData) { ?>
                <input type="hidden" name="id" value="<?php echo $editData["id"]; ?>">
            <?php } ?>

            <div class="form-row">

                <div class="form-group">
                    <label>Room Type</label>

                    <select name="room_type_id" required>

                        <option value="">Select Room Type</option>

                        <?php
                        if ($roomTypes && $roomTypes->num_rows > 0) {

                            while ($type = $roomTypes->fetch_assoc()) {

                                $selected = "";

                                if ($editData && $editData["room_type_id"] == $type["id"]) {
                                    $selected = "selected";
                                }

                                echo "<option value='" . $type["id"] . "' $selected>";
                                echo $type["name"];
                                echo "</option>";
                            }
                        }
                        ?>

                    </select>
                </div>

                <div class="form-group">
                    <label>Room Number</label>

                    <input type="text"
                           name="room_number"
                           required
                           value="<?php echo $editData ? $editData["room_number"] : ""; ?>">
                </div>

            </div>

            <div class="form-row">

                <div class="form-group">
                    <label>Floor</label>

                    <input type="number"
                           name="floor"
                           required
                           value="<?php echo $editData ? $editData["floor"] : ""; ?>">
                </div>

                <div class="form-group">
                    <label>Status</label>

                    <select name="status" required>

                        <?php
                        $statuses = ["available", "occupied", "dirty", "maintenance", "blocked"];

                        foreach ($statuses as $status) {

                            $selected = "";

                            if ($editData && $editData["status"] == $status) {
                                $selected = "selected";
                            }

                            echo "<option value='$status' $selected>";
                            echo ucfirst($status);
                            echo "</option>";
                        }
                        ?>

                    </select>
                </div>

            </div>

            <div class="form-group">

                <label>Notes</label>

                <textarea name="notes"
                          placeholder="Additional room notes..."><?php echo $editData ? $editData["notes"] : ""; ?></textarea>

            </div>

            <button type="submit">

                <?php
                echo $editData ? "Update Room" : "Add Room";
                ?>

            </button>

            <?php if ($editData) { ?>

                <a href="rooms.php" class="cancel-link">
                    Cancel Edit
                </a>

            <?php } ?>

        </form>

    </div>

    <div class="table-search-box">
    <input
        type="text"
        class="table-search"
        placeholder="Search table data..."
    >
</div>

<br>

        <h2>All Rooms</h2>

        <br>

        <table>

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Room Number</th>
                    <th>Room Type</th>
                    <th>Floor</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Action</th>
                </tr>

            </thead>

            <tbody>

                <?php

                if ($rooms && $rooms->num_rows > 0) {

                    while ($room = $rooms->fetch_assoc()) {

                        echo "<tr>";

                        echo "<td>" . $room["id"] . "</td>";

                        echo "<td>" . $room["room_number"] . "</td>";

                        echo "<td>" . $room["room_type"] . "</td>";

                        echo "<td>Floor " . $room["floor"] . "</td>";

                       echo "<td>
        <span class='status-badge status-" . $room["status"] . "'>
            " . ucfirst($room["status"]) . "
        </span>
      </td>";

                        echo "<td>" . $room["notes"] . "</td>";

                        echo "<td>

                            <a class='edit-btn'
                               href='rooms.php?edit=" . $room["id"] . "'>
                               Edit
                            </a>

                            <a class='delete-btn'
                               href='../../controllers/roomController.php?delete_room=" . $room["id"] . "'
                               onclick='return confirm(\"Are you sure?\")'>
                               Delete
                            </a>

                        </td>";

                        echo "</tr>";
                    }

                } else {

                    echo "<tr>";
                    echo "<td colspan='7'>No rooms found.</td>";
                    echo "</tr>";
                }

                ?>

            </tbody>

        </table>

    </div>

</div>

<?php

require_once "../../app/layouts/footer.php";

?>