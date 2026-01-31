<?php
include('../../includes/header.php');


$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');


$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDayOfMonth = date('w', strtotime("$year-$month-01")); 
$monthName = date('F', mktime(0, 0, 0, $month, 10));


$reservations = [];
$query = "SELECT * FROM reservations WHERE MONTH(start_date) = '$month' AND YEAR(start_date) = '$year'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($result)) {
    
    $day = date('j', strtotime($row['start_date']));
    $reservations[$day][] = $row;
}


$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }

$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }
?>

<div class="panel-header" style="margin-bottom: 20px; border-bottom: none;">
    <div>
        <h1 style="font-size: 1.5rem; color: var(--deep-navy);">Chapel Schedule</h1>
        <p style="color: var(--text-light);">Manage viewing dates and prevent conflicts.</p>
    </div>
    <a href="add_reservation.php" class="btn btn-primary"><i class="fas fa-plus"></i> Book New Service</a>
</div>

<div class="panel"> 
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="btn" style="background: #eee;"><i class="fas fa-chevron-left"></i></a>
        <h2 style="margin: 0; color: var(--deep-navy);"><?= $monthName . " " . $year ?></h2>
        <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="btn" style="background: #eee;"><i class="fas fa-chevron-right"></i></a>
    </div>
 
    <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; text-align: center;">
 
        <div style="font-weight: bold; padding: 10px; color: var(--muted-teal);">Sun</div>
        <div style="font-weight: bold; padding: 10px; color: var(--muted-teal);">Mon</div>
        <div style="font-weight: bold; padding: 10px; color: var(--muted-teal);">Tue</div>
        <div style="font-weight: bold; padding: 10px; color: var(--muted-teal);">Wed</div>
        <div style="font-weight: bold; padding: 10px; color: var(--muted-teal);">Thu</div>
        <div style="font-weight: bold; padding: 10px; color: var(--muted-teal);">Fri</div>
        <div style="font-weight: bold; padding: 10px; color: var(--muted-teal);">Sat</div>
 
        <?php for($i=0; $i<$firstDayOfMonth; $i++): ?>
            <div style="background: #fafafa; height: 120px; border-radius: 8px;"></div>
        <?php endfor; ?>
 
        <?php for($day=1; $day<=$daysInMonth; $day++): ?>
            <div style="background: var(--white); border: 1px solid #eee; height: 120px; border-radius: 8px; position: relative; padding: 5px; text-align: left; transition: 0.3s; overflow-y: auto;">
                <span style="font-weight: 600; color: var(--deep-navy); display: block; margin-bottom: 5px;"><?= $day ?></span>
                
                <?php if(isset($reservations[$day])): ?>
                    <?php foreach($reservations[$day] as $res): ?>
                        <div style="background: var(--slate-blue); color: white; font-size: 0.7rem; padding: 4px; border-radius: 4px; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: pointer;" title="<?= $res['deceased_name'] ?> (<?= $res['chapel_name'] ?>)">
                            <?= date('H:i', strtotime($res['start_date'])) ?> - <?= $res['deceased_name'] ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>