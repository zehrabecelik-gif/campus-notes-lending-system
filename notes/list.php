<?php
require_once __DIR__ . "/../config/db_connect.php";
require_once __DIR__ . "/../includes/functions.php";

render_header("Notes List");

$message = $_GET['message'] ?? '';

$keyword = trim($_GET['keyword'] ?? '');
$department = trim($_GET['department'] ?? '');
$status = trim($_GET['status'] ?? '');

$where = [];
$params = [];
$types = "";
$active_filters = [];

if ($keyword !== '') {
    $where[] = "(n.title LIKE ? OR n.course_name LIKE ?)";
    $like = "%" . $keyword . "%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
    $active_filters[] = "Keyword search active: '" . $keyword . "' searched in title and course_name.";
}

if ($department !== '') {
    $where[] = "n.department = ?";
    $params[] = $department;
    $types .= "s";
    $active_filters[] = "Department filter active: Department = " . $department . ".";
}

if ($status !== '') {
    $where[] = "n.status = ?";
    $params[] = $status;
    $types .= "s";
    $active_filters[] = "Status filter active: Status = " . $status . ".";
}

$sql = "SELECT n.note_id, n.title, n.course_name, n.department, n.status, n.created_at,
               m.name AS owner_name
        FROM notes n
        INNER JOIN members m ON n.owner_id = m.member_id";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY n.note_id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$row_count = $result->num_rows;

$dept_result = $conn->query("SELECT DISTINCT department FROM notes ORDER BY department");

if (empty($active_filters)) {
    $explain_title = "Showing all " . $row_count . " notes. No filters active.";
    $active_filters[] = "No WHERE condition was added to the SQL query.";
} else {
    $explain_title = "Showing " . $row_count . " result(s) with active search/filter conditions.";
    $active_filters[] = "The same PHP variables used in the WHERE clause are used to generate this explanation.";
}

render_alert('success', $message);
render_explain($explain_title, $active_filters);
?>

<form method="get" class="filters">
    <div>
        <label>Search Keyword</label>
        <input type="text" name="keyword" placeholder="Search title or course name" value="<?php echo e($keyword); ?>">
    </div>

    <div>
        <label>Department</label>
        <select name="department">
            <option value="">All departments</option>
            <?php while ($dept = $dept_result->fetch_assoc()): ?>
                <option value="<?php echo e($dept['department']); ?>" <?php echo $department === $dept['department'] ? 'selected' : ''; ?>>
                    <?php echo e($dept['department']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div>
        <label>Status</label>
        <select name="status">
            <option value="">All statuses</option>
            <option value="Available" <?php echo $status === 'Available' ? 'selected' : ''; ?>>Available</option>
            <option value="Borrowed" <?php echo $status === 'Borrowed' ? 'selected' : ''; ?>>Borrowed</option>
        </select>
    </div>

    <div>
        <button type="submit">Apply</button>
        <a class="button secondary" href="list.php">Reset</a>
    </div>
</form>

<a class="button" href="add.php">Add New Note</a>
<a class="button secondary" href="/cnls/loans/borrow.php">Borrow Note</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Course</th>
            <th>Department</th>
            <th>Owner</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo e($row['note_id']); ?></td>
                <td><?php echo e($row['title']); ?></td>
                <td><?php echo e($row['course_name']); ?></td>
                <td><?php echo e($row['department']); ?></td>
                <td><?php echo e($row['owner_name']); ?></td>
                <td class="status-<?php echo e($row['status']); ?>"><?php echo e($row['status']); ?></td>
                <td><?php echo e($row['created_at']); ?></td>
                <td class="actions">
                    <a class="button small" href="edit.php?id=<?php echo e($row['note_id']); ?>">Edit</a>
                    <a class="button small danger" href="delete.php?id=<?php echo e($row['note_id']); ?>">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php render_footer(); ?>