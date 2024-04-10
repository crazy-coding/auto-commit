<?php
include __DIR__."/services/db.php";
// Get id
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
  $sql = "SELECT * FROM portfolios_projects WHERE id = " . $_GET['id'];
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
$sql = "SELECT * FROM portfolios_projects";
$result = $conn->query($sql);
$view_projects = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $view_projects[] = $row;
  }
} else {
  echo "0 results";
}

// Delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['_method'] == "DELETE") {
  $id = $_POST['id'];

  $sql = "DELETE FROM portfolios_projects WHERE id=$id";

  if ($conn->query($sql) === TRUE) {
    echo "Record deleted successfully";
    $conn->close();
    header('Location: projects.php');
  } else {
    echo "Error deleting record: " . $conn->error;
  }
}

// Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['id']) {
  $id = $_POST['id'];
  $form = [
    "title" => $_POST['title'],
    "name" => $_POST['name'],
    "summary" => $_POST['summary'],
    "description" => $_POST['description'],
    "explanation" => $_POST['explanation'],
    "skills" => $_POST['skills'],
    "duration" => $_POST['duration'],
    "images" => $_POST['images'],
  ];

  $updateStatements = array();
  foreach ($form as $key => $value) {
    $escapedValue = mysqli_real_escape_string($conn, $value);
    $updateStatements[] = "$key = '$escapedValue'";
  }
  $updateString = implode(", ", $updateStatements);
  $sql = "UPDATE portfolios_projects SET $updateString WHERE id = $id";

  if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
    $conn->close();
    header('Location: projects.php');
  } else {
    echo "Error updating record: " . $conn->error;
  }
}

// Insert
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $form = [
    "title" => $_POST['title'],
    "name" => $_POST['name'],
    "summary" => $_POST['summary'],
    "description" => $_POST['description'],
    "explanation" => $_POST['explanation'],
    "skills" => $_POST['skills'],
    "duration" => $_POST['duration'],
    "images" => $_POST['images'],
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
  $sql = "INSERT INTO portfolios_projects ($fieldNamesString) VALUES ($fieldValuesString)";
  if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
    $conn->close();
    header('Location: projects.php');
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}

// Set page title
$pageTitle = "Portfolios - Projects";

ob_start();
?>
<div class="container-fluid">
  <!-- Page Heading -->
  <h1 class="h3 mb-2 text-gray-800">Portfolios / Projects</h1>
  <p class="mb-4">The projects that can be used for portfolios websites.</p>

  <!-- Projects cards -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Projects</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Title</th>
              <th>Name</th>
              <th>Summary</th>
              <th>Description</th>
              <th>Explanation</th>
              <th>Skills</th>
              <th>Duration</th>
              <th>Images</th>
              <th>
                <button class="btn btn-success" onclick="newItem()">
                  <i class="fas fa-plus fa-sm fa-fw"></i>
                </button>
              </th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($view_projects as $project) { ?>
              <tr>
                <td><?= $project["title"] ?></td>
                <td><?= $project["name"] ?></td>
                <td><?= $project["summary"] ?></td>
                <td><?= $project["description"] ?></td>
                <td><?= $project["explanation"] ?></td>
                <td><?= $project["skills"] ?></td>
                <td><?= $project["duration"] ?> Months</td>
                <td><?= $project["images"] ?></td>
                <td>
                  <div class="d-flex">
                    <button class="btn btn-primary mr-2" onclick="editItem(<?= $project['id'] ?>)">
                      <i class="fas fa-pen fa-sm fa-fw"></i>
                    </button>
                    <button class="btn btn-danger" onclick="deleteItem(<?= $project['id'] ?>)">
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
            <h5 class="modal-title" id="deleteModalLabel">Project</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="number" class="project-input" hidden name="id" id="delete_project_id">
            <p>Are you sure to delete this project?</p>
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
            <h5 class="modal-title" id="upsertModalLabel">Project</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="number" class="project-input" hidden name="id" id="project_id">
            <div class="form-group">
              <label for="project_title">Title</label>
              <input type="text" class="form-control project-input" name="title" id="project_title">
            </div>
            <div class="form-group">
              <label for="project_name">Name</label>
              <input type="text" class="form-control project-input" name="name" id="project_name">
            </div>
            <div class="form-group">
              <label for="project_summary">Summary</label>
              <textarea class="form-control project-input" name="summary" id="project_summary" rows="3"></textarea>
            </div>
            <div class="form-group">
              <label for="project_description">Description</label>
              <textarea class="form-control project-input" name="description" id="project_description" rows="4"></textarea>
            </div>
            <div class="form-group">
              <label for="project_explanation">Explanation</label>
              <textarea class="form-control project-input" name="explanation" id="project_explanation" rows="6"></textarea>
            </div>
            <div class="form-group">
              <label for="project_skills">Skills</label>
              <textarea class="form-control project-input" name="skills" id="project_skills" rows="3"></textarea>
            </div>
            <div class="form-group">
              <label for="project_duration">Duration</label>
              <select class="form-control project-input" name="duration" id="project_project_duration">
                <option value="1">A Month</option>
                <option value="2">2 Months</option>
                <option value="6">Half a Year</option>
                <option value="12">A Year</option>
                <option value="24">2 Years</option>
              </select>
            </div>
            <div class="form-group">
              <label for="project_images">Images</label>
              <textarea class="form-control project-input" name="images" id="project_images" rows="5"></textarea>
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
      $(".project-input").val('');
      $("#upsertModalLabel").text("New Project");
      $('#upsertModal').modal('show');
    }

    function editItem(id) {
      fetch(`/pages/projects.php?id=${id}`)
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          // Handle the retrieved data
          console.log(data);
          $("#upsertModalLabel").text("Edit Project - " + data.id);
          $("#project_id").val(data.id);
          $("#project_title").val(data.title);
          $("#project_name").val(data.name);
          $("#project_summary").val(data.summary);
          $("#project_description").val(data.description);
          $("#project_explanation").val(data.explanation);
          $("#project_skills").val(data.skills);
          $("#project_duration").val(data.duration);
          $("#project_images").val(data.images);

          $('#upsertModal').modal('show');
        })
        .catch(error => {
          // Handle errors
          console.error('There was a problem with the fetch operation:', error);
        });
    }

    function deleteItem(id) {
      $("#delete_project_id").val(id);
      $("#deleteModalLabel").text("Delete Project - " + id);
      $('#deleteModal').modal('show');
    }
  </script>
</div>
<?php
// Set content file
$contentView = ob_get_clean();

// Include login layout
include __DIR__."/layouts/admin.php";
?>