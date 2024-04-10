<?php
include __DIR__."/services/db.php";

$directory = __DIR__."/../../tmp";

function auto_commit($gitinfo, $directory) {
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
  $repo = str_replace('https://', '', $gitinfo["url"]);
  $branch = $gitinfo["branch"] ?? "main";
  $username = $gitinfo["username"];
  $useremail = $gitinfo["useremail"];
  $token = $gitinfo["token"];
  if ($cronoptions["commits"]) {
    $min = explode(',', $cronoptions["commits"])[0];
    $max = explode(',', $cronoptions["commits"])[1];
  } else {
    $min = 0;
    $max = 5;
  }
  if (date("w") == 6 || date("w") == 0) {
    if ($cronoptions["workdays"]) {
      switch ($cronoptions["workdays"]) {
        case "full":
          break;
        case "sometimes":
          if (rand(0,1)) {
            echo "skip weekend";
            return;
          }
          break;
        case "never":
        default:
          echo "skip weekend";
          return;
      }
    } else {
      echo "skip weekend";
      return;
    }
  }

  // Step 1: Clone the repository
  exec("git clone -b $branch https://$username:$token@$repo $directory/auto-commits-repo");
  chdir("$directory/auto-commits-repo"); // Move to the cloned repository directory

  exec("git config user.name $username");
  exec("git config user.email $useremail");

  $fileLines = file("README.md");

  if (count($fileLines) > 30) {
      $fileLines = array_slice($fileLines, 0, -20);
      file_put_contents("README.md", implode("", $fileLines));
  }

  // Step 2: Repeat updating README.md for a random number of times
  $numUpdates = rand($min, $max);
  for ($i = 0; $i < $numUpdates; $i++) {

    $currentDateTime = date("Y-m-d H:i:s");
    file_put_contents("README.md", "\nUpdated on: $currentDateTime", FILE_APPEND);

    exec("git add README.md");
    exec("git commit -m 'Updated README.md with current datetime'");
  }

  // Step 3: Git push all commits
  exec("git push origin $branch");

  // Step 4: Remove cloned repository
  chdir($directory); // Move back to the parent directory
  exec("rm -rf auto-commits-repo"); // Remove cloned repository directory

  echo $numUpdates." commits pushed.\n";
}

echo "Started cronjob : ".date("Y-m-d")."\n";
$sql = "SELECT * FROM autocommits_gitinfos";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    auto_commit($row, $directory);
  }
  echo "Completed cronjob successfully!!\n";
} else {
  echo "No git infos.\n";
}
?>