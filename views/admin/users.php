<?php

require_once "../../app/helpers/session.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../models/userModel.php";

$users = getAllUsers();

$editData = null;

if (isset($_GET["edit"])) {
    $editData = getUserById($_GET["edit"]);
}

require_once "../../app/layouts/header.php";
require_once "../../app/layouts/sidebar.php";
require_once "../../app/layouts/navbar.php";

?>

<div class="main-content">

    <div class="dashboard-title">
        <h1>User Management</h1>
        <p>Manage system users and staff accounts.</p>
    </div>

    <?php

    if (isset($_SESSION["success"])) {

        echo "<div class='success-message'>";
        echo $_SESSION["success"];
        echo "</div>";

        unset($_SESSION["success"]);
    }

    if (isset($_SESSION["error"])) {

        echo "<div class='error'>";
        echo $_SESSION["error"];
        echo "</div>";

        unset($_SESSION["error"]);
    }

    ?>

    <div class="form-box">

        <h2>

            <?php
            echo $editData ? "Edit User" : "Add New User";
            ?>

        </h2>

        <form action="../../controllers/userController.php" method="POST">

            <input
                type="hidden"
                name="action"
                value="<?php echo $editData ? "update_user" : "add_user"; ?>"
            >

            <?php if ($editData) { ?>

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $editData["id"]; ?>"
                >

            <?php } ?>

            <div class="form-row">

                <div class="form-group">

                    <label>Full Name</label>

                    <input
                        type="text"
                        name="name"
                        required
                        value="<?php echo $editData ? $editData["name"] : ""; ?>"
                    >

                </div>

                <div class="form-group">

                    <label>Email Address</label>

                    <input
                        type="email"
                        name="email"
                        required
                        value="<?php echo $editData ? $editData["email"] : ""; ?>"
                    >

                </div>

            </div>

            <div class="form-row">

                <div class="form-group">

                    <label>Phone Number</label>

                    <input
                        type="text"
                        name="phone"
                        required
                        value="<?php echo $editData ? $editData["phone"] : ""; ?>"
                    >

                </div>

                <div class="form-group">

                    <label>User Role</label>

                    <select name="role" required>

                        <?php

                        $roles = [
                            "admin",
                            "guest",
                            "receptionist",
                            "housekeeping"
                        ];

                        foreach ($roles as $role) {

                            $selected = "";

                            if ($editData && $editData["role"] == $role) {
                                $selected = "selected";
                            }

                            echo "<option value='$role' $selected>";
                            echo ucfirst($role);
                            echo "</option>";
                        }

                        ?>

                    </select>

                </div>

            </div>

            <div class="form-row">

                <?php if (!$editData) { ?>

                    <div class="form-group">

                        <label>Password</label>

                        <input
                            type="password"
                            name="password"
                            required
                        >

                    </div>

                <?php } ?>

                <div class="form-group">

                    <label>Account Status</label>

                    <select name="is_active">

                        <?php

                        $statuses = [
                            1 => "Active",
                            0 => "Inactive"
                        ];

                        foreach ($statuses as $key => $value) {

                            $selected = "";

                            if (
                                $editData &&
                                $editData["is_active"] == $key
                            ) {
                                $selected = "selected";
                            }

                            echo "<option value='$key' $selected>";
                            echo $value;
                            echo "</option>";
                        }

                        ?>

                    </select>

                </div>

            </div>

            <button type="submit">

                <?php
                echo $editData ? "Update User" : "Add User";
                ?>

            </button>

            <?php if ($editData) { ?>

                <a href="users.php" class="cancel-link">
                    Cancel Edit
                </a>

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

        <h2>All Users</h2>

        <br>

        <table>

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

            </thead>

            <tbody>

                <?php

                if ($users && $users->num_rows > 0) {

                    while ($user = $users->fetch_assoc()) {

                        echo "<tr>";

                        echo "<td>" . $user["id"] . "</td>";

                        echo "<td>" . $user["name"] . "</td>";

                        echo "<td>" . $user["email"] . "</td>";

                        echo "<td>" . $user["phone"] . "</td>";

                        echo "<td>" . ucfirst($user["role"]) . "</td>";

                        echo "<td>";

                       if ($user["is_active"] == 1) {

    echo "<span class='status-badge status-active'>
            Active
          </span>";

} else {

    echo "<span class='status-badge status-inactive'>
            Inactive
          </span>";
}

                        echo "</td>";

                        echo "<td>

                            <a class='edit-btn'
                               href='users.php?edit=" . $user["id"] . "'>
                               Edit
                            </a>

                            <a class='delete-btn'
                               href='../../controllers/userController.php?delete=" . $user["id"] . "'
                               onclick='return confirm(\"Delete this user?\")'>
                               Delete
                            </a>

                        </td>";

                        echo "</tr>";
                    }

                } else {

                    echo "<tr>";
                    echo "<td colspan='7'>No users found.</td>";
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