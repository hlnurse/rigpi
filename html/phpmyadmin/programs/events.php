<?php
include_once "db_connect.php";
$sqlEvents = "SELECT id, title, start_date, end_date FROM events LIMIT 20";
($resultset = mysqli_query($conn, $sqlEvents)) or
  die("database error:" . mysqli_error($conn));
$calendar = [];
while ($rows = mysqli_fetch_assoc($resultset)) {
  // convert  date to milliseconds
  $start = strtotime($rows["start_date"]) * 1000;
  $end = strtotime($rows["end_date"]) * 1000;
  $calendar[] = [
    "id" => $rows["id"],
    "title" => $rows["title"],
    "url" => "#",
    "class" => "event-important",
    "start" => "$start",
    "end" => "$end",
  ];
}
$calendarData = [
  "success" => 1,
  "result" => $calendar,
];
echo json_encode($calendarData);
?>
