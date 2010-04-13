<?php
class Book_model extends Model {

    function Book_model()
    {
        parent::Model();
    }

    function search($keywords, $item_page)
    {
                $private_key = '1T+RaZ/v5Zvun/OS6JXyUCOoNldd6Sj7lcRjMo/P';
                $method = "GET";
                $host = "ecs.amazonaws.co.uk";
                $uri = "/onca/xml";

                $timeStamp = gmdate("Y-m-d\TH:i:s\Z");
                $params["AWSAccessKeyId"] = "AKIAI6E3GGOU4F3U6FBA";
                $params["ItemPage"] = $item_page;
                $params["Keywords"] = $keywords;
                $params["ResponseGroup"] = "Medium,Offers";
                $params["SearchIndex"] = "Books";
                $params["Operation"] = "ItemSearch";
                $params["Service"] = "AWSECommerceService";
                $params["Timestamp"] = $timeStamp;
                $params["Version"] = "2009-03-31";

                ksort($params);

                $canonicalized_query = array();
                foreach ($params as $param=>$value)
                {
                   $param = str_replace("%7E", "~", rawurlencode($param));
                   $value = str_replace("%7E", "~", rawurlencode($value));
                   $canonicalized_query[] = $param. "=". $value;
                }
                $canonicalized_query = implode("&", $canonicalized_query);

                $string_to_sign = $method."\n".$host."\n".$uri."\n".$canonicalized_query;

                $signature = base64_encode(hash_hmac("sha256",$string_to_sign, $private_key, True));

                $signature = str_replace("%7E", "~", rawurlencode($signature));

                $request = "http://".$host.$uri."?".$canonicalized_query."&Signature=".$signature;

                $response = @file_get_contents($request);
               
                if ($response === False)
                {
                    return "response fail";
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