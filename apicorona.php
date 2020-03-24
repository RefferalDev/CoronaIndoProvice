<?php
//get string between
function get_string_between($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

//jatim
function CoronaJatim()
{
    $url = "http://covid19dev.jatimprov.go.id/xweb/draxi";
    $get_contents = file_get_contents($url);
    $DOM = new \DOMDocument();
    $DOM->loadHTML(get_string_between($get_contents, '<tbody>', '</tbody>'));

    $trList = $DOM->getElementsByTagName("tr");
    $rows = [];
    foreach ($trList as $tr) {
        $row = [];
        foreach ($tr->getElementsByTagName("td") as $td) {
            $row[] = trim($td->textContent);
        }
        $rows[] = $row;
    }

    $aDataTableDetailHTML = [];
    foreach ($rows[0] as $col => $value) {
        $aDataTableDetailHTML[] = array_column($rows, $col);
    }

    $kota = $aDataTableDetailHTML[0];
    $odp = $aDataTableDetailHTML[1];
    $pdp = $aDataTableDetailHTML[2];
    $confirm = $aDataTableDetailHTML[3];

    $data_jatim = [];
    for ($i = 0; $i <= count($kota) - 1; $i++) {
        $data_jatim[$i]["city"] = $kota[$i];
        $data_jatim[$i]["odp"] = $odp[$i];
        $data_jatim[$i]["pdp"] = $pdp[$i];
        $data_jatim[$i]["confirm"] = $confirm[$i];
    }
    $count_odp = array_sum(array_column($data_jatim, 'odp'));
    $count_pdp = array_sum(array_column($data_jatim, 'pdp'));
    $count_confirm = array_sum(array_column($data_jatim, 'confirm'));
    return json_encode(["odp" => (int) $count_odp, "pdp" => (int) $count_pdp, "confirm" => (int) $count_confirm, "detail" => $data_jatim]);
}

//jateng
function CoronaJateng()
{
    $url = "https://corona.jatengprov.go.id/";
    $get_contents = file_get_contents($url);
    $count_odp = get_string_between($get_contents, '<p class="card-text" style="color:green;">', '</p>');
    $count_pdp = get_string_between($get_contents, '<p class="card-text" style="color:#0373fc;">', '</p>');
    $count_confirm = get_string_between($get_contents, '<p class="card-text" style="color:red;">', '</p>');
    return json_encode(["odp" => (int) $count_odp, "pdp" => (int) $count_pdp, "confirm" => (int) $count_confirm]);
}

//jabar
function CoronaJabar()
{
    $url = "https://covid19-public.digitalservice.id/analytics/aggregation/";
    $data_jabar = json_decode(file_get_contents($url), true);

    $last_key = end(array_keys(array_filter(array_column($data_jabar, 'positif'))));
    $data = (object) $data_jabar[$last_key];
    return json_encode(["odp" => (int) $data->total_odp, "pdp" => (int) $data->total_pdp, "confirm" => (int) $data->total_positif_saat_ini]);
}


//aceh
// $url = "https://dashboard.bravo.siat.web.id/public/dashboard/b1fcaade-589b-4620-a715-21d2d4cc234e";  

// var h1 = document.getElementsByTagName("h1");
// var hasil = [];
// hasil['odp'] = h1[1].textContent;
// hasil['pdp'] = h1[2].textContent;
// hasil['confirm'] = h1[5].textContent;
// console.log(hasil);

//sumbar
function CoronaSumbar()
{
    $url = "https://corona.sumbarprov.go.id/details/index_master_corona";
    $get_contents = file_get_contents($url);
    $DOM = new \DOMDocument();
    @$DOM->loadHTML(get_string_between($get_contents, '<tbody>', '</tbody>'));
    $trList = $DOM->getElementsByTagName("tr");
    $rows = [];
    foreach ($trList as $tr) {
        $row = [];
        foreach ($tr->getElementsByTagName("td") as $td) {
            $row[] = trim($td->textContent);
        }
        $rows[] = $row;
    }

    return json_encode(["odp" => (int) $rows[0][2], "pdp" => (int) $rows[0][3], "confirm" => (int) $rows[0][6]]);
}

//sumsel
function Coronasumsel()
{
    $url = "http://corona.sumselprov.go.id/index.php?module=home&id=1";
    $get_contents = file_get_contents($url);
    $DOM = new \DOMDocument();
    @$DOM->loadHTML($get_contents);
    $xp    = new \DOMXPath($DOM);
    $nodes = $xp->query('//font[@color="#006600"]');
    $result = [];
    foreach ($nodes as $element) {
        $nodes = $element->childNodes;
        foreach ($nodes as $node) {
            $result[] = $node->nodeValue;
        }
    }

    return json_encode(["odp" => (int) $result[0], "pdp" => (int) $result[1], "confirm" => (int) $result[2]]);
}

// lampung
function CoronaLampung()
{
    $url = [
        "https://geoportal.lampungprov.go.id/gis/rest/services/Kesehatan/COVID19_KABUPATEN/FeatureServer/0/query?f=json&where=pdp%20%3C%3E%200&returnGeometry=true&spatialRel=esriSpatialRelIntersects&geometry=%7B%22xmin%22%3A11271098.442811023%2C%22ymin%22%3A-626172.1357070468%2C%22xmax%22%3A11897270.578523021%2C%22ymax%22%3A0.000004950910806655884%2C%22spatialReference%22%3A%7B%22wkid%22%3A102100%7D%7D&geometryType=esriGeometryEnvelope&inSR=102100&outFields=*&orderByFields=pdp%20DESC&outSR=102100&resultType=tile",
        "https://geoportal.lampungprov.go.id/gis/rest/services/Kesehatan/COVID19_KABUPATEN/FeatureServer/0/query?f=json&returnGeometry=true&spatialRel=esriSpatialRelIntersects&geometry=%7B%22xmin%22%3A11271098.442811023%2C%22ymin%22%3A-1252344.2714190446%2C%22xmax%22%3A11897270.578523021%2C%22ymax%22%3A-626172.1357070468%2C%22spatialReference%22%3A%7B%22wkid%22%3A102100%7D%7D&geometryType=esriGeometryEnvelope&inSR=102100&outFields=*&outSR=102100&resultType=tile",
    ];

    $data_lampung = [];
    $key = 0;
    foreach ($url as $site) {
        $get_contents = json_decode(file_get_contents($site), true);
        foreach ($get_contents['features'] as $value) {
            $data_lampung[$key]['city'] = $value['attributes']['kabupaten'];
            $data_lampung[$key]['odp'] = (int) $value['attributes']['odp'];
            $data_lampung[$key]['pdp'] = (int) $value['attributes']['pdp'];
            $data_lampung[$key]['confirm'] = (int) $value['attributes']['hsp'];
            $key++;
        }
    }

    $count_odp = array_sum(array_column($data_lampung, 'odp'));
    $count_pdp = array_sum(array_column($data_lampung, 'pdp'));
    $count_confirm = array_sum(array_column($data_lampung, 'confirm'));
    return json_encode(["odp" => (int) $count_odp, "pdp" => (int) $count_pdp, "confirm" => (int) $count_confirm, "detail" => $data_lampung]);
}


//banten
function CoronaBanten()
{
    $url = "https://infocorona.bantenprov.go.id/home";
    $get_contents = file_get_contents($url);
    $DOM = new \DOMDocument();
    @$DOM->loadHTML(get_string_between($get_contents, '<!-- end Home Slider -->', '</section>'));
    $pList = $DOM->getElementsByTagName("b");
    $hasil = [];
    $arr = [
        PHP_EOL => "",
        "  " => ""
    ];
    foreach ($pList as $cal) {
        $hasil[] = explode(" ", strtr($cal->nodeValue, $arr))[0];
    }

    return json_encode(["odp" => (int) $hasil[0], "pdp" => (int) $hasil[1], "confirm" => (int) $hasil[2]]);
}

//sulsel
function CoronaSulsel()
{
    $url = "https://covid19.sulselprov.go.id";
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );

    $get_contents = file_get_contents($url, false, stream_context_create($arrContextOptions));
    $DOM = new \DOMDocument();
    @$DOM->loadHTML($get_contents);
    $xp    = new \DOMXPath($DOM);
    $nodes = $xp->query('//span[@style="font-size: 100px; font-weight:bold "]');
    $result = [];
    foreach ($nodes as $element) {
        $nodes = $element->childNodes;
        foreach ($nodes as $node) {
            $result[] = $node->nodeValue;
        }
    }

    return json_encode(["odp" => (int) $result[0], "pdp" => (int) $result[1], "confirm" => (int) $result[2]]);
}

//ntb
function CoronaNtb()
{
    $url = "https://corona.ntbprov.go.id/list-data";
    $get_contents = file_get_contents($url);
    $DOM = new \DOMDocument();
    @$DOM->loadHTML(get_string_between($get_contents, '<div class="table-responsive">', '</div>'));
    $trList = $DOM->getElementsByTagName("tr");
    $rows = [];
    foreach ($trList as $tr) {
        $row = [];
        foreach ($tr->getElementsByTagName("td") as $td) {
            $row[] = trim($td->textContent);
        }
        $rows[] = $row;
    }

    $result = [];
    $filter = [PHP_EOL => "", "  " => " ", "\n" => "", "   " => ""];
    $keys = 0;
    foreach ($rows as $key => $value) {
        if (!empty($value[0])) {
            $result[$keys]['city'] = strtr($value[0], $filter);
            $result[$keys]['odp'] = $value[5];
            $result[$keys]['pdp'] = $value[2];
            $result[$keys]['confirm'] = "Belum ada Data"; //sementara belum tersedia data
            $keys++;
        }
    }

    $key_end = end(array_keys($result));
    $data = $result[$key_end];
    unset($result[$key_end]);
    return json_encode(["odp" => (int) $data['odp'], "pdp" => (int) $data['pdp'], "confirm" => $data['confirm'], "detail" => $result]);
}


//kaltim
function CoronaKaltim()
{

    $url = "http://covid19.kaltimprov.go.id";
    $get_contents = file_get_contents($url);
    $DOM = new \DOMDocument();
    @$DOM->loadHTML($get_contents);
    $xp    = new \DOMXPath($DOM);
    $nodes = $xp->query('//div[@class="right"]');
    $result = [];
    $arr_filter  = [
        PHP_EOL => "",
        "\t" => "",
    ];
    foreach ($nodes as $element) {
        $nodes = $element->childNodes;
        foreach ($nodes as $node) {
            $result[] = explode(" ", strtr($node->nodeValue, $arr_filter))[0];
        }
    }
    return json_encode(["odp" => (int) $result[2], "pdp" => (int) $result[1], "confirm" => (int) $result[0]]);
}


//kalbar
function CoronaKalbar()
{
    $url = "https://dinkes.kalbarprov.go.id/covid-19";
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );

    $get_contents = file_get_contents($url, false, stream_context_create($arrContextOptions));
    $DOM = new \DOMDocument();
    @$DOM->loadHTML($get_contents);
    $xp    = new \DOMXPath($DOM);
    $nodes = $xp->query('//h2[@class="elementor-cta__title elementor-cta__content-item elementor-content-item"]');
    $result = [];
    $arr_filter =  ["\t" => "", "\n" => ""];
    foreach ($nodes as $element) {
        $nodes = $element->childNodes;
        foreach ($nodes as $node) {
            $result[] = explode(" ", strtr($node->nodeValue, $arr_filter))[0];
        }
    }

    return json_encode(["odp" => (int) $result[1], "pdp" => (int) $result[0], "confirm" => "Belum ada Data"]);
}

