<?php

namespace Post;
/*

use function Post\handle_url_request;

*/
// https://mdwiki.org/w/rest.php/v1/page/Sympathetic_crashing_acute_pulmonary_edema/html
// https://mdwiki.org/w/rest.php/v1/revision/1420795/html

use function Printn\test_print;

function handle_url_request(string $endPoint, string $method = 'GET', array $params = []): string
{
    $usr_agent = 'WikiProjectMed Translation Dashboard/1.0 (https://mdwikicx.toolforge.org/; tools.mdwikicx@toolforge.org)';

    $ch = curl_init();

    $url = $endPoint;
    $printableUrl = $endPoint;

    if (!empty($params) && $method === 'GET') {
        $query_string = http_build_query($params);
        $url = strpos($url, '?') === false ? "$url?$query_string" : "$url&$query_string";
        $printableUrl = $url;
        $endPoint = $url;
    }

    curl_setopt($ch, CURLOPT_URL, $endPoint);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    test_print($printableUrl);

    $output = curl_exec($ch);

    if ($output === false) {
        test_print("endPoint: ($endPoint), cURL Error: " . curl_error($ch));
        curl_close($ch);
        return '';
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code !== 200) {
        error_log("HttpClientService: API returned HTTP $http_code for URL: $printableUrl");

        // Check for Cloudflare protection
        $isCloudflareProtected = false;
        if (is_string($output) && str_contains($output, 'Just a moment...')) {
            $isCloudflareProtected = true;
            error_log("HttpClientService: Cloudflare protection detected for URL: $printableUrl");
            test_print("Cloudflare protection detected: 'Just a moment...' page returned");
        }

        test_print("API returned HTTP $http_code: $http_code");
        if (!$isCloudflareProtected) {
            test_print(var_export($output, true));
        }
        $output = '';
    }

    curl_close($ch);

    return $output;
}
