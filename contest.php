<?php
include 'stalk.lib.php';

$links = file("runners.txt");
$len = strlen("http://thecreativechallenge.org/pdetail/");
$videos = array();

echo "STARTING:  " . date('r') . "\n";
echo "Getting all the votes\n";
foreach ($links as $line => $url) {
    $url = trim($url);
    /*
     */
    $facebook = get_likes($url);
    $twitter = get_tweets($url);
    $google = get_plusones($url);
    $linkedin = get_linkedin($url);
    $total = $facebook + $twitter + $google + $linkedin;
    $name = substr($url, $len, -1);

    $videos[$name] = $total;
}

arsort($videos);

foreach ($videos as $video => $votes) {
    $name = str_pad(substr($video, 0, 20), 20, " ");
    echo "${name}\t\t${votes} votes\n";
}
echo "-------\n";
echo "REPORT DONE:  " . date('r') . "\n";
?>
