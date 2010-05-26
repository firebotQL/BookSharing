<?php

class Forum_model extends Model
{
    function __construct()
    {
        parent::Model();
        
    }

    function register($username, $password, $email)
    {
       $forum_db = $this->load->database('forum', TRUE);
       $data = array('username' => $username,
                 'password' => md5($password),
                 'email' => $email
       );
       $query = $forum_db->insert('users', $data);
       return $query;
    }

    function validate()
    {
        $forum_db = $this->load->database('forum', TRUE);
        $forum_db->where('username', $this->input->post('username'));
        $forum_db->where('password', md5($this->input->post('password')));
        $query =  $forum_db->get('users');
        if ($query->num_rows == 1) {
            //where are we posting to?
            $url = 'http://localhost/forum/NinkoBB//register.php';

            //what post fields?
            $fields = array(
               'username'=> $this->input->post('username'),
               'password'=> $this->input->post('password'),
               'login' => 'login'
            );

            //build the urlencoded data
            $postvars='';
            $sep='';
            foreach($fields as $key=>$value)
            {
               $postvars.= $sep.urlencode($key).'='.urlencode($value);
               $sep='&';
            }


            //open connection
            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_POST,count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
            /* STEP 1. let’s create a cookie file */
            $ckfile = tempnam ("/tmp", "CURLCOOKIE");
            /* STEP 2. visit the homepage to set the cookie properly */
            $ch = curl_init ("http://somedomain.com/");
            curl_setopt ($ch, CURLOPT_COOKIEJAR, $ckfile);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec ($ch);
            print_r($ckfile);

           /* curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 20);

            // Only calling the head
           // curl_setopt($ch, CURLOPT_HEADER, true); // header will be at output
           // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD'); // HTTP request is 'HEAD'

            //execute post
            $result = curl_exec($ch);
            print_r($result . "<br />");
            $this->load->helper('cookie');
            $header = "set-cookie";
            $test = $this->parse_header($result, $header);
            $length = strlen($test) - 8 - 23;
            $test = substr($test, 22, $length);
            print_r($test);
            $cookie = array(
                   'name' => 'login',
                   'value' => "power%7C" . $test,
                   'path' => '/'
            ); 

            set_cookie($cookie);
           // print_r($result);
            //close connection */
            curl_close($ch);

            $this->load->database('default', TRUE);
            return $query;
        }
    }

    function parse_header($content, $header){
        $ret_str = "";
        if( preg_match_all("/($header.*)/i",$content, $match ) )
        {
            $match = $match[1];
            foreach( $match as $str)
            {
                $ret_str .= $str ;
            }
        }
        return $ret_str;
    }
}