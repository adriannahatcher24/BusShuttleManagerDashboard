<?php
    session_start();
    require '../Database/connect.php';


    $hourly = array();
    $entries = array();
    $input = "";
    $loopDropdown = array();
    $loop ="";


    $sql = sprintf("SELECT * FROM loops");
    // Populating the loops dropdown
    if($result = mysqli_query($con,$sql)) {
        while($row = mysqli_fetch_assoc($result)) {
            array_push($loopDropdown, $row);
        }
        } else {
        http_response_code(404);
        }

        // $sql = sprintf("SELECT * FROM Entries LIMIT 100");
        // // Populating the page
        // if($result = mysqli_query($con,$sql)) {
        //     while($row = mysqli_fetch_assoc($result)) {
        //         array_push($entries, $row);
        //     }
        //     } else {
        //     http_response_code(404);
        //     }

        
    // If post occurs
    if(isset($_POST['SubmitButton'])){
        $input = $_POST['loop'];
        $dateInput = $_POST['dateInput'];
        if($input != '' && $dateInput != '') {

        $newDate = date("Y-m-d", strtotime($dateInput));
        makeList($entries, $con, $newDate, $input);
        

        }
        // header('Location: Entries.php');
  
    }

    if(isset($_POST['HourlyButton'])){
        $input = $_POST['loop'];
        $dateInput = $_POST['dateInput'];
        if($input != '' && $dateInput != '') {

        $newDate = date("Y-m-d", strtotime($dateInput));
        
        showHourly($hourly, $con, $input, $input);

        }
        // header('Location: Entries.php');
  
    }



    
    function makeList(&$entries, $con, $input, $loop) {
        $sql = sprintf("SELECT * FROM `Entries` WHERE `date`='$input' AND `loop`= '$loop'");
    
        if($result = mysqli_query($con,$sql)) {
        while($row = mysqli_fetch_assoc($result)) {
            array_push($entries, $row);
        }
        } else {
        http_response_code(404);
        }
    }


    function showHourly(&$hourly, $con, $input, $loop){
        $hour =  0;

        for($hour=0; $hour<24; $hour++){
            $sql = sprintf("SELECT SUM(`boarded`) as `boarded` from `entries` where `loop` = '$loop' and `timestamp` BETWEEN '2019-01-29 $hour:00:00' and '2019-01-29 $hour:59:59'");
            if($result = mysqli_query($con,$sql)) {
            while($row = mysqli_fetch_assoc($result)) {
                array_push($hourly, $row);
            }
            } else {
            http_response_code(404);
            }
        }
    
    }



?>


<!--  -->


<?php
        require '../themepart/resources.php';
        require '../themepart/sidebar.php';
        require '../themepart/pageContentHolder.php';
    ?>


<HTML LANG="EN">

<HEAD>


</HEAD>

<form method=post>

</form>

<div class="d-flex justify-content-center">
    <form action="" method="post">
      <div class="form-row align-items-center">
         <div class="col-auto">
             <input class="form-control mb-2" input="text" name="dateInput" id="datepicker" width="276" />
            </div>
         <div class="col-auto">
                                    <select class="form-control mb-2" name="loop" id="loop">
                                        <option selected="selected">Select a Loop</option>
                                        <?php
                            foreach($loopDropdown as $name) { ?>
                                        <option name="loop" value="<?= $name['loops'] ?>"><?= $name['loops'] ?>
                                        </option>
                                        <?php
                            } ?>
                                    </select>
                                </div>
        <div class="col-auto">
          <button type="submit" name="SubmitButton" class="btn btn-dark mb-2">Submit</button>
          
          <button type="submit" name="HourlyButton" class="btn btn-dark mb-2">Filter by Hour</button>

        </div>
        </div>
    </form>
    </div>


    <script>
        $('#datepicker').datepicker();
    </script>

<body>


    <table id="editable_table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Boarded</th>
                <th>Stop</th>
                <th>Time</th>
                <th>Date</th>
                <th>Loop</th>
                <th>Driver</th>
                <th>Left Behind</th>
            </tr>
        </thead>
        <tbody class="row_position">
            <?php foreach ($entries as $log): ?>
            <tr id="<?php echo $log['id'] ?>">
                <td><?php echo $log['boarded']; ?></td>
                <td><?php echo $log['stop']; ?></td>
                <td><?php echo $log['timestamp']; ?></td>
                <td><?php echo $log['date']; ?></td>
                <td><?php echo $log['loop']; ?></td>
                <td><?php echo $log['driver']; ?></td>
                <td><?php echo $log['leftBehind']; ?></td>
                <td style="display:none;"><?php echo $log['id']; ?></td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    
    <table id="editable_table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Time</th>
                <th>Boarded</th>
                
            </tr>
        </thead>

        <?php $time = 0; ?>


<!-- This controls the hourly display -->
        <tbody class="row_position">
            <?php foreach ($hourly as $log): ?>

                <td><?php echo "$time:00 - $time:59"; ?></td>
                <td><?php echo 0 + $log['boarded']; ?></td>
                <?php $time = $time + 1; ?>


                <td style="display:none;"><?php echo $log['id']; ?></td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>


</body>

<script>
$(document).ready(function() {
    $('#editable_table').Tabledit({
        url: '../Actions/actionEntries.php',
        hideIdentifier: true,
        columns: {
            identifier: [7, 'id'],
            editable: [
                [0, 'boarded'],
                [1, 'stop'],
                [2, 'timestamp'],
                [3, 'date'],
                [4, 'loop'],
                [5, 'driver'],
                [6, 'leftBehind']
            ]
        }
    });

});

$(".row_position").sortable({
    delay: 150,
    stop: function() {
        var selectedData = new Array();
        $('.row_position>tr').each(function() {
            var test = $(this).attr("id");
            selectedData.push($.trim(test));
        });
        console.log(selectedData);
        updateOrder(selectedData);
    }
});


function updateOrder(data) {
    $.ajax({
        url: "../Actions/actionEntries.php",
        type: 'post',
        data: {
            position: data
        },
        success: function() {
            alert('your change successfully saved');
        }
    })
}
</script>



</HTML>

<?php require '../themepart/footer.php'; ?>