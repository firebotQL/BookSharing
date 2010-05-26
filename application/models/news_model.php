<?php

class News_model extends Model
{
    function News_model()
    {
        parent::Model();
    }

    function save_news($user_id, $header, $content)
    {
        $data = array('header' => $header,
                      'content' => $content,
                      'user_id' => $user_id
        );
        $news_saved = $this->db->insert('news', $data);
        return $news_saved;
    }

    function edit_news($user_id, $header, $content, $news_id)
    {
        $data = array('id' => $news_id,
                      'header' => $header,
                      'content' => $content,
                      'user_id' => $user_id,
        );
        $news_updated = $this->db->update('news', $data);
        return $news_updated;
    }

    function get_news($from, $quantity)
    {
        $this->db->select('n.id,
                           n.header,
                           n.content,
                           n.user_id,
                           n.publish_time,
                           ur.username');
        $this->db->join('user ur', 'ur.id = n.user_id');
        $this->db->orderby('publish_time', 'desc');
        $query = $this->db->get('news n ', $quantity , $from);

        if ($query->num_rows() > 0)
        {
            return $query;
        }
        else
        {
            return NULL;
        }

    }

    function get_news_by_id($news_id)
    {
        $this->db->select('n.id,
                           n.header,
                           n.content,
                           n.user_id,
                           n.publish_time,
                           ur.username');
        $this->db->join('user ur', 'ur.id = n.user_id');
        $this->db->where('n.id', $news_id);
        $this->db->orderby('publish_time');
        $query = $this->db->get('news n');

        if ($query->num_rows() > 0)
        {
            return $query;
        }
        else
        {
            return NULL;
        }

    }

    function get_total_news()
    {
        return $this->db->count_all('news');
    }
}
