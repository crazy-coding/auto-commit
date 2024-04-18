<?php
include __DIR__."/services/db.php";

// Get all
$sql = "
SELECT DATE(al.commit_at) AS commit_date, SUM(al.commits) AS total_commits, ag.username
FROM autocommits_logs al
LEFT JOIN autocommits_gitinfos ag ON ag.id = al.gitinfo_id
GROUP BY DATE(al.commit_at), ag.username
ORDER BY commit_date DESC
LIMIT 100;
";
$result = $conn->query($sql);
$view_logs = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $view_logs[] = $row;
  }
} else {
  echo "0 results";
}

// Set page title
$pageTitle = "Auto Commits - Logs";

ob_start();
?>
<div class="container-fluid">
  <!-- Page Heading -->
  <h1 class="h3 mb-2 text-gray-800">Auto Commits / Logs</h1>
  <p class="mb-4">Git infomations that can be used for daily auto commits.</p>

  <!-- Logs cards -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Logs</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>User Name</th>
              <th>Commits</th>
              <th>Commit At</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($view_logs as $log) { ?>
              <tr>
                <td><?= $log["username"] ?></td>
                <td><?= $log["total_commits"] ?></td>
                <td><?= $log["commit_date"] ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php
// Set content file
$contentView = ob_get_clean();

// Include login layout
include __DIR__."/layouts/admin.php";
?>