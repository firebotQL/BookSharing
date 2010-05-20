<?php

class Friend_model extends Model
{
    function Friend_model()
    {
        parent::Model();
    }

    function add_friend($friend_id, $user_id)
    {
        $this->db->select('fl.id');
        $this->db->join('friend_list fl', 'fl.connect_id = fc.id');
        $this->db->where('user_id', $user_id);
        $this->db->where('friend_id', $friend_id);
        $result = $this->db->get('friend_connect fc');
        if ($result->num_rows() == 0)
        {
            $fc_data = array('user_id' => $user_id);
            $this->db->insert('friend_connect', $fc_data);
            $fc_id = $this->db->insert_id();
            $fl_data = array('connect_id' => $fc_id,
                             'friend_id' => $friend_id);
            $result = $this->db->insert('friend_list', $fl_data);
            return $result;
        }
        else
        {
            return TRUE;
        }
    }

    function get_friend_list($user_id, $from, $quantity)
    {
        $this->db->select('fl.id,
                           us.username,
                           us.id as \'friend_id\',
                           ud.avatar,
                           ud.first_name

                            ');
        $this->db->join('friend_list fl', 'fl.connect_id = fc.id');
        $this->db->join('user us', 'us.id = fl.friend_id');
        $this->db->join('user_data ud', 'ud.user_id = us.id');
        $this->db->where('fc.user_id', $user_id);
        $this->db->orderby('fl.id', 'desc');
        $result = $this->db->get('friend_connect fc', $quantity , $from);
        if ($result->num_rows() > 0)
        {
            return $result;
        }
        else
        {
            return NULL;
        }
    }


    function get_total_friend_count($user_id)
    {
        $this->db->where('user_id', $user_id);
        $result = $this->db->get('friend_connect');
        return $result->num_rows();
    }

    function search_friends($keyword, $from, $quantity)
    {
        $this->db->select('us.username,
                           us.id as \'friend_id\',
                           ud.avatar,
                           ud.first_name

                            ');
        $this->db->join('user_data ud', 'ud.user_id = us.id');
        $this->db->where('us.username', $keyword);

        $keyword = explode("|", $keyword);
        foreach ($keyword as $element)
        {
           $this->db->or_where('us.username', $element);
           $this->db->or_where('ud.first_name', $element);
           $this->db->or_where('ud.second_name', $element);
           $name_surename = explode(" ", $element);
           foreach ($name_surename as $element2)
           {
                $this->db->or_where('ud.first_name', $element2);
                $this->db->or_where('ud.second_name', $element2);
           }
           
        }
        
        $this->db->orderby('us.username', 'desc');
        $result = $this->db->get('user us', $quantity , $from);
        if ($result->num_rows() > 0)
        {
            return $result;
        }
        else
        {
            return NULL;
        }
    }

    function get_search_friend_count($keyword)
    {
        $this->db->join('user_data ud', 'ud.user_id = us.id');
        $this->db->where('us.username', $keyword);
        $keyword = explode("|", $keyword);
        foreach ($keyword as $element)
        {
           $this->db->or_where('us.username', $element);
           $this->db->or_where('ud.first_name', $element);
           $this->db->or_where('ud.second_name', $element);
           $name_surename = explode(" ", $element);
           foreach ($name_surename as $element2)
           {
                $this->db->or_where('ud.first_name', $element2);
                $this->db->or_where('ud.second_name', $element2);
           }

        }
        $result = $this->db->get('user us');
        return $result->num_rows();
    }

    function friend_exist($user_id, $friend_id)
    {
        $this->db->select('fl.id');
        $this->db->join('friend_list fl', 'fl.connect_id = fc.id');
        $this->db->where('user_id', $user_id);
        $this->db->where('friend_id', $friend_id);
        $result = $this->db->get('friend_connect fc');
        if ($result->num_rows() > 0)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
}