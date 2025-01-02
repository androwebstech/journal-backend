<?php $title = 'Daily Reports © '.date('Y').'(All rights reserved to AndroWebsTech Pvt Ltd)'; ?>
<html>
    <head><title><?=$title?></title></head>
    <body>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
<?php
if(!empty($records) && count($records) > 0){
    echo'
    <img src="https://sadarhospitalsitamarhi.org/assets/header.png" style="width:100%;"/>
    <br/>
    <table class="table">
            <thead>
                <tr>
                <th>Head Name</th>
                <th>Head Count</th>
                <th>Remarks</th>
                <th>Department</th>
                <th>Created By</th>
                <th>Created At</th>
                </tr>
            <thead>
            <tbody>';
        foreach($records as $row){
            echo'<tr><td>'.$row['head_name'].'</td>
                    <td>'.$row['count'].'</td>
                    <td>'.$row['remarks'].'</td>
                    <td>'.$row['department_name'].'</td>
                    <td>'.$row['fname'].' '.$row['lname'].'</td>
                    <td>'.$row['created_at'].'</td>
                </tr>';
        }
    echo '</tbody></table>
    ';

}else{
    echo'<h3>No Records to show</h3>';
}
?>

<div style="position: fixed;bottom: 0;width: 100%;text-align: center;"><?=$title?></div>
</body>
</html>