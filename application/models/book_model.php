<?php
class Book_model extends Model {

    function Book_model()
    {
        parent::Model();
    }

    function search($keywords, $item_page, $response_group)
    {
                $private_key = '1T+RaZ/v5Zvun/OS6JXyUCOoNldd6Sj7lcRjMo/P';
                $method = "GET";
                $host = "ecs.amazonaws.co.uk";
                $uri = "/onca/xml";

                $timeStamp = gmdate("Y-m-d\TH:i:s\Z");
                $params["AWSAccessKeyId"] = "AKIAI6E3GGOU4F3U6FBA";
                $params["ItemPage"] = $item_page;
                $params["Keywords"] = $keywords;
                $params["ResponseGroup"] = $response_group;
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
               // print_r($request);
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
                        $this->search_localy($keywords, $item_page, $parsed_xml);
                        return $parsed_xml;
                    }
                }

	
    }

    function search_localy($keywords, $item_page, $parsed_xml)
    {
       $this->db->select('b.title,
                         b.publ_date,
                         b.pages,
                         b.type_id,
                         b.isbn,
                         b.cover,
                         a.name as \'author_name\',
                         p.name as \'publisher_name\'');

       $this->db->join('author a', 'a.id = b.author_id');
       $this->db->join('publisher p', 'p.id = b.publisher_id');
       $this->db->where('type_id', "1");
       $this->db->where('isbn', $keywords);
       $param_array = explode("|", $keywords);
       foreach ($param_array as $element)
       {
           $this->db->or_where('isbn', $element);
       }
       $this->db->or_where('title like', "%" . $keywords . "%");
       $this->db->or_where('a.name like', "%" . $keywords . "%");
       $result = $this->db->get('book b');

       if ($result->num_rows() > 0)
       {
    
           $parsed_xml->Items->TotalResults = $parsed_xml->Items->TotalResults + $result->num_rows();
           $xml_str= "<Item>
                        <ItemAttributes>
                        </ItemAttributes>
                        <MediumImage>
                        </MediumImage>
                     </Item>";
           foreach ($result->result() as $row)
           {
               $local_xml = new SimpleXMLElement($xml_str);
               $local_xml->ItemAttributes->addChild('Author', $row->author_name);
               $local_xml->ItemAttributes->addChild('Publisher', $row->publisher_name);
               $local_xml->ItemAttributes->addChild('Title', $row->title);
               $local_xml->ItemAttributes->addChild('PublicationDate', $row->publ_date);
               $local_xml->ItemAttributes->addChild('NumberOfPages', $row->pages);
              // $test->ItemAttributes->addChild('Feature', 'None');
               $local_xml->ItemAttributes->addChild('TYPE', $row->type_id);
               $local_xml->ItemAttributes->addChild('ISBN', $row->isbn);
               $local_xml->MediumImage->addChild('URL', $row->cover);
               $this->AddXMLElement($parsed_xml->Items, $local_xml);
           }
       }
    }

    // Helper function TODO: should be moved to helper classes later on
    function AddXMLElement(SimpleXMLElement $dest, SimpleXMLElement $source)
    {
        $new_dest = $dest->addChild($source->getName(), $source[0]);

        foreach ($source->attributes() as $name => $value)
        {
            $new_dest->addAttribute($name, $value);
        }

        foreach ($source->children() as $child)
        {
            $this->AddXMLElement($new_dest, $child);
        }
    }

    function add_to_bookshelve($isbn, $user_id, $book_type)
    {
        $b_data = array('isbn' => $isbn,
                        'type_id' => $book_type);
        $bs_data;
        $result = FALSE;
        $query = $this->db->where($b_data);
        $query = $this->db->get('book');
        if ($query->num_rows() > 0)
        {
            $bs_data = array('book_id' => $query->row()->id,
                             'user_id' => $user_id);
            $this->db->where($bs_data);
            $inner_q = $this->db->get('book_shelve', $bs_data);
            if ($inner_q->num_rows() > 0)
            {
                return $result;
            }
        }
        else
        {
            $this->db->insert('book', $b_data);
            $book_id = $this->db->insert_id();
            $bs_data = array('book_id' => $book_id,
                             'user_id' => $user_id);
        }

        $result = $this->db->insert('book_shelve', $bs_data);
        return $result;
    }

    function get_shelve_books($user_id)
    {
        $this->db->select('b.id, b.isbn, b.isbn13');
        $this->db->join('book b', 'b.id = bs.book_id');
        $this->db->where('user_id',$user_id);
        $result = $this->db->get('book_shelve bs');
        return $result;
    }

    function check_book_exist($user_id, $isbn)
    {
        $this->db->select('b.id');
        $this->db->join('book b', 'b.id = bs.book_id');
        $this->db->where('user_id', $user_id);
        $this->db->where('b.isbn', $isbn);
        $result = $this->db->get('book_shelve bs');
        return $result->num_rows();
    }

    function remove_from_bookshelve($user_id, $isbn)
    {

        $this->db->select('bs.book_id, bs.id');
        $this->db->join('book b', 'b.id = bs.book_id');
        $this->db->where('user_id', $user_id);
        $this->db->where('b.isbn', $isbn);
        $result = $this->db->get('book_shelve bs');
        if ($result->num_rows() > 0)
        {
            $row = $result->row();

            $b_id = array('id' => $row->book_id);
            $bs_id = array('id' => $row->id);
            $this->db->delete('book', $b_id);
            $this->db->delete('book_shelve', $bs_id);
        } 
    }

    function upload_book($user_id, $book_data)
    {
        // search in amazon
        // search localy
        // if not exist upload new book
        // if successful return TRUE
        // if unsuccessful return FALSE
        $result_from_amazon = $this->search($book_data['isbn'], 1, "Medium");

        if (!empty($result_by_isbn) && $result_by_isdn->Items->TotalResults > 0)
        {
            // Book already exist in amazon database
            return FALSE;
        }

        $this->db->where('isbn', $book_data['isbn']);
        $result_from_local = $this->db->get('book');

        if ($result_from_local->num_rows() > 0)
        {
            // Book already exist in local database
            return FALSE;
        }

        // Uploading book to the local storage
        print_r('uploading author');
        // 1. Checking if author exist, if not creating it and receiving id
        $author_data = array('name' => $book_data['author']);
        $author_result = $this->db->get('author', $author_data);
        $author_id;
        if ($author_result->num_rows() > 0)
        {
            $row = $author_result->row();
            $author_id = $row->id;
        }
        else
        {
             $this->db->insert('author', $author_data);
             $author_id = $this->db->insert_id();
        }

        // 2. Checking if publisher exist, if not creating it and receiving id
        $publisher_data = array('name' => $book_data['publisher']);
        $publisher_result = $this->db->get('publisher', $publisher_data);
        $publisher_id;
        if ($publisher_result->num_rows() > 0)
        {
            $row = $publisher_result->row();
            $publisher_id = $row->id;
        }
        else
        {
            $this->db->insert('publisher', $publisher_data);
            $publisher_id = $this->db->insert_id();
        }
        
        // 3. Creating new book row in database
        $b_insert = array('title' => $book_data['title'],
                            'isbn' => $book_data['isbn'],
                            'publ_date' => $book_data['publ_date'],
                            'pages' => $book_data['pages'],
                            'cover' => $book_data['cover'],
                            'author_id' => $author_id,
                            'publisher_id' => $publisher_id,
                            'type_id' => 1);
        $result = $this->db->insert('book', $b_insert);
        return $result;
    }

}