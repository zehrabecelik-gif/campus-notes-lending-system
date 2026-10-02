<?php
require_once __DIR__ . "/../config/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM members WHERE member_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();

if (!$member) {
    render_header("Member Not Found");
    render_alert('error', "Member not found.");
    echo '<a class="button" href="list.php">Back to Members</a>';
    render_footer();
    exit;
}

render_header("Edit Member");

$error = "";

$name = $member['name'];
$student_number = $member['student_number'];
$department = $member['department'];
$email = $member['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $student_number = trim($_POST['student_number'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '') {
        $error = "Rejected: Name cannot be empty (Rule: " . RULE_REQUIRED_FIELD . ").";
    } elseif ($student_number === '') {
        $error = "Rejected: Student number cannot be empty (Rule: " . RULE_REQUIRED_FIELD . ").";
    } elseif ($department === '') {
        $error = "Rejected: Department cannot be empty (Rule: " . RULE_REQUIRED_FIELD . ").";
    } elseif ($email === '') {
        $error = "Rejected: Email cannot be empty (Rule: " . RULE_REQUIRED_FIELD . ").";
    } elseif (!is_valid_email($email)) {
        $error = "Rejected: Email is not valid - must contain '@' and a domain (Rule: " . RULE_VALID_EMAIL . ").";
    } elseif (student_number_exists($conn, $student_number, $id)) {
        $error = "Rejected: This student number is already registered (Rule: " . RULE_UNIQUE_STUDENT_NUMBER . ").";
    } elseif (member_email_exists($conn, $email, $id)) {
        $error = "Rejected: This email is already registered (Rule: " . RULE_UNIQUE_EMAIL . ").";
    } else {
        $stmt = $conn->prepare("UPDATE members SET name = ?, student_number = ?, department = ?, email = ? WHERE member_id = ?");
        $stmt->bind_param("ssssi", $name, $student_number, $department, $email, $id);

        if ($stmt->execute()) {
            redirect("list.php?message=" . urlencode("Record updated successfully. Member ID: " . $id . " has been updated."));
        } else {
            $error = "Database error: " . $stmt->error;
        }
    }
}

render_alert('error', $error);

render_explain("Update validation explanation", [
    "The form validates required fields, email format, unique email, and unique student number.",
    "Uniqueness checks exclude the current member ID.",
    "The UPDATE operation uses a prepared statement."
]);
?>

<form method="post">
    <label>Name</label>
    <input type="text" name="name" value="<?php echo e($name); ?>">

    <label>Student Number</label>
    <input type="text" name="student_number" value="<?php echo e($student_number); ?>">

    <label>Department</label>
    <input type="text" name="department" value="<?php echo e($department); ?>">

    <label>Email</label>
    <input type="text" name="email" value="<?php echo e($email); ?>">

    <button type="submit">Update Member</button>
    <a class="button secondary" href="list.php">Back</a>
</form>

<?php render_footer(); ?>