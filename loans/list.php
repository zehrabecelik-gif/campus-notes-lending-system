<?php
require_once __DIR__ . "/../config/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";

render_header("Loans List");

$message = $_GET['message'] ?? '';

$loan_status = trim($_GET['loan_status'] ?? '');

$where = [];
$explain_items = [];

if ($loan_status === 'active') {
    $where[] = "l.return_date IS NULL";
    $explain_items[] = "Loan status filter active: return_date IS NULL.";
} elseif ($loan_status === 'returned') {
    $where[] = "l.return_date IS NOT NULL";
    $explain_items[] = "Loan status filter active: return_date IS NOT NULL.";
}

$sql = "SELECT l.loan_id, l.loan_date, l.due_date, l.return_date, l.penalty_amount,
               n.title AS note_title, n.course_name,
               b.name AS borrower_name
        FROM loans l
        INNER JOIN notes n ON l.note_id = n.note_id
        INNER JOIN members b ON l.borrower_id = b.member_id";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY l.loan_id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$row_count = $result->num_rows;

if (empty($explain_items)) {
    $explain_title = "Showing all " . $row_count . " loan records. No loan status filter active.";
    $explain_items[] = "No WHERE condition was added for active/returned loans.";
} else {
    $explain_title = "Showing " . $row_count . " loan record(s) with active filter.";
    $explain_items[] = "The same filter variable is used in the SQL condition and this explanation.";
}

render_alert('success', $message);
render_explain($explain_title, $explain_items);
?>

<form method="get" class="filters">
    <div>
        <label>Loan Status</label>
        <select name="loan_status">
            <option value="">All loans</option>
            <option value="active" <?php echo $loan_status === 'active' ? 'selected' : ''; ?>>Active loans</option>
            <option value="returned" <?php echo $loan_status === 'returned' ? 'selected' : ''; ?>>Returned loans</option>
        </select>
    </div>

    <div></div>
    <div></div>

    <div>
        <button type="submit">Apply</button>
        <a class="button secondary" href="list.php">Reset</a>
    </div>
</form>

<a class="button" href="borrow.php">Borrow Note</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Note</th>
            <th>Course</th>
            <th>Borrower</th>
            <th>Loan Date</th>
            <th>Due Date</th>
            <th>Return Date</th>
            <th>Penalty</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo e($row['loan_id']); ?></td>
                <td><?php echo e($row['note_title']); ?></td>
                <td><?php echo e($row['course_name']); ?></td>
                <td><?php echo e($row['borrower_name']); ?></td>
                <td><?php echo e($row['loan_date']); ?></td>
                <td><?php echo e($row['due_date']); ?></td>
                <td><?php echo $row['return_date'] ? e($row['return_date']) : 'Active'; ?></td>
                <td><?php echo e($row['penalty_amount']); ?> TL</td>
                <td class="actions">
                    <?php if (!$row['return_date']): ?>
                        <a class="button small" href="return.php?id=<?php echo e($row['loan_id']); ?>">Return</a>
                    <?php else: ?>
                        Returned
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php render_footer(); ?>