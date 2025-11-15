<?php
function redirect($url) {
    header("Location: $url");
    exit();
}

function displayMessage($type, $message) {
    return '<div class="alert alert-' . $type . '">' . $message . '</div>';
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>