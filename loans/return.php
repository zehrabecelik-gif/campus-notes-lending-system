<?php
require_once __DIR__ . "/../config/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT l.*, n.title AS note_title, n.course_name, b.name AS borrower_name
                        FROM loans l
                        INNER JOIN notes n ON l.note_id = n.note_id
                        INNER JOIN members b ON l.borrower_id = b.member_id
                        WHERE l.loan_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$loan = $stmt->get_result()->fetch_assoc();

if (!$loan) {
    render_header("Loan Not Found");
    render_alert('error', "Loan not found.");
    echo '<a class="button" href="list.php">Back to Loans</a>';
    render_footer();
    exit;
}

render_header("Return Note");

$error = "";
$return_date = $_POST['return_date'] ?? date('Y-m-d');

if ($loan['return_date'] !== null) {
    render_alert('error', "This loan has already been returned.");
    echo '<a class="button" href="list.php">Back to Loans</a>';
    render_footer();
    exit;
}

$penalty_info = compute_penalty($loan['due_date'], $return_date);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($return_date === '') {
        $error = "Rejected: Return date cannot be empty (Rule: " . RULE_REQUIRED_FIELD . ").";
    } elseif ($return_date < $loan['loan_date']) {
        $error = "Rejected: Return date cannot be before loan date (Rule: " . RULE_RETURN_DATE_VALID . ").";
    } else {
        $penalty_info = compute_penalty($loan['due_date'], $return_date);
        $penalty_amount = $penalty_info['penalty'];

        $conn->begin_transaction();

        try {
            $update_loan = $conn->prepare("UPDATE loans SET return_date = ?, penalty_amount = ? WHERE loan_id = ?");
            $update_loan->bind_param("sdi", $return_date, $penalty_amount, $id);
            $update_loan->execute();

            $update_note = $conn->prepare("UPDATE notes SET status = 'Available' WHERE note_id = ?");
            $update_note->bind_param("i", $loan['note_id']);
            $update_note->execute();

            $conn->commit();

            redirect("list.php?message=" . urlencode("Return successful. Penalty: " . $penalty_amount . " TL."));
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Database error: " . $e->getMessage();
        }
    }
}

render_alert('error', $error);

render_explain("Penalty calculation explanation", [
    "Due date is calculated at borrow time as loan_date + 7 days.",
    "Days overdue = max(0, return_date - due_date).",
    "Penalty amount = days overdue x 5 TL.",
    "After return, loan.return_date and loan.penalty_amount are updated, and note.status becomes Available."
]);
?>

<div class="card">
    <h3>Loan Information</h3>
    <p><strong>Note:</strong> <?php echo e($loan['note_title']); ?> - <?php echo e($loan['course_name']); ?></p>
    <p><strong>Borrower:</strong> <?php echo e($loan['borrower_name']); ?></p>
    <p><strong>Loan Date:</strong> <?php echo e($loan['loan_date']); ?></p>
    <p><strong>Due Date:</strong> <?php echo e($loan['due_date']); ?></p>
</div>

<div class="card">
    <h3>Penalty Breakdown Preview</h3>

    <table>
        <tr>
            <th>Loan Detail</th>
            <th>Value</th>
        </tr>

        <tr>
            <td>Due date</td>
            <td><?php echo e($loan['due_date']); ?></td>
        </tr>

        <tr>
            <td>Actual return date</td>
            <td><?php echo e($return_date); ?></td>
        </tr>

        <tr>
            <td>Days overdue</td>
            <td><?php echo e($penalty_info['days_overdue']); ?> day(s)</td>
        </tr>

        <tr>
            <td>Daily penalty rate</td>
            <td><?php echo e($penalty_info['daily_rate']); ?> TL / day</td>
        </tr>

        <tr>
            <td>Penalty formula</td>
            <td>max(0, return_date - due_date) x daily_rate</td>
        </tr>

        <tr>
            <td>Penalty calculation</td>
            <td><?php echo e($penalty_info['days_overdue']); ?> x <?php echo e($penalty_info['daily_rate']); ?> TL</td>
        </tr>

        <tr>
            <td>Total penalty</td>
            <td><?php echo e($penalty_info['penalty']); ?> TL</td>
        </tr>
    </table>

    <?php if ($penalty_info['penalty'] == 0): ?>
        <p><strong>No penalty - note was returned on time. Rule: return_date <= due_date.</strong></p>
    <?php else: ?>
        <p><strong>Late return detected. Penalty applied because return_date > due_date.</strong></p>
    <?php endif; ?>
</div>

<form method="post">
    <label>Return Date</label>
    <input type="date" name="return_date" value="<?php echo e($return_date); ?>">

    <button type="submit">Confirm Return</button>
    <a class="button secondary" href="list.php">Back</a>
</form>

<?php render_footer(); ?>