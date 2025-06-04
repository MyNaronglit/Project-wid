<?php
require_once('server.php');
$db = new server();
$ierp = $db->connect_sql();

header('Content-Type: application/json');

function get_news($ierp){    
    $sql = "SELECT * FROM news ORDER BY news_id DESC LIMIT 5";
    $result = $ierp->query($sql);
    
    $newsData = [];
    while ($row = $result->fetch_assoc()) {
        $newsData[] = [
            "news_id" => $row["news_id"],
            "news_title" => $row["news_title"],
            "news_content" => $row["news_content"],
            "news_image" => $row["news_image"]
        ];
    }
    
    return $newsData;
}

echo json_encode(get_news($ierp));
?>
