<?php

class Comment_model extends Model
{
    function Comment_model()
    {
        parent::Model();
    }

    function send_comment($owner_id_or_isbn, $sender_id, $content, $type_id)
    {
        switch ($type_id)
        {
            case 0:
                $c_data = array('content' => $content);
                $c_saved = $this->db->insert('comment', $c_data);
                if ($c_saved)
                {
                    $c_id = $this->db->insert_id();
                    $cr_data = array('comment_id' => $c_id,
                                'owner_id' => $owner_id_or_isbn,
                                'sender_id' => $sender_id,
                                'type_id' => $type_id);
                    $cr_saved = $this->db->insert('comment_relation', $cr_data);
                    return $cr_saved;
                }
                return $c_saved;
             case 1:
                $c_data = array('content' => $content);
                $c_saved = $this->db->insert('comment', $c_data);
                if ($c_saved)
                {
                    $c_id = $this->db->insert_id();
                    $cr_data = array('comment_id' => $c_id,
                                'isbn' => $owner_id_or_isbn,
                                'sender_id' => $sender_id,
                                'type_id' => $type_id);
                    $cr_saved = $this->db->insert('comment_relation', $cr_data);
                    return $cr_saved;
                }
                return $c_saved;
        }
    }

    function get_total($user_id, $type_id, $isbn = "")
    {
       switch ($type_id)
       {
           case 0:
            $this->db->where('owner_id', $user_id);
            break;
           case 1:
            $this->db->where('isbn', $isbn);
            break;
       }
       
       $query = $this->db->get('comment_relation');
       return $query->num_rows();
    }

    function get_comments($user_id, $from, $quantity, $type_id, $isbn = "")
    {
       switch($type_id)
       {
           case 0:
               $this->db->select('c.content,
                                 c.time_stamp,
                                 cr.id,
                                 us.id as \'sender_id\',
                                 us.username \'sender_name\',
                                 ud.avatar');
               $this->db->join('comment c', 'c.id = cr.comment_id');
               $this->db->join('user us', 'us.id = cr.sender_id');
               $this->db->join('user_data ud', 'ud.user_id = us.id');
               $this->db->where('owner_id', $user_id);
               $this->db->orderby('id', 'desc');
               $query = $this->db->get('comment_relation cr', $quantity , $from);
               if ($query->num_rows() > 0)
               {
                   return $query;
               }
               else
               {
                   return NULL;
               }
               break;
           case 1:
               $this->db->select('c.content,
                                 c.time_stamp,
                                 cr.id,
                                 us.id as \'sender_id\',
                                 us.username \'sender_name\',
                                 ud.avatar');
               $this->db->join('comment c', 'c.id = cr.comment_id');
               $this->db->join('user us', 'us.id = cr.sender_id');
               $this->db->join('user_data ud', 'ud.user_id = us.id');
               $this->db->where('ISBN', $isbn);
               $this->db->orderby('id', 'desc');
               $query = $this->db->get('comment_relation cr', $quantity , $from);
               if ($query->num_rows() > 0)
               {
                   return $query;
               }
               else
               {
                   return NULL;
               }
               break;
       }
    }
}
