<?php
class Book_model extends Model {

    function Book_model()
    {
        parent::Model();
    }

    function search_amazon($keywords, $item_page)
    {
                $private_key = '1T+RaZ/v5Zvun/OS6JXyUCOoNldd6Sj7lcRjMo/P';
                $method = "GET";
                $host = "ecs.amazonaws.com";
                $uri = "/onca/xml";

                $timeStamp = gmdate("Y-m-d\TH:i:s.000\Z");
                $timeStamp = str_replace(":", "%3A", $timeStamp);
                print_r($timeStamp);
                $params["AWSAccesskeyId"] = "AKIAI6E3GGOU4F3U6FBA";
                $params["ItemPage"] = $item_page;
                $params["Keywords"] = $keywords;
                $params["ResponseGroup"] = "Medium2%2525COffers";
                $params["SearchIndex"] = "Books";
                $params["Operation"] = "ItemSearch";
                $params["Service"] = "AWSECommerceService";
                $params["Timestamp"] = $timeStamp;
                $params["Version"] = "2009-03-31";

                ksort($params);

                $canonicalized_query = array();
                foreach ($params as $param=>$value)
                {
                  // $param = str_replace("%7E", "~", rawurlencode($param));
                 //  $param = str_replace("%20", "+", rawurlencode($param));
                 //  $value = str_replace("%7E", "~", rawurlencode($value));
                 //  $value = str_replace("%20", "+", rawurlencode($value));
                   $canonicalized_query[] = $param. "=". $value;
                }
                $canonicalized_query = implode("&", $canonicalized_query);

                $string_to_sign = $method."\n\r".$host."\n\r".$uri."\n\r".$canonicalized_query;

                $signature = base64_encode(hash_hmac("sha256",$string_to_sign, $private_key, True));

                //$signature = str_replace("%7E", "~", rawurlencode($signature));

                $request = "http://".$host.$uri."?".$canonicalized_query."&Signature=".$signature;

                $response = @file_get_contents($request);

                if ($response === False)
                {
                    return "response fail\n $string_to_sign";
                }
                else
                {
                    $parsed_xml = simplexml_load_string($response);
                    if ($parsed_xml === False)
                    {
                        return "parse fail";
                    }
                    else
                    {
                        return $parsed_xml;
                    }
                }

	
    }

}