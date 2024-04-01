<?php
require_once('../ulogin/config/all.inc.php');
require_once('../ulogin/main.inc.php');

if (!sses_running())
    sses_start();

function isAppLoggedIn(){
    return isset($_SESSION['uid']) && isset($_SESSION['username']) && isset($_SESSION['loggedIn']) && ($_SESSION['loggedIn']===true);
}

if (!isAppLoggedIn()) {
    header("Location: ../index.php"); /* Redirect browser */
    exit();
} 

require_once(dirname(__FILE__) . '/../DataLink/AccessLayer.php');

$accessLayer = new AccessLayer();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inputText'])) {
    $newLoopName = $_POST['inputText'];
    $accessLayer->add_loop($newLoopName);
}

$results = $accessLayer->get_loops();
?>

<?php
require '../themepart/resources.php';
require '../themepart/sidebar.php';
require '../themepart/pageContentHolder.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Loops</title>
</head>
<body>
    <div align="center">
        <h1>Manage Loops</h1>

        <div class="d-flex justify-content-center">
            <h3>Create a New Loop</h3>
        </div>
        <div class="d-flex justify-content-center">
            <form action="" method="post">
                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <label class="sr-only" for="inlineFormInput">Loop Name</label>
                        <input type="text" input="text" class="form-control mb-2" name='inputText' id="inlineFormInput" placeholder="enter loop name">
                    </div>
                    <div class="col-auto">
                        <button type="submit" name="SubmitButton" class="btn btn-dark mb-2">Create</button>
                    </div>
                </div>
            </form>
        </div>

        <table id="editable_table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Loop</th>
                    <th></th> 
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $loop) : ?>
                    <tr>
                        <td><?php echo $loop->loops; ?></td>
                        <td>
                            <button style="background-color: blue;"> <a href="Routes.php" class="view-details-link" data-loop-id="123">View Details</a></button>
                        </td>
                        <td style="display:none;"><?php echo $loop->id; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


    <script>
    $(document).ready(function() {
        $('.view-details-btn').click(function() {
            event.preventDefault();
            var loopId = $(this).data('loop-id');
            window.location.href = 'routes' + loopId;
        });
    });
</script>
</body>
</html>
<?php require '../themepart/footer.php'; ?>
<?php

require_once(dirname(__FILE__) . '/../config.php');

include_once('../Model/User.php');

?>
