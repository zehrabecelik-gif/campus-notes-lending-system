<?php
require_once __DIR__ . "/config/db_connect.php";
require_once __DIR__ . "/includes/functions.php";

render_header("Campus Notes Lending System");

$member_count = $conn->query("SELECT COUNT(*) AS total FROM members")->fetch_assoc()['total'];
$note_count = $conn->query("SELECT COUNT(*) AS total FROM notes")->fetch_assoc()['total'];
$active_loan_count = $conn->query("SELECT COUNT(*) AS total FROM loans WHERE return_date IS NULL")->fetch_assoc()['total'];
?>

<div class="card">
    <h2>Project Overview</h2>
    <p>
        CNLS is a local PHP + MySQL web application for lending and borrowing printed academic lecture notes.
        It demonstrates CRUD, search/filter, server-side validation, prepared statements, and visible explainability.
    </p>
</div>

<div class="grid">
    <div class="card">
        <h3>Members</h3>
        <p>Total members: <?php echo e($member_count); ?></p>
        <a class="button" href="/cnls/members/list.php">Manage Members</a>
    </div>

    <div class="card">
        <h3>Notes</h3>
        <p>Total notes: <?php echo e($note_count); ?></p>
        <a class="button" href="/cnls/notes/list.php">Manage Notes</a>
    </div>

    <div class="card">
        <h3>Loans</h3>
        <p>Active loans: <?php echo e($active_loan_count); ?></p>
        <a class="button" href="/cnls/loans/list.php">Manage Loans</a>
    </div>
</div>

<?php
render_explain("No login explanation", [
    "Login is not implemented because it is optional in the assignment guideline.",
    "Member Create is not account registration; it inserts a student record into the members table.",
    "The system runs locally in XAMPP and demonstrates database operations through prepared statements."
]);

render_footer();
?>