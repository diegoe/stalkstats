<?php
include 'stalk.lib.php';

$links = file("runners.txt");
$len = strlen("http://thecreativechallenge.org/pdetail/");
$videos = array();

echo "STARTING:  " . date('r') . "\n";
echo "Getting all the votes\n";
foreach ($links as $line => $url) {
    $url = trim($url);
    $facebook = get_likes($url);
    $twitter = get_tweets($url);
    $google = get_plusones($url);
    $linkedin = get_linkedin($url);
    $total = $facebook + $twitter + $google + $linkedin;
    $name = substr($url, $len, -1);

    $videos[$name] = $total;
}

arsort($videos);

$print = "REPORT FOR:  " . date('r') . "\n";
$print .= "-------\n";
foreach ($videos as $video => $votes) {
    $name = str_pad(substr($video, 0, 20), 20, " ");
    $print .= "${name}\t\t${votes} votes\n";
}
$print .= "-------\n";
$print .= "REPORT DONE:  " . date('r') . "\n";

echo $print;

$reportname = "report-".date('Ymd')."-".date('His');
file_put_contents($reportname, $print);
?>
