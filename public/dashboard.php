<?php
session_start();
require '../config/database.php';

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// Fetch active users
$result = $conn->query("SELECT * FROM users WHERE status = 'Active' AND deleted_at IS NULL");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-4">
        <h2>User Management</h2>
        <a href="../actions/logout.php" class="btn btn-danger">Logout</a>

        <div class="mt-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add Users</button>
            <button class="btn btn-danger" id="bulkDeleteBtn" disabled>Delete Selected</button>
        </div>

        <table class="table mt-3">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="userTable">
                <?php while ($user = $result->fetch_assoc()) : ?>
                    <tr data-id="<?= $user['id'] ?>">
                        <td><input type="checkbox" class="userCheckbox" value="<?= $user['id'] ?>"></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning editUserBtn" data-id="<?= $user['id'] ?>" 
                                    data-name="<?= htmlspecialchars($user['name']) ?>" 
                                    data-email="<?= htmlspecialchars($user['email']) ?>" 
                                    data-role="<?= htmlspecialchars($user['role']) ?>" 
                                    data-bs-toggle="modal" data-bs-target="#editUserModal">Edit</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Users Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="bulkUsersContainer"></div>
                    <button class="btn btn-secondary mt-2" id="addMoreUser">Add More</button>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" id="saveUsers">Save</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editUserId">
                    <input type="text" class="form-control mb-2" id="editUserName" placeholder="Name">
                    <input type="email" class="form-control mb-2" id="editUserEmail" placeholder="Email">
                    <select class="form-control" id="editUserRole">
                        <option value="Project Manager">Project Manager</option>
                        <option value="Team Lead">Team Lead</option>
                        <option value="Developer">Developer</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" id="updateUser">Update</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Bulk select toggle
            $('#selectAll').on('change', function() {
                $('.userCheckbox').prop('checked', $(this).prop('checked'));
                $('#bulkDeleteBtn').prop('disabled', !$('.userCheckbox:checked').length);
            });

            $(document).on('change', '.userCheckbox', function() {
                $('#bulkDeleteBtn').prop('disabled', !$('.userCheckbox:checked').length);
            });

            // Add User Fields
            $('#addMoreUser').on('click', function() {
                $('#bulkUsersContainer').append(`
            <div class="userEntry mb-2">
                <input type="text" class="form-control mb-1 userName" placeholder="Name">
                <input type="email" class="form-control mb-1 userEmail" placeholder="Email">
                <select class="form-control userRole">
                    <option value="Project Manager">Project Manager</option>
                    <option value="Team Lead">Team Lead</option>
                    <option value="Developer">Developer</option>
                </select>
                <button class="btn btn-danger btn-sm removeUserEntry mt-1">Remove</button>
            </div>
        `);
            });

            // Remove User Entry
            $(document).on('click', '.removeUserEntry', function() {
                $(this).closest('.userEntry').remove();
            });
            $('.editUserBtn').on('click', function() {
                $('#editUserId').val($(this).data('id'));
                $('#editUserName').val($(this).data('name'));
                $('#editUserEmail').val($(this).data('email'));
                $('#editUserRole').val($(this).data('role'));
            });

            //edit user
            $('#updateUser').on('click', function() {
                let userId = $('#editUserId').val();
                let name = $('#editUserName').val().trim();
                let email = $('#editUserEmail').val().trim();
                let role = $('#editUserRole').val();

                if (!name || !email) {
                    alert("All fields are required.");
                    return;
                }

                $.post('../actions/update_user.php', {
                    id: userId,
                    name,
                    email,
                    role
                }, function(response) {
                    alert(response.message);
                    location.reload();
                }, 'json').fail(function(jqXHR, textStatus) {
                    alert("Error: " + textStatus + "\n" + jqXHR.responseText);
                });
            });


            // Save Users (Bulk Add)
            $('#saveUsers').on('click', function() {
                let users = [];

                $('.userEntry').each(function() {
                    let name = $(this).find('.userName').val().trim();
                    let email = $(this).find('.userEmail').val().trim();
                    let role = $(this).find('.userRole').val();

                    if (name && email) {
                        users.push({
                            name,
                            email,
                            role
                        });
                    }
                });

                if (users.length === 0) {
                    alert("Please enter at least one valid user.");
                    return;
                }

                $.post('../actions/bulk_add_users.php', JSON.stringify({
                    users
                }), function(response) {
                    alert(response.message);
                    location.reload();
                }, 'json').fail(function(jqXHR, textStatus) {
                    alert("Error: " + textStatus + "\n" + jqXHR.responseText);
                });
            });

            // Bulk Delete Users
            $('#bulkDeleteBtn').on('click', function() {
                let selectedIds = $('.userCheckbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) {
                    alert("No users selected.");
                    return;
                }

                $.post('../actions/bulk_delete_users.php', JSON.stringify({
                    ids: selectedIds
                }), function(response) {
                    if (typeof response === "object") {
                        alert("Status: " + response.status + "\nMessage: " + response.message +
                            (response.ids ? "\nDeleted IDs: " + response.ids.join(", ") : ""));
                    } else {
                        alert(response);
                    }
                    location.reload();
                }, 'json').fail(function(jqXHR, textStatus) {
                    alert("Error: " + textStatus + "\n" + jqXHR.responseText);
                });
            });
        });
    </script>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>