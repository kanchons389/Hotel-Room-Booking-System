<?php

require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../models/roomModel.php";

$roomTypes = getAllRoomTypes();

$editData = null;

if (isset($_GET["edit"])) {
    $editData = getRoomTypeById($_GET["edit"]);
}

require_once "../../app/layouts/header.php";
require_once "../../app/layouts/sidebar.php";
require_once "../../app/layouts/navbar.php";

?>

<div class="main-content">

    <div class="dashboard-title">
        <h1>Room Type Management</h1>
        <p>Add, update, and manage hotel room categories.</p>
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
        <h2><?php echo $editData ? "Edit Room Type" : "Add New Room Type"; ?></h2>

        <form action="../../controllers/roomController.php" method="POST">

           <input
    type="hidden"
    name="action"
    value="<?php echo $editData ? "update_room_type" : "add_room_type"; ?>"
>

            <?php if ($editData) { ?>
                <input type="hidden" name="id" value="<?php echo $editData["id"]; ?>">
            <?php } ?>

            <div class="form-row">
                <div class="form-group">
                    <label>Room Type Name</label>
                    <input type="text" name="name" required
                           value="<?php echo $editData ? $editData["name"] : ""; ?>">
                </div>

                <div class="form-group">
                    <label>Price Per Night</label>
                    <input type="number" step="0.01" name="price_per_night" required
                           value="<?php echo $editData ? $editData["price_per_night"] : ""; ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Maximum Capacity</label>
                    <input type="number" name="max_capacity" required
                           value="<?php echo $editData ? $editData["max_capacity"] : ""; ?>">
                </div>

                
            </div>

            <div class="form-group">
                <label>Amenities</label>
                <input type="text" name="amenities"
                       placeholder="WiFi, AC, TV, Room Service"
                       value="<?php echo $editData ? implode(', ', json_decode($editData["amenities"], true)) : ""; ?>">
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" required><?php echo $editData ? $editData["description"] : ""; ?></textarea>
            </div>

            <button type="submit">
                <?php echo $editData ? "Update Room Type" : "Add Room Type"; ?>
            </button>

            <?php if ($editData) { ?>
                <a href="room_types.php" class="cancel-link">Cancel Edit</a>
            <?php } ?>

        </form>
    </div>

    <div class="dashboard-table">
	<div class="table-search-box">
    <input
        type="text"
        class="table-search"
        placeholder="Search table data..."
    >
</div>

<br>
        <h2>All Room Types</h2>
        <br>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Room Type</th>
                    <th>Price</th>
                    <th>Capacity</th>
                    <th>Amenities</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php
                if ($roomTypes && $roomTypes->num_rows > 0) {
                    while ($room = $roomTypes->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $room["id"] . "</td>";
                        echo "<td>" . $room["name"] . "</td>";
                        echo "<td>BDT " . $room["price_per_night"] . "</td>";
                        echo "<td>" . $room["max_capacity"] . " Person</td>";
                      
                        echo "<td>";

                        $amenities = json_decode($room["amenities"], true);
                        if ($amenities) {
                            echo implode(", ", $amenities);
                        }

                        echo "</td>";
                        echo "<td>
                                <a class='edit-btn' href='room_types.php?edit=" . $room["id"] . "'>Edit</a>
                                <a class='delete-btn' href='../../controllers/roomController.php?delete_room_type=" . $room["id"] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No room types found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once "../../app/layouts/footer.php"; ?>