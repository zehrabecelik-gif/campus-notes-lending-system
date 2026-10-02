<?php
require_once __DIR__ . "/../config/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";

render_header("Add Note");

$error = "";

$title = "";
$course_name = "";
$department = "";
$owner_id = "";
$status = "Available";

$members_stmt = $conn->prepare("SELECT member_id, name, student_number FROM members ORDER BY name");
$members_stmt->execute();
$members = $members_stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $course_name = trim($_POST['course_name'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $owner_id = (int)($_POST['owner_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');

    if ($title === '') {
        $error = "Rejected: Note title cannot be empty (Rule: " . RULE_REQUIRED_FIELD . ").";
    } elseif ($course_name === '') {
        $error = "Rejected: Course name cannot be empty (Rule: " . RULE_REQUIRED_FIELD . ").";
    } elseif ($department === '') {
        $error = "Rejected: Department cannot be empty (Rule: " . RULE_REQUIRED_FIELD . ").";
    } elseif ($owner_id <= 0) {
        $error = "Rejected: Owner must be selected (Rule: " . RULE_REQUIRED_FIELD . ").";
    } elseif (!in_array($status, ['Available', 'Borrowed'], true)) {
        $error = "Rejected: Status must be Available or Borrowed (Rule: " . RULE_ALLOWED_STATUS . ").";
    } else {
        $stmt = $conn->prepare("INSERT INTO notes (title, course_name, department, owner_id, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $title, $course_name, $department, $owner_id, $status);

        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            redirect("list.php?message=" . urlencode("Record added successfully. Note ID: " . $new_id . " has been created."));
        } else {
            $error = "Database error: " . $stmt->error;
        }
    }
}

render_alert('error', $error);

render_explain("Note create explanation", [
    "This page demonstrates CREATE for the notes entity.",
    "Owner is selected from the members table, which proves the members-to-notes relationship.",
    "Status is restricted to Available or Borrowed.",
    "The INSERT operation uses a prepared statement."
]);
?>

<form method="post">
    <label>Note Title</label>
    <input type="text" name="title" value="<?php echo e($title); ?>">

    <label>Course Name</label>
    <input type="text" name="course_name" value="<?php echo e($course_name); ?>">

    <label>Department</label>
    <input type="text" name="department" value="<?php echo e($department); ?>">

    <label>Owner</label>
    <select name="owner_id">
        <option value="">Select owner</option>
        <?php while ($m = $members->fetch_assoc()): ?>
            <option value="<?php echo e($m['member_id']); ?>" <?php echo (string)$owner_id === (string)$m['member_id'] ? 'selected' : ''; ?>>
                <?php echo e($m['name'] . " (" . $m['student_number'] . ")"); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Status</label>
    <select name="status">
        <option value="Available" <?php echo $status === 'Available' ? 'selected' : ''; ?>>Available</option>
        <option value="Borrowed" <?php echo $status === 'Borrowed' ? 'selected' : ''; ?>>Borrowed</option>
    </select>

    <button type="submit">Create Note</button>
    <a class="button secondary" href="list.php">Back</a>
</form>

<?php render_footer(); ?>