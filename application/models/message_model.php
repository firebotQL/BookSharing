<?php


class Message_model extends Model {

    function get_messages($id, $type_id, $per_page, $nr, $full)
    {
        $field = "receiver_id";
        if ($type_id == "2")
            $field = "sender_id";
        if ($type_id == "2" || $type_id == "1")
            $type_id = "0";
        $this->db->select('m.subject,
                             m.date_sent,
                             us.username as \'sendername\',
                             ur.username as \'receivername\',
                             mr.id as \'m_r_id\',
                             m.id as \'m_id\',
                             us.id as \'sender_id\',
                             ur.id as \'receiver_id\'');
        $this->db->join('message m', 'm.id = mr.message_id');
        $this->db->join('user us','us.id = mr.sender_id');
        $this->db->join('user ur','ur.id = mr.receiver_id');
        $this->db->where($field, $id);
        $this->db->where('type_id', $type_id);
        $this->db->orderby('m_id', 'desc');

        if ($full == TRUE)
        {
            $query = $this->db->get('message_relation mr');
        }
        else
        {
            $query = $this->db->get('message_relation mr', $nr ,$per_page);
        }
        return $query;
    }

    function save_message($sender_id, $receiver_name, $content, $subject)
    {
        $this->db->select('id');
        $this->db->where('username', $receiver_name);
        $user = $this->db->get('user', 1);
        if ($user->num_rows() > 0 )
        {
            $receiver_id = $user->row()->id;

            $m_data = array('subject' => $subject,
                            'content' => $content);
            $this->db->insert('message', $m_data);
            $m_query_id = $this->db->insert_id();
            $mr_data = array('message_id' => $m_query_id,
                             'sender_id' => $sender_id,
                             'receiver_id' => $receiver_id);

            return $this->db->insert('message_relation', $mr_data);
        }
        else
        {
            return FALSE;
        }
    }

    function get_message($user_id, $message_id)
    {
        $this->db->select('mr.sender_id as \'sender_id\',
                           m.id as \'message_id\',
                           m.subject,
                           m.content,
                           us.username
                         ');
        $this->db->join('message m', "m.id = mr.message_id");
        $this->db->join('user us', "us.id = mr.sender_id");
        $this->db->where('receiver_id', $user_id);
        $this->db->where('message_id', $message_id);
        $query = $this->db->get('message_relation mr');
        return $query;
    }

}
