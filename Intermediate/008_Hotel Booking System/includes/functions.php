<?php
function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function calculateNights($check_in, $check_out) {
    $start = new DateTime($check_in);
    $end = new DateTime($check_out);
    return $start->diff($end)->days;
}

function getRoomPrice($room_type) {
    $prices = [
        'single' => 100,
        'double' => 150,
        'suite' => 250,
        'deluxe' => 300
    ];
    return $prices[$room_type] ?? 0;
}
?>