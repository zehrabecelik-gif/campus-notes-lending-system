<?php
define('DAILY_PENALTY_RATE', 5);

define('RULE_REQUIRED_FIELD', 'required field');
define('RULE_VALID_EMAIL', 'valid email');
define('RULE_UNIQUE_EMAIL', 'unique email');
define('RULE_UNIQUE_STUDENT_NUMBER', 'unique student number');
define('RULE_ALLOWED_STATUS', 'allowed status');
define('RULE_STATUS_AVAILABLE', 'status = Available');
define('RULE_BORROWER_NOT_OWNER', 'borrower != owner');
define('RULE_DATE_NOT_FUTURE', 'loan_date <= today');
define('RULE_RETURN_DATE_VALID', 'return_date >= loan_date');

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: " . $url);
    exit;
}

function render_header($title) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($title); ?> | CNLS</title>
    <link rel="stylesheet" href="/cnls/assets/style.css">
</head>
<body>
<div class="container">
    <nav class="topnav">
        <a href="/cnls/index.php">Dashboard</a>
        <a href="/cnls/members/list.php">Members</a>
        <a href="/cnls/notes/list.php">Notes</a>
        <a href="/cnls/loans/list.php">Loans</a>
    </nav>

    <h1><?php echo e($title); ?></h1>
<?php
}

function render_footer() {
?>
</div>
</body>
</html>
<?php
}

function render_alert($type, $message) {
    if (!$message) {
        return;
    }

    $class = $type === 'success' ? 'alert success' : 'alert error';
    echo '<div class="' . e($class) . '">' . e($message) . '</div>';
}

function render_explain($title, $items) {
    echo '<div class="explain">';
    echo '<h3>Why? / Explain</h3>';
    echo '<strong>' . e($title) . '</strong>';

    if (!empty($items)) {
        echo '<ul>';
        foreach ($items as $item) {
            echo '<li>' . e($item) . '</li>';
        }
        echo '</ul>';
    }

    echo '</div>';
}

function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function member_email_exists($conn, $email, $exclude_member_id = null) {
    if ($exclude_member_id) {
        $stmt = $conn->prepare("SELECT member_id FROM members WHERE email = ? AND member_id != ?");
        $stmt->bind_param("si", $email, $exclude_member_id);
    } else {
        $stmt = $conn->prepare("SELECT member_id FROM members WHERE email = ?");
        $stmt->bind_param("s", $email);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}

function student_number_exists($conn, $student_number, $exclude_member_id = null) {
    if ($exclude_member_id) {
        $stmt = $conn->prepare("SELECT member_id FROM members WHERE student_number = ? AND member_id != ?");
        $stmt->bind_param("si", $student_number, $exclude_member_id);
    } else {
        $stmt = $conn->prepare("SELECT member_id FROM members WHERE student_number = ?");
        $stmt->bind_param("s", $student_number);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}

function compute_penalty($due_date, $return_date) {
    $due = new DateTime($due_date);
    $returned = new DateTime($return_date);

    if ($returned <= $due) {
        return [
            'days_overdue' => 0,
            'daily_rate' => DAILY_PENALTY_RATE,
            'penalty' => 0
        ];
    }

    $days = $due->diff($returned)->days;

    return [
        'days_overdue' => $days,
        'daily_rate' => DAILY_PENALTY_RATE,
        'penalty' => $days * DAILY_PENALTY_RATE
    ];
}
?>