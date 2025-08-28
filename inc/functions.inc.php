<?php

function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function show_pages($num_pages, $page){
    $page_shown = [];
    if($num_pages>5) {
        if ($page<5){
            $page_shown = [1,2,3,4,5];
        }
        else {
            $page_shown = [$page-2, $page-1, $page];
            if ($num_pages>=$page+1){
                $page_shown = [$page-3, $page-2, $page-1, $page, $page+1];
                if ($num_pages>=$page+2){
                    $page_shown = [$page-2, $page-1, $page, $page+1, $page+2];
                }
            }
            else {
                $page_shown = [$page-4, $page-3, $page-2, $page-1, $page];
            }
        }
    }
    else {
        $page_shown = [];
        for($i=1; $i<=$num_pages; $i++){
            $page_shown[] = $i;
        }
    }
    return $page_shown;
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function load_xml($xmlFile, $xslFile2){
    // Load XML
    $xml = new DOMDocument();
    $xml->load($xmlFile);

    // Load XSLT
    $xsl = new DOMDocument();
    $xsl->load($xslFile2);

    // Configure the transformer
    $proc = new XSLTProcessor();
    $proc->importStyleSheet($xsl);

    // Transform and output
    return $proc->transformToXML($xml);
}

