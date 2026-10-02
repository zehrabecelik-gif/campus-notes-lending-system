<?php
require_once __DIR__ . "/../config/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";

render_header("Add Member");

$error = "";

$name = "";
$student_number = "";
$department = "";
$email = "";

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
    } elseif (student_number_exists($conn, $student_number)) {
        $error = "Rejected: This student number is already registered (Rule: " . RULE_UNIQUE_STUDENT_NUMBER . ").";
    } elseif (member_email_exists($conn, $email)) {
        $error = "Rejected: This email is already registered (Rule: " . RULE_UNIQUE_EMAIL . ").";
    } else {
        $stmt = $conn->prepare("INSERT INTO members (name, student_number, department, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $student_number, $department, $email);

        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            redirect("list.php?message=" . urlencode("Record added successfully. Member ID: " . $new_id . " has been created."));
        } else {
            $error = "Database error: " . $stmt->error;
        }
    }
}

render_alert('error', $error);

render_explain("Validation explanation", [
    "All member fields are validated on the server side before INSERT.",
    "Email must pass email format validation.",
    "Student number and email must be unique.",
    "The INSERT operation uses a prepared statement."
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

    <button type="submit">Create Member</button>
    <a class="button secondary" href="list.php">Back</a>
</form>

<?php render_footer(); ?>