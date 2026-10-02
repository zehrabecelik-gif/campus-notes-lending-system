<?php
require_once __DIR__ . "/../config/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM members WHERE member_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();

render_header("Delete Member");

if (!$member) {
    render_alert('error', "Member not found.");
    echo '<a class="button" href="list.php">Back to Members</a>';
    render_footer();
    exit;
}

$error = "";

$count_notes_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM notes WHERE owner_id = ?");
$count_notes_stmt->bind_param("i", $id);
$count_notes_stmt->execute();
$notes_count = $count_notes_stmt->get_result()->fetch_assoc()['total'];

$count_loans_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM loans WHERE borrower_id = ?");
$count_loans_stmt->bind_param("i", $id);
$count_loans_stmt->execute();
$loans_count = $count_loans_stmt->get_result()->fetch_assoc()['total'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($notes_count > 0 || $loans_count > 0) {
        $error = "Rejected: This member cannot be deleted because related notes or loans exist (Rule: foreign key integrity).";
    } else {
        $stmt = $conn->prepare("DELETE FROM members WHERE member_id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            redirect("list.php?message=" . urlencode("Record deleted successfully. Member ID: " . $id . " has been deleted."));
        } else {
            $error = "Database error: " . $stmt->error;
        }
    }
}

render_alert('error', $error);

render_explain("Delete explanation", [
    "A member can be deleted only if no related notes or loans exist.",
    "This protects referential integrity between members, notes, and loans.",
    "For demo deletion, use the sample member named Demo Delete Member."
]);
?>

<form method="post">
    <p>Are you sure you want to delete this member?</p>
    <p><strong><?php echo e($member['name']); ?></strong> - <?php echo e($member['student_number']); ?></p>
    <p>Related notes: <?php echo e($notes_count); ?></p>
    <p>Related loans: <?php echo e($loans_count); ?></p>

    <button type="submit" class="button danger">Delete Member</button>
    <a class="button secondary" href="list.php">Cancel</a>
</form>

<?php render_footer(); ?>