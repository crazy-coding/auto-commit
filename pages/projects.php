<?php
// Set page title
$pageTitle = "Portfolios - Projects";

ob_start();
?>
<div class="container-fluid">
</div>
<?php
// Set content file
$contentView = ob_get_clean();

// Include login layout
include "layouts/admin.php";
?>