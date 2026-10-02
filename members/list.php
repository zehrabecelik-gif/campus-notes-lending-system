<?php
require_once __DIR__ . "/../config/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";

render_header("Members List");

$message = $_GET['message'] ?? '';

$stmt = $conn->prepare("SELECT member_id, name, student_number, department, email, created_at FROM members ORDER BY member_id DESC");
$stmt->execute();
$result = $stmt->get_result();

render_alert('success', $message);

render_explain("Members CRUD page", [
    "This page demonstrates READ for the members entity.",
    "Add, edit, and delete buttons demonstrate CREATE, UPDATE, and DELETE.",
    "The SELECT operation uses a prepared statement."
]);
?>

<a class="button" href="add.php">Add New Member</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Student Number</th>
            <th>Department</th>
            <th>Email</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo e($row['member_id']); ?></td>
                <td><?php echo e($row['name']); ?></td>
                <td><?php echo e($row['student_number']); ?></td>
                <td><?php echo e($row['department']); ?></td>
                <td><?php echo e($row['email']); ?></td>
                <td><?php echo e($row['created_at']); ?></td>
                <td class="actions">
                    <a class="button small" href="edit.php?id=<?php echo e($row['member_id']); ?>">Edit</a>
                    <a class="button small danger" href="delete.php?id=<?php echo e($row['member_id']); ?>">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php render_footer(); ?>