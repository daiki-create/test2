<?php

class Youtube_model extends MY_Model {

    private $_db = 'master';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('tables/youtube_tbl');
    }
    
    // ----------------------------------------------------------------------------------------------------------
    
    public function create_yt_post($index=NULL)
    {
        log_debug("Youtube_model.create_yt_post() run.");
        $this->_db = $this->youtube_tbl->initialize($this->_db);

        $youtube_config = config_item('youtube');
        $youtube_request_url = $youtube_config['url'] . '&channel_id=' . $youtube_config['channel_id'] . '&key=' . $youtube_config['key'];
        
        $this->load->library('MY_http_client'); 
        $this->my_http_client->set_url($youtube_request_url);
        $this->my_http_client->request();
        //$this->my_http_client->get_response();
        if ($response = $this->my_http_client->json_decode())
        {
            log_debug($response);

            if ($index !== NULL)
            {
                $youtube_data = [
                    'video_id'     => $response['items'][$index]['id']['videoId'],
                    'title'        => $response['items'][$index]['snippet']['title'],
                    'discription'  => $response['items'][$index]['snippet']['description'],
                    'published_at' => $response['items'][$index]['snippet']['publishedAt'],
                ];

                return $this->youtube_tbl->insert($youtube_data);
            }
            else
            {
                $before_count = $this->youtube_tbl->count_all_results();

                foreach ($response['items'] as $item)
                {
                    $video_id = $item['id']['videoId'];
                    $youtube_data = [
                        'video_id'     => $video_id,
                        'title'        => $item['snippet']['title'],
                        'discription'  => $item['snippet']['description'],
                        'published_at' => $item['snippet']['publishedAt'],
                    ];

                    if ($this->youtube_tbl->insert_update($youtube_data, ['video_id' => $video_id]))
                    {
                        $updated_count++;
                    }
                }

                $after_count = $this->youtube_tbl->count_all_results();

                return $after_count - $before_count;;
            }
        }
    }

    // ----------------------------------------------------------------------------------------------------------

    public function get_yt_posts($limit=NULL)
    {
        log_debug("Youtube_model.get_yt_posts() run.");
        $this->youtube_tbl->initialize($this->_db);
        return $this->youtube_tbl->get_yt_posts($limit);
    }

}
