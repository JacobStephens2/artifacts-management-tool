<?php 
require_once('../../artifacts_private/initialize.php');
require_login();
$plural_page_name = 'Users';
$singular_page_name = 'User';
$player_set = find_players_by_user_id();
$page_title = 'Users';
include(SHARED_PATH . '/header.php');
?>

<main>
  <div class="objects listing">
    <h1><?php echo $plural_page_name; ?></h1>

    <div class="actions">
      <a class="action" href="<?php echo url_for('/users/new.php'); ?>">Add New <?php echo $singular_page_name; ?></a>
    </div>

  	<table class="list">
  	  <tr id="headerRow">
        <th>Name (<?php echo $player_set->num_rows; ?>)</th>
        <th>Gender</th>
  	    <th>Age</th>
        <th></th>
  	  </tr>

      <?php while($player = mysqli_fetch_assoc($player_set)) { ?>
        <tr>
          <td>
            <a class="table-action" href="<?php echo url_for('/users/edit.php?id=' . h(u($player['id']))); ?>">
              <?php echo h($player['FirstName']) . ' ' . h($player['LastName']); ?>
            </a>
          </td>
    	    <td><?php echo h($player['G']); ?></td>
    	    <td><?php echo h($player['Age']); ?></td>
          <td><a class="table-action" href="<?php echo url_for('/users/delete.php?id=' . h(u($player['id']))); ?>">Delete</a></td>
    	  </tr>
      <?php } ?>
  	</table>

    <?php
      mysqli_free_result($player_set);
    ?>
  </div>

</main>

<?php include(SHARED_PATH . '/footer.php'); ?>