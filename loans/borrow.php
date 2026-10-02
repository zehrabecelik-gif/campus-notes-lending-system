<?php
require_once __DIR__ . "/../config/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";

render_header("Borrow Note");

$error = "";

$note_id = (int)($_POST['note_id'] ?? 0);
$borrower_id = (int)($_POST['borrower_id'] ?? 0);
$loan_date = $_POST['loan_date'] ?? date('Y-m-d');

$notes_stmt = $conn->prepare("SELECT note_id, title, course_name FROM notes WHERE status = 'Available' ORDER BY title");
$notes_stmt->execute();
$available_notes = $notes_stmt->get_result();

$members_stmt = $conn->prepare("SELECT member_id, name, student_number FROM members ORDER BY name");
$members_stmt->execute();
$members = $members_stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($note_id <= 0) {
        $error = "Rejected: Note must be selected (Rule: " . RULE_REQUIRED_FIELD . ").";
    } elseif ($borrower_id <= 0) {
        $error = "Rejected: Borrower must be selected (Rule: " . RULE_REQUIRED_FIELD . ").";
    } elseif ($loan_date === '') {
        $error = "Rejected: Loan date cannot be empty (Rule: " . RULE_REQUIRED_FIELD . ").";
    } elseif ($loan_date > date('Y-m-d')) {
        $error = "Rejected: Loan date cannot be in the future (Rule: " . RULE_DATE_NOT_FUTURE . ").";
    } else {
        $note_stmt = $conn->prepare("SELECT note_id, owner_id, status FROM notes WHERE note_id = ?");
        $note_stmt->bind_param("i", $note_id);
        $note_stmt->execute();
        $note = $note_stmt->get_result()->fetch_assoc();

        if (!$note) {
            $error = "Rejected: Selected note does not exist (Rule: valid note).";
        } elseif ($note['status'] !== 'Available') {
            $error = "Rejected: This note is currently borrowed and not available (Rule: " . RULE_STATUS_AVAILABLE . ").";
        } elseif ((int)$note['owner_id'] === $borrower_id) {
            $error = "Rejected: You cannot borrow your own note (Rule: " . RULE_BORROWER_NOT_OWNER . ").";
        } else {
            $due_date = date('Y-m-d', strtotime($loan_date . ' +7 days'));

            $conn->begin_transaction();

            try {
                $insert_stmt = $conn->prepare("INSERT INTO loans (note_id, borrower_id, loan_date, due_date, return_date, penalty_amount) VALUES (?, ?, ?, ?, NULL, 0.00)");
                $insert_stmt->bind_param("iiss", $note_id, $borrower_id, $loan_date, $due_date);
                $insert_stmt->execute();

                $update_stmt = $conn->prepare("UPDATE notes SET status = 'Borrowed' WHERE note_id = ?");
                $update_stmt->bind_param("i", $note_id);
                $update_stmt->execute();

                $conn->commit();

                redirect("list.php?message=" . urlencode("Borrow successful. Due date is " . $due_date . " (Rule: due_date = loan_date + 7 days)."));
            } catch (Exception $e) {
                $conn->rollback();
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}

render_alert('error', $error);

render_explain("Borrow validation explanation", [
    "Only notes with status = Available can be borrowed.",
    "Borrower cannot be the owner of the selected note.",
    "Loan date cannot be in the future.",
    "Due date is automatically calculated as loan_date + 7 days.",
    "Borrow operation inserts a loan record and updates note status to Borrowed."
]);
?>

<form method="post">
    <label>Available Note</label>
    <select name="note_id">
        <option value="">Select available note</option>
        <?php while ($n = $available_notes->fetch_assoc()): ?>
            <option value="<?php echo e($n['note_id']); ?>" <?php echo (string)$note_id === (string)$n['note_id'] ? 'selected' : ''; ?>>
                <?php echo e($n['title'] . " - " . $n['course_name']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Borrower</label>
    <select name="borrower_id">
        <option value="">Select borrower</option>
        <?php while ($m = $members->fetch_assoc()): ?>
            <option value="<?php echo e($m['member_id']); ?>" <?php echo (string)$borrower_id === (string)$m['member_id'] ? 'selected' : ''; ?>>
                <?php echo e($m['name'] . " (" . $m['student_number'] . ")"); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Loan Date</label>
    <input type="date" name="loan_date" value="<?php echo e($loan_date); ?>">

    <button type="submit">Borrow Note</button>
    <a class="button secondary" href="list.php">Back</a>
</form>

<?php render_footer(); ?>