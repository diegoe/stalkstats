<?php
/*
 * stalkstats - Stalk a link's share count in all the social networks.
 *   Copyright © 2013  Diego Escalante Urrelo <diegoe@gmail.com>
 *   http://github.com/diegoe
 *
 * All code under MIT license.
 *
 * URLs and functions taken from:
 *   http://johndyer.name/getting-counts-for-twitter-links-facebook-likesshares-and-google-1-plusones-in-c-or-php/
 */
function get_linkedin($url) {
    $json_string = file_get_contents('http://www.linkedin.com/countserv/count/share?format=json&url=' . $url);
    $json = json_decode($json_string, true);
    return intval($json['count']);
}

function get_tweets($url) {
    $json_string = file_get_contents('http://urls.api.twitter.com/1/urls/count.json?url=' . $url);
    $json = json_decode($json_string, true);
    return intval($json['count']);
}
 
function get_likes($url) {
    $json_string = file_get_contents('http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=' . $url);
    $json = json_decode($json_string, true);
    return intval($json[0]['share_count']);
}
 
function get_plusones($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    $curl_results = curl_exec ($curl);
    curl_close ($curl);
 
    $json = json_decode($curl_results, true);
    return intval($json[0]['result']['metadata']['globalCounts']['count']);
}

if ($argc > 1)
    $url = $argv[1];
else
    $url = "http://thecreativechallenge.org/pdetail/unlock-the-ad-free-web/";

$facebook = get_likes($url);
$twitter = get_tweets($url);
$google = get_plusones($url);
$linkedin = get_linkedin($url);

$total = $facebook + $twitter + $google + $linkedin;

echo date("r") . "\n";
echo "-------\n";
echo "Facebook: " . $facebook . "\n";
echo "Twitter: " . $twitter . "\n";
echo "Google+: " . $google . "\n";
echo "LinkedIn: " . $linkedin . "\n";
echo "-------\n";
echo "Total: " . $total . "\n";

if (!file_exists ('db.db'))
    $new_db = true;
else
    $new_db = false;

$db = new SQLite3('db.db');
if ($new_db) {
    $sql_create = 'CREATE TABLE stats (datetime INTEGER, facebook INTEGER, twitter INTEGER, googleplus INTEGER, linkedin INTEGER, total INTEGER)';
    $db->exec($sql_create);
}

$date = time();

$sql_time = "SELECT datetime FROM stats ORDER BY datetime DESC LIMIT 1";
$row = $db->query($sql_time)->fetchArray();

if (!$row || ($date - $row['datetime']) > 3600) {
    $sql = "INSERT INTO stats VALUES (${date}, ${facebook}, ${twitter}, ${google}, ${linkedin}, ${total})";
    $db->exec($sql);
} else {
    echo "No SQL record created: ${date} - ${row['datetime']} = ". ($date - $row['datetime']) .".\n";
}
