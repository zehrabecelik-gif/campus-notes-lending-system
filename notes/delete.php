<?php
require_once __DIR__ . "/../config/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM notes WHERE note_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$note = $stmt->get_result()->fetch_assoc();

render_header("Delete Note");

if (!$note) {
    render_alert('error', "Note not found.");
    echo '<a class="button" href="list.php">Back to Notes</a>';
    render_footer();
    exit;
}

$error = "";

$count_loans_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM loans WHERE note_id = ?");
$count_loans_stmt->bind_param("i", $id);
$count_loans_stmt->execute();
$loans_count = $count_loans_stmt->get_result()->fetch_assoc()['total'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($loans_count > 0) {
        $error = "Rejected: This note cannot be deleted because it has loan history (Rule: foreign key integrity).";
    } else {
        $stmt = $conn->prepare("DELETE FROM notes WHERE note_id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            redirect("list.php?message=" . urlencode("Record deleted successfully. Note ID: " . $id . " has been deleted."));
        } else {
            $error = "Database error: " . $stmt->error;
        }
    }
}

render_alert('error', $error);

render_explain("Delete explanation", [
    "A note can be deleted only if it has no loan history.",
    "This protects the relationship between notes and loans.",
    "For demo deletion, use the sample note named Demo Delete Note."
]);
?>

<form method="post">
    <p>Are you sure you want to delete this note?</p>
    <p><strong><?php echo e($note['title']); ?></strong> - <?php echo e($note['course_name']); ?></p>
    <p>Related loans: <?php echo e($loans_count); ?></p>

    <button type="submit" class="button danger">Delete Note</button>
    <a class="button secondary" href="list.php">Cancel</a>
</form>

<?php render_footer(); ?>