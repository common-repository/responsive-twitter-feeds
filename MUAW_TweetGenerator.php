<?php
class MUAW_TweetGenerator{
    function muawtw_get_raw_tweets($settings, $user, $tw_count, $retweet){

        $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
        $getfield = "?exclude_retweets=$retweet&screen_name=$user&count=$tw_count";
        $requestMethod = 'GET';

        $twitter = new TwitterAPIExchange($settings);

        $json_tweets = $twitter->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest();

        $tw_string = json_decode($json_tweets, $assoc = TRUE);

        $tweets = array();

        $count = 0;
        foreach($tw_string as $tw){
            /** create an array element which belongs to twitter profile information. Always the first tweet is returning profile information. */
            if($count==0){
                $tw_profile = array();
                $tw_profile['user_id'] = $tw['user']['id'];
                $tw_profile['name'] = $tw['user']['name'];
                $tw_profile['screen_name'] = $tw['user']['screen_name'];
                $tw_profile['description'] = $tw['user']['description'];
                $tw_profile['url'] = $tw['user']['url'];
                $tw_profile['location'] = $tw['user']['location'];
                $tw_profile['profile_image'] = $tw['user']['profile_image_url'];
                $tw_profile['profile_background'] = $tw['user']['profile_background_image_url'];
                $tw_profile['followers'] = $tw['user']['followers_count'];
                $tw_profile['created_at'] = $tw['user']['created_at'];
                $tweets[] = $tw_profile;
            }

            /** create tweets with required details and add them to return array */
            $tweet = array();
            $tweet['id'] = $tw['id'];
            $tweet['created_at'] = $tw['created_at'];
            $tweet['text'] = $tw['text'];
            $tweet['source'] = $tw['source'];
            $tweets[] = $tweet;

            $count++;
        }
        return $tweets;
    }

    function muawtw_get_tweets($settings, $user, $tw_count, $retweet, $message){

        $out_tw = '<div class="muaw-twitter-block">';
        $out_tw .= '<div class="muaw-twitter-title-wrap">';
        $out_tw .= '<div class="muaw-twitter-title">Twitter Feeds</div>';
        $out_tw .= '<div class="muaw-twitter-image"><img src="'.plugins_url( '/twbird.png', __FILE__ ).'" /></div>';
        $out_tw .= '</div>';
        $out_tw .= '<div class="muaw-twitter-feed-wrap">';

        if($message != ''){
            $out_tw .= '<div class="muaw-single-tweet">';
            $out_tw .= '<div class="muaw-error-message">'.$message.'</div>';
            $out_tw .= '</div>';
        } else {
            $raw_tweets = $this->muawtw_get_raw_tweets($settings, $user, $tw_count, $retweet);
            $tw_image = $raw_tweets[0]['profile_image'];
            $tw_screen_name = $raw_tweets[0]['screen_name'];
            $tw_profile_name = $raw_tweets[0]['name'];
            $count = 0;


            foreach($raw_tweets as $rtw):
                if($count!=0):
                    $posted_date = strtotime($rtw['created_at']);
                    $posted_date = date('dS M, Y', $posted_date);
                    $tw_post = "https://twitter.com/".$tw_screen_name."/status/".$rtw['id'];
                    $out_tw .= '<div class="muaw-single-tweet">';
                    $out_tw .= '<div class="muaw-twitter-image"><img src="'.$tw_image.'"></div>';
                    $out_tw .= '<div class="muaw-twitter-date"><span>'.$tw_profile_name.'</span> '.$posted_date.'</div>';
                    $out_tw .= '<div class="muaw-twitter-content">'.$rtw['text'].'</div>';
                    $out_tw .= '<div class="muaw-twitter-view"><a href="'.$tw_post.'">View on Twitter</a> </div>';
                    $out_tw .= '</div>';
                endif;
                $count++;
            endforeach;
        }
        $out_tw .= '</div>';
        $out_tw .= '</div>';

        return $out_tw;
    }
}
?>