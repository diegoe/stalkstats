<?php
/*
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
?>
