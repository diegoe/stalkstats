<?php
/*
 * stalkstats - Stalk a link's share count in all the social networks.
 *   Copyright © 2013  Diego Escalante Urrelo <diegoe@gmail.com>
 *   http://github.com/diegoe
 *
 * All code under MIT license.
 */
include 'stalk.lib.php';

if ($argc > 1) {
    $no_db = true;
    $url = $argv[1];
} else
    $url = "http://thecreativechallenge.org/pdetail/unlock-the-ad-free-web/";

$facebook = get_likes($url);
$twitter = get_tweets($url);
$google = get_plusones($url);
$linkedin = get_linkedin($url);

$total = $facebook + $twitter + $google + $linkedin;
$row = null;

if (!$no_db) {
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

    $sql_time = "SELECT * FROM stats ORDER BY datetime DESC LIMIT 1";
    $row = $db->query($sql_time)->fetchArray();

    if (!$row || ($date - $row['datetime']) > 3600) {
        $sql = "INSERT INTO stats VALUES (${date}, ${facebook}, ${twitter}, ${google}, ${linkedin}, ${total})";
        $db->exec($sql);
    } else {
        echo "No SQL record created: ${date} - ${row['datetime']} = ". ($date - $row['datetime']) .".\n";
        echo "-------\n";
    }
} else {

}

$d_facebook = '(+' . ($facebook - $row['facebook']) . ')';
$d_twitter = '(+' . ($twitter - $row['twitter']) . ')';
$d_google = '(+' . ($google - $row['googleplus']) . ')';
$d_linkedin = '(+' . ($linkedin - $row['linkedin']) . ')';
$d_total = '(+' . ($total - $row['total']) . ')';


echo "NOW:  " . date('r') . "\n";
echo "LAST: " . date('r', $row['datetime']) . "\n";
echo "-------\n";
echo "Facebook: " . $facebook . $d_facebook . "\n";
echo "Twitter: " . $twitter . $d_twitter . "\n";
echo "Google+: " . $google . $d_google . "\n";
echo "LinkedIn: " . $linkedin . $d_linkedin . "\n";
echo "-------\n";
echo "Total: " . $total . $d_total . "\n";
