<?php


class Message_model extends Model {

    function get_messages($id, $type_id, $per_page, $nr)
    {
        $field = "receiver_id";
        if ($type_id == "2")
            $field = "sender_id";
        $this->db->select('*');
        $this->db->join('message', 'message.id = message_relation.message_id');
        $this->db->join('user','message_relation.'. $field . ' = user.id');
        $this->db->where($field, $id);
        $this->db->where('type_id', $type_id);
        $query = $this->db->get('message_relation', $per_page, $nr);
        return $query;
    }

}
