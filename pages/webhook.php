<?php
include "services/db.php";

function auto_commit($gitinfo) {
  echo "Git user [".$gitinfo['username']."] is commiting... ";

  // Get cron option values
  $pattern = '/--(\w+)=([^\s]+)/';
  $cronoptions = array();
  preg_match_all($pattern, $gitinfo["cronoption"], $matches, PREG_SET_ORDER);
  foreach ($matches as $match) {
      $key = $match[1];
      $value = $match[2];
      $cronoptions[$key] = $value;
  }

  // Set variables
  $repo = $gitinfo["url"];
  $branch = $gitinfo["branch"];
  $username = $gitinfo["username"];
  $token = $gitinfo["token"];
  $min = explode(',', $cronoptions["commits"])[0] ?? 0;
  $max = explode(',', $cronoptions["commits"])[1] ?? 5;

  // Step 1: Clone the repository
  exec("git clone -b $branch https://$username:$token@$repo");

  // Step 2: Update README.md with current datetime and create commit
  $clonedRepo = basename($repo, ".git");
  chdir($clonedRepo); // Move to the cloned repository directory

  // Step 3: Repeat updating README.md for a random number of times
  $numUpdates = rand($min, $max);
  for ($i = 0; $i < $numUpdates; $i++) {
      $currentDateTime = date("Y-m-d H:i:s");
      file_put_contents("README.md", "\nUpdated on: $currentDateTime", FILE_APPEND);

      exec("git add README.md");
      exec("git commit -m 'Updated README.md with current datetime'");
  }

  // Step 4: Git push all commits
  exec("git push origin $branch");

  // Step 5: Remove cloned repository
  chdir(".."); // Move back to the parent directory
  exec("rm -rf $clonedRepo"); // Remove cloned repository directory

  echo $numUpdates." commits pushed.\n";
}

echo "Started cronjob : ".date("Y-m-d")."\n";
$sql = "SELECT * FROM autocommits_gitinfos";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    auto_commit($row);
  }
  echo "Completed cronjob successfully!!\n";
} else {
  echo "No git infos.\n";
}
?>