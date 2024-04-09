<?php
include "services/db.php";
// Get id
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
  $sql = "SELECT * FROM autocommits_gitinfos WHERE id = " . $_GET['id'];
  $result = $conn->query($sql);
  $data = array();
  if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
  }
  header('Content-Type: application/json');
  echo json_encode($data);

  // Step 6: Close connection
  $conn->close();
  exit;
}

// Get all
$sql = "SELECT * FROM autocommits_gitinfos";
$result = $conn->query($sql);
$view_options = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $view_options[] = $row;
  }
} else {
  echo "0 results";
}

// Delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['_method'] == "DELETE") {
  $id = $_POST['id'];

  $sql = "DELETE FROM autocommits_gitinfos WHERE id=$id";

  if ($conn->query($sql) === TRUE) {
    echo "Record deleted successfully";
    $conn->close();
    header('Location: options.php');
  } else {
    echo "Error deleting record: " . $conn->error;
  }
}

// Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['id']) {
  $id = $_POST['id'];
  $form = [
    "username" => $_POST['username'],
    "useremail" => $_POST['useremail'],
    "token" => $_POST['token'],
    "url" => $_POST['url'],
    "branch" => $_POST['branch'] ?? "main",
    "description" => $_POST['description'],
    "cronoption" => $_POST['cronoption'],
  ];

  $updateStatements = array();
  foreach ($form as $key => $value) {
    $escapedValue = mysqli_real_escape_string($conn, $value);
    $updateStatements[] = "$key = '$escapedValue'";
  }
  $updateString = implode(", ", $updateStatements);
  $sql = "UPDATE autocommits_gitinfos SET $updateString WHERE id = $id";

  if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
    $conn->close();
    header('Location: options.php');
  } else {
    echo "Error updating record: " . $conn->error;
  }
}

// Insert
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $form = [
    "username" => $_POST['username'],
    "useremail" => $_POST['useremail'],
    "token" => $_POST['token'],
    "url" => $_POST['url'],
    "branch" => $_POST['branch'] ?? "main",
    "description" => $_POST['description'],
    "cronoption" => $_POST['cronoption'],
  ];
  $fieldNames = array();
  $fieldValues = array();
  foreach ($form as $key => $value) {
    $escapedValue = mysqli_real_escape_string($conn, $value);
    $fieldNames[] = $key;
    $fieldValues[] = "'$escapedValue'";
  }
  $fieldNamesString = implode(", ", $fieldNames);
  $fieldValuesString = implode(", ", $fieldValues);
  $sql = "INSERT INTO autocommits_gitinfos ($fieldNamesString) VALUES ($fieldValuesString)";
  if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
    $conn->close();
    header('Location: options.php');
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}

// Set page title
$pageTitle = "Auto Commits - Options";

ob_start();
?>
<div class="container-fluid">
  <!-- Page Heading -->
  <h1 class="h3 mb-2 text-gray-800">Auto Commits / Options</h1>
  <p class="mb-4">Git infomations that can be used for daily auto commits.</p>

  <!-- Options cards -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Options</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>User Name</th>
              <th>User Email</th>
              <th>Token</th>
              <th>URL</th>
              <th>Branch</th>
              <th>Description</th>
              <th>Cron Option</th>
              <th>
                <button class="btn btn-success" onclick="newItem()">
                  <i class="fas fa-plus fa-sm fa-fw"></i>
                </button>
              </th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($view_options as $option) { ?>
              <tr>
                <td><?= $option["username"] ?></td>
                <td><?= $option["useremail"] ?></td>
                <td><?= $option["token"] ?></td>
                <td><?= $option["url"] ?></td>
                <td><?= $option["branch"] ?></td>
                <td><?= $option["description"] ?></td>
                <td><?= $option["cronoption"] ?></td>
                <td>
                  <div class="d-flex">
                    <button class="btn btn-primary mr-2" onclick="editItem(<?= $option['id'] ?>)">
                      <i class="fas fa-pen fa-sm fa-fw"></i>
                    </button>
                    <button class="btn btn-danger" onclick="deleteItem(<?= $option['id'] ?>)">
                      <i class="fas fa-trash fa-sm fa-fw"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Page level plugins -->
  <script a src="/assets/vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="/assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="/assets/js/demo/datatables-demo.js"></script>

  <!-- Delete Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form action="" method="POST">
          <input type="hidden" name="_method" value="DELETE">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Option</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="number" class="option-input" hidden name="id" id="delete_option_id">
            <p>Are you sure to delete this option?</p>
          </div>
          <div class="modal-footer">
            <button type="reset" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Upsert Modal -->
  <div class="modal fade" id="upsertModal" tabindex="-1" role="dialog" aria-labelledby="upsertModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form action="" method="POST">
          <div class="modal-header">
            <h5 class="modal-title" id="upsertModalLabel">Option</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="number" class="option-input" hidden name="id" id="option_id">
            <div class="form-group">
              <label for="option_username">User Name</label>
              <input type="text" class="form-control option-input" name="username" id="option_username">
            </div>
            <div class="form-group">
              <label for="option_useremail">User Email</label>
              <input type="text" class="form-control option-input" name="useremail" id="option_useremail">
            </div>
            <div class="form-group">
              <label for="option_token">Token</label>
              <input type="text" class="form-control option-input" name="token" id="option_token">
            </div>
            <div class="form-group">
              <label for="option_url">URL</label>
              <input type="text" class="form-control option-input" name="url" id="option_url">
            </div>
            <div class="form-group">
              <label for="option_branch">Branch</label>
              <input type="text" class="form-control option-input" placeholder="main" name="branch" id="option_branch">
            </div>
            <div class="form-group">
              <label for="option_description">Description</label>
              <textarea class="form-control option-input" name="description" id="option_description" rows="4"></textarea>
            </div>
            <div class="form-group">
              <label for="option_cronoption">Cron Option <i class="ml-2 fa fa-info fa-sm fa-fw" data-toggle="tooltip" title="--workdays=full|sometimes|never --commits=min,max"></i></label>
              <input type="text" class="form-control option-input" name="cronoption" id="option_cronoption">
            </div>
          </div>
          <div class="modal-footer">
            <button type="reset" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function newItem() {
      $(".option-input").val('');
      $("#upsertModalLabel").text("New Option");
      $('#upsertModal').modal('show');
    }

    function editItem(id) {
      fetch(`/pages/options.php?id=${id}`)
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          // Handle the retrieved data
          console.log(data);
          $("#upsertModalLabel").text("Edit Option - " + data.id);
          $("#option_id").val(data.id);
          $("#option_username").val(data.username);
          $("#option_useremail").val(data.useremail);
          $("#option_token").val(data.token);
          $("#option_url").val(data.url);
          $("#option_branch").val(data.branch);
          $("#option_description").val(data.description);
          $("#option_cronoption").val(data.cronoption);

          $('#upsertModal').modal('show');
        })
        .catch(error => {
          // Handle errors
          console.error('There was a problem with the fetch operation:', error);
        });
    }

    function deleteItem(id) {
      $("#delete_option_id").val(id);
      $("#deleteModalLabel").text("Delete Option - " + id);
      $('#deleteModal').modal('show');
    }
  </script>
</div>
<?php
// Set content file
$contentView = ob_get_clean();

// Include login layout
include "layouts/admin.php";
?>