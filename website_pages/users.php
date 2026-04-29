<?php
require "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')
{
    header("Location: login.php");
    exit;
}

$limit = 50;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : "";
$roleFilter = isset($_GET['role']) ? $_GET['role'] : "";

$sort = isset($_GET['sort']) ? $_GET['sort'] : "id";
$order = isset($_GET['order']) ? $_GET['order'] : "asc";

$allowedSort = ["id", "username", "email", "role"];
$allowedOrder = ["asc", "desc"];

if (!in_array($sort, $allowedSort)) $sort = "id";
if (!in_array($order, $allowedOrder)) $order = "asc";

$query = "SELECT id, username, email, role FROM users WHERE 1";

if ($search !== "")
{
    $searchEscaped = $conn->real_escape_string($search);
    $query .= " AND (username LIKE '%$searchEscaped%' OR email LIKE '%$searchEscaped%')";
}

if ($roleFilter !== "")
{
    $roleEscaped = $conn->real_escape_string($roleFilter);
    $query .= " AND role = '$roleEscaped'";
}

$query .= " ORDER BY $sort $order LIMIT $limit OFFSET $offset";

$users = $conn->query($query);

$countQuery = "SELECT COUNT(*) AS total FROM users WHERE 1";

if ($search !== "")
{
    $searchEscaped = $conn->real_escape_string($search);
    $countQuery .= " AND (username LIKE '%$searchEscaped%' OR email LIKE '%$searchEscaped%')";
}

if ($roleFilter !== "")
{
    $roleEscaped = $conn->real_escape_string($roleFilter);
    $countQuery .= " AND role = '$roleEscaped'";
}

$total = $conn->query($countQuery)->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Users</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        #adminWrapper
        {
            max-width: 900px;
            margin: 60px auto;
            padding: 20px;
        }

        table
        {
            border-collapse: collapse;
            width: 100%;
        }

        th, td
        {
            padding: 10px 15px;
            border: 1px solid #00f2fe;
            text-align: left;
            color: white;
        }

        th a
        {
            color: white;
            text-decoration: none;
        }

        th a.sorted.asc::after
        {
            content: ' ↑';
        }

        th a.sorted.desc::after
        {
            content: ' ↓';
        }

        th a.sorted
        {
            font-weight: bold;
            text-decoration: none;
        }

        .pagination
        {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a
        {
            margin: 0 5px;
            color: white;
            text-decoration: none;
        }

        .pagination a.current
        {
            font-weight: bold;
            text-decoration: underline;
        }

        input, select, button
        {
            padding: 5px 10px;
            margin-right: 5px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #00f2fe;
            background: #1f2937;
            color: white;
        }

        button
        {
            background: linear-gradient(90deg,#00eaff,#0077ff);
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div id="spaceBackground">
    <div id="stars1" class="starLayer"></div>
    <div id="stars2" class="starLayer"></div>
    <div id="stars3" class="starLayer"></div>
</div>

<div id="adminWrapper">

    <h1>All Users</h1>

    <form method="GET" class="users-toolbar" style="margin-bottom:20px; text-align:center;">
        <input type="text" name="search" placeholder="Search username/email" value="<?php echo htmlspecialchars($search); ?>">

        <select name="role">
            <option value="">All Roles</option>
            <option value="student" <?php if($roleFilter=="student") echo "selected"; ?>>Student</option>
            <option value="educator" <?php if($roleFilter=="educator") echo "selected"; ?>>Educator</option>
            <option value="parent" <?php if($roleFilter=="parent") echo "selected"; ?>>Parent</option>
            <option value="admin" <?php if($roleFilter=="admin") echo "selected"; ?>>Admin</option>
        </select>

        <button type="submit">Search</button>
    </form>

    <table>
        <tr>
            <?php
            $columns = ['id'=>'ID','username'=>'Username','email'=>'Email','role'=>'Role'];
            foreach($columns as $col => $label):
                $nextOrder = ($sort == $col && $order == 'asc') ? 'desc' : 'asc';
                $sortedClass = ($sort == $col) ? 'sorted ' . $order : '';
            ?>
            <th>
                <a href="?sort=<?php echo $col; ?>&order=<?php echo $nextOrder; ?>&search=<?php echo htmlspecialchars($search); ?>&role=<?php echo $roleFilter; ?>" class="<?php echo $sortedClass; ?>">
                    <?php echo $label; ?>
                </a>
            </th>
            <?php endforeach; ?>
        </tr>

        <?php while ($u = $users->fetch_assoc()): ?>
        <tr>
            <td><?php echo $u['id']; ?></td>
            <td><?php echo $u['username']; ?></td>
            <td><?php echo $u['email']; ?></td>
            <td><?php echo $u['role']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search); ?>&role=<?php echo $roleFilter; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>"
               class="<?php if($i==$page) echo 'current'; ?>">
               <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>

    <br>
    <a id="logoutBtn" href="admin_dashboard.php">Back to Admin Panel</a>

</div>

</body>
</html>
