<?php
require_once 'config/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$project_id = $_GET['id'];

// Fetch project details
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) {
    header("Location: index.php");
    exit();
}

// Handle adding new task
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO tasks (project_id, title, description, priority, due_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$project_id, $title, $description, $priority, $due_date]);
        header("Location: project.php?id=" . $project_id);
        exit();
    } else {
        $message = "Task title cannot be empty!";
    }
}

// Fetch all tasks for this project
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE project_id = ? ORDER BY created_at DESC");
$stmt->execute([$project_id]);
$tasks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['title']); ?> - Tasks</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; }
        .container { max-width: 700px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); }
        h1, h2 { color: #333; }
        .back-link { display: inline-block; margin-bottom: 15px; color: #007bff; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        form { background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 20px; display: flex; flex-direction: column; gap: 10px; }
        input, textarea, select { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        ul { list-style: none; padding: 0; }
        li { background: #e9ecef; padding: 12px; margin-bottom: 10px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; }
        .task-info h4 { margin: 0 0 5px 0; color: #333; }
        .task-info p { margin: 0; color: #666; font-size: 14px; }
        .badge { padding: 3px 6px; border-radius: 4px; font-size: 12px; color: white; }
        .High { background: #dc3545; }
        .Medium { background: #ffc107; color: black; }
        .Low { background: #28a745; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">&larr; Back to Projects</a>
        <h1>Project: <?php echo htmlspecialchars($project['title']); ?></h1>

        <?php if (!empty($message)): ?>
            <p class="error"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Add Task Form -->
        <form action="project.php?id=<?php echo $project_id; ?>" method="POST">
            <h3>Add New Task</h3>
            <input type="text" name="title" placeholder="Task title..." required>
            <textarea name="description" placeholder="Task description..."></textarea>
            <select name="priority">
                <option value="Low">Low Priority</option>
                <option value="Medium" selected>Medium Priority</option>
                <option value="High">High Priority</option>
            </select>
            <input type="date" name="due_date">
            <button type="submit" name="add_task">Add Task</button>
        </form>

        <h2>Tasks List</h2>
        <ul>
            <?php if (count($tasks) > 0): ?>
                <?php foreach ($tasks as $task): ?>
                    <li>
                        <div class="task-info">
                            <h4><?php echo htmlspecialchars($task['title']); ?></h4>
                            <p><?php echo htmlspecialchars($task['description']); ?></p>
                            <small>Due: <?php echo $task['due_date']; ?></small>
                        </div>
                        <div>
                            <span class="badge <?php echo $task['priority']; ?>"><?php echo $task['priority']; ?></span>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No tasks found for this project yet.</p>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>