<?php
require_once('../../private/initialize.php');
require_login();

if(is_post_request()) {
  $response = [];
  $response['Title'] = $_POST['Title'] ?? '';
  $response['PlayDate'] = $_POST['PlayDate'] ?? '';
  $response['Note'] = $_POST['Note'] ?? '';

  $response['Player1'] = $_POST['Player1'] ?? '';
  $response['Player2'] = $_POST['Player2'] ?? '';
  $response['Player3'] = $_POST['Player3'] ?? '';
  $response['Player4'] = $_POST['Player4'] ?? '';
  $response['Player5'] = $_POST['Player5'] ?? '';
  $response['Player6'] = $_POST['Player6'] ?? '';
  $response['Player7'] = $_POST['Player7'] ?? '';
  $response['Player8'] = $_POST['Player8'] ?? '';
  $response['Player9'] = $_POST['Player9'] ?? '';

  $playerCount = $_GET['playerCount'] ?? 1;

  $result = insert_response($response, $playerCount);

  if($result === true) {
    $new_id = mysqli_insert_id($db);
    $_SESSION['message'] = "The response was recorded successfully.";
    redirect_to(url_for('/uses/create.php'));
  } else {
    $errors = $result;
  }

} else {
  // display the blank form
  $response = [];
  $response["Title"] = '';
  $response["PlayDate"] = '';
  $response["Player"] = '';
  $playerCount = $_GET['playerCount'] ?? 1;
}

$page_title = 'Record Use';
include(SHARED_PATH . '/header.php'); 
?>

<main>
    <script>
      function getArtifacts() {
        fetch('https://<?php echo API_ORIGIN; ?>/artifacts.php', {
          method: 'GET',
          credentials: 'include',
        })
          .then((response) => response.json())
          .then(
            (data => {
              if (data.authenticated == false) {
                location.href = "/login.php?action=logout";
              } else {
                const titleSelect = document.querySelector('select#Title');
                titleSelect.innerHTML = '';
                for (let i = 0; i < data.artifacts.length; i++) {
                  let option = document.createElement('option');
                  option.value = data.artifacts[i].id;
                  option.innerText = data.artifacts[i].Title;
                  titleSelect.append(option);
                }
              }
            })
          )
        ;
      }
    </script>

    <h1>Record Use</h1>

    <!-- Player count form -->
    <form
      action="<?php echo url_for('/uses/create.php'); ?>"
      method="get"
      >
			<label for="playerCount">User Count</label>
      <select name="playerCount" id="playerCount">
        <?php
          $i = 1;
          while ($i < 10) {
            echo "<option value=\"" . $i . "\"";
            if($i == $playerCount) {
              echo " selected";
            }
            echo ">" . $i . "</option>";
            $i++;
          }
        ?>
      </select>
      <input type="submit" value="Select User Count" />
    </form>

    <!-- Create use form -->
    <form action="<?php echo url_for('/uses/create.php?playerCount=' . $playerCount); ?>" method="post">

      <!-- This select gets populated by the JavaScript fetch request above -->
      <label for="Title">Artifact</label>
			<select name="Title" id="Title">
      </select>

      <label for="SearchTitles">Search Artifacts</label>
      <input type="text" name="SearchTitles" id="SearchTitles">
      
      <label for="PlayDate">Response Date</label>
      <input 
        type="date" 
        id="PlayDate" 
        name="PlayDate" 
        value="<?php echo date('Y') . '-' . date('m') . '-' . date('d'); ?>"
      />

      <label for="SearchUser1">Search Users</label>
      <input type="text" name="SearchUser1" id="SearchUser1">

			<!-- Choose players -->
			<select id="User1" name="Player1">
				<option value='<?php echo $_SESSION['player_id']; ?>'>
          <?php echo $_SESSION['FullName']; ?>
        </option>
				<?php
					$player_set = list_players();
					while($player = mysqli_fetch_assoc($player_set)) {
						echo "<option value=\"" . h($player['id']) . "\">";
							echo h($player['FirstName']) . ' ' . h($player['LastName']);
						echo "</option>";
					}
					mysqli_free_result($player_set);
				?>
			</select>

      <script>
        getArtifacts();
        function searchUsers(e) {
          requestBody = {
            "query": e.target.value,
          };
          fetch('https://<?php echo API_ORIGIN; ?>/users.php', {
            method: 'POST',
            credentials: 'include',
            body: JSON.stringify(requestBody),
          })
            .then((response) => response.json())
            .then(
              (data => {
                const userSelect = document.querySelector('select#User1');
                userSelect.innerHTML = '';
                for (let i = 0; i < data.users.length; i++) {
                  let option = document.createElement('option');
                  option.value = data.users[i].id;
                  option.innerText = data.users[i].FullName;
                  userSelect.append(option);
                }
              })
            )
          ;
        }
        const searchUsersInput = document.querySelector('input#SearchUser1');
        searchUsersInput.addEventListener('input', searchUsers);
      </script>

      <?php
        $i = 1;
        $p = 2;
        while ($playerCount > $i) { ?>
          <input type="text" name="SearchUser<?php echo $p; ?>" id="SearchUser<?php echo $p; ?>">

          <select id="User<?php echo $p; ?>" name="Player<?php echo $p; ?>">
            <option value="">Choose a player</option>
            <?php
            $player_set = list_players();
            while($player = mysqli_fetch_assoc($player_set)) {
              echo "<option value=\"" . h($player['id']) . "\">";
                echo h($player['FirstName']) . ' ' . h($player['LastName']);
              echo "</option>";
            }
						mysqli_free_result($player_set); ?>     
          </select>

          <script>
            getArtifacts();
            function searchUsers(e) {
                requestBody = {
                  "query": e.target.value,
                };
                fetch('https://<?php echo API_ORIGIN; ?>/users.php', {
                  method: 'POST',
                  credentials: 'include',
                  body: JSON.stringify(requestBody),
                })
                  .then((response) => response.json())
                  .then(
                    (data => {
                      const userSelect = document.querySelector('select#User<?php echo $p; ?>');
                      userSelect.innerHTML = '';
                      for (let i = 0; i < data.users.length; i++) {
                        let option = document.createElement('option');
                        option.value = data.users[i].id;
                        option.innerText = data.users[i].FullName;
                        userSelect.append(option);
                      }
                    })
                  )
                ;
            }
            const searchUser<?php echo $p; ?>Input = document.querySelector('input#SearchUser<?php echo $p; ?>');
            searchUser<?php echo $p; ?>Input.addEventListener('input', searchUsers);
          </script>
          <?php
          $i++;
          $p++;
        }
      ?>

      <label for="Note">Note</label>
      <textarea 
        cols="30" 
        rows="5"
        name="Note" 
        id="Note"
        ></textarea>

			<input type="submit" value="Record Use" />

    </form>

    <!-- Append options to artifact and user select elements -->
    <script defer>
      getArtifacts();
      function searchArtifacts(e) {
        requestBody = {
          "query": e.target.value,
        };
        fetch('https://<?php echo API_ORIGIN; ?>/artifacts.php', {
          method: 'POST',
          credentials: 'include',
          body: JSON.stringify(requestBody),
        })
          .then((response) => response.json())
          .then(
            (data => {
              if (data.authenticated == false) {
                location.href = "/login.php";
              } else {
                const titleSelect = document.querySelector('select#Title');
                titleSelect.innerHTML = '';
                for (let i = 0; i < data.artifacts.length; i++) {
                  let option = document.createElement('option');
                  option.value = data.artifacts[i].id;
                  option.innerText = data.artifacts[i].Title;
                  titleSelect.append(option);
                }
              }
            })
          )
        ;
      }
      const searchTitlesInput = document.querySelector('input#SearchTitles');
      searchTitlesInput.addEventListener('input', searchArtifacts);

      getArtifacts();
    </script>

</main>

<?php include(SHARED_PATH . '/footer.php'); ?>