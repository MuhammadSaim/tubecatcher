<?php


/**
 * Plugin Name: TubeCatcher
 * Version: 0.1.0
 * Description: A wordpress plugin to download videos from the youtube.
 * Author: Muhammad Saim
 * Author URI: https://muhammadsaim.com
 * Requires PHP: 7.1
 * License: GPLv2 or later
 * Requires PHP: 7.1
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: tubecatcher
 */


use TubeCatcher\GetVideoInfo;
use TubeCatcher\VideoDownloader;


defined('ABSPATH') or die("Hey, you can't access this file, you silly human");


// require config files
if (file_exists(dirname(__FILE__) . '/tubecatcher-constants.php')) {
    require_once dirname(__FILE__) . '/tubecatcher-constants.php';
}


// autoload the composer
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}



if (!class_exists('TubeCatcher')) {
    

    class TubeCatcher
    {
        

        private $youtube_video_info;
        private $youtube_downloader;


        /**
         * a constructor to initialize the files
         */
        public function __construct()
        {

            //shortcode
            add_shortcode('tubecatcher', [$this, 'shortcode']);

            // This is for authenticated users
            add_action('wp_ajax_tubecathcer_ajax_form_action', [$this, 'tubecatcher_ajax_form']);

            // This is for unauthenticated users.
            add_action('wp_ajax_nopriv_tubecathcer_ajax_form_action', [$this, 'tubecatcher_ajax_form']);


            // initiate the youtube video info
            $this->youtube_video_info = new GetVideoInfo();

            // initiate the youtube video downloader
            $this->youtube_downloader = new VideoDownloader();


        }


        /**
         *
         *
         * a ajax handler which handle the ajax request and validate the request 
         * and send the response back to the form
         * 
         * 
         */

        public function tubecatcher_ajax_form()
        {

            // check request is comming from the valid source

            if ( 
                ! isset( $_POST['tubecatcher_nonce_field'] ) 
                || ! wp_verify_nonce( $_POST['tubecatcher_nonce_field'], 'tubecatcher_action_nonce') 
            ) {

                echo json_encode([
                    'error' => true,
                    'error_type' => 'message',
                    'message' => "Something went wrong please try again."
                ]);
                wp_die();

            }


            // simple field validation to check field is empty or not

            if(isset($_POST["tubecatcher_video_url"]) && !empty($_POST["tubecatcher_video_url"])){

            
            // find out this is the valid url
            if(filter_var($_POST["tubecatcher_video_url"], FILTER_VALIDATE_URL)){


                // now check url is valid youtube url
                if($this->validateYouTubeUrl($_POST["tubecatcher_video_url"])){

                }else{
                    echo json_encode([
                        "error" => true,
                        "error_type" => 'field',
                        "message" => "Please provide a valid YouTube URL."
                    ]);
                    wp_die();   
                }


            }else{
                echo json_encode([
                    "error" => true,
                    "error_type" => 'field',
                    "message" => "Please provide a valid URL."
                ]);
                wp_die();
            }


            }else{
                echo json_encode([
                    "error" => true,
                    "error_type" => 'field',
                    "message" => "Please provide the YouTube video URL."
                ]);
                wp_die();
            }

        }

        

        /**
         * a method responsible to get values from shortcode and display
         *
         * @return string
         */
        public function shortcode($atts)
        {
            ob_start();
            extract(
                shortcode_atts(
                    [
                        'text' => 'Get',
                    ],
                    $atts,
                    'tubecatcher'
                )
            );
            $this->render_shortcode_html($text);
            return ob_get_clean();
        }




        /**
         * render the form and the other divs
         *
         * @param $text
         * @return void
         */
        public function render_shortcode_html($text)
        {

            // css
            wp_enqueue_style('tubecatcher-bootstrap-style', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', [], null, 'all');
            wp_enqueue_style('tubecatcher-fontawesome-style', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', [], null, 'all');
            wp_enqueue_style('tubecatcher-style', plugins_url('assets/css/style.css', __FILE__), ['tubecatcher-bootstrap-style', 'tubecatcher-fontawesome-style'], null, 'all');
            // js
            wp_enqueue_script('tubecatcher-bootstrap-script', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', [], true);
            wp_enqueue_script('tubecatcher-script', plugins_url('assets/js/script.js', __FILE__), ['jquery', 'tubecatcher-bootstrap-script'], true);
            // wp localization
            wp_localize_script( 'tubecatcher-script', 'tubecatcher_ajax', [
                'admin_ajax_url' => admin_url('admin-ajax.php')
            ]);

            $video_data = (new GetVideoInfo())->getInfo($this->get_youtube_id('https://www.youtube.com/watch?v=jADTdg-o8i0'));
            // var_dump($video_data);
            // wp_die();
            ?>
<div class="container tubecatcher-container">
    <div class="card shadow tubecatcher-card">
        <div class="card-body tubecatcher-card-body">
            <div class="card-title tubecatcher-card-title">YouTube video downloader</div>
            <form name="tubecatcher-ajax-form" method="post" action="" class="tubecatcher-ajax-form">
                <?php wp_nonce_field( 'tubecatcher_action_nonce', 'tubecatcher_nonce_field' ); ?>
                <div class="mb-3">
                    <label for="tubecatcher_video_url" class="tubecatcher-form-label">YouTube Link</label>
                    <input type="url" name="tubecatcher_video_url" class="form-control tubecatcher-input"
                        placeholder="https://www.youtube.com/watch?v=jADTdg-o8i0">
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-danger tubecatcher-card-btn">Get Video</button>
                </div>
            </form>
        </div>
    </div>
    <!-- video info box -->
    <div class="container">
        <div class="card shodow mt-5 tubecatcher-card-info-box">
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <img src="<?= $video_data['thumbnail'] ?>" alt="<?= $video_data['title'] ?> thumbnail" class="img-fluid rounded-start tubecatcher-video-image"/>
                </div>
                <div class="col-md-8 col-sm-12">
                    <div class="card-body">
                        <h5 class="text-break fw-bold"><?= $video_data['title'] ?></h3>
                        <div class="d-flex align-items-center">
                            <i class="fab fa-youtube tubecatcher-fa-2x tubecatcher-fa-youtube me-2"></i>
                            <span class="tubecatcher-channel-name"><?= $video_data['channel_name'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end video info box -->
</div>
<?php
        }




        /**
         * extract youtube video id from the youtube vide URL
         *
         * @param $link
         * @return void
         */
        public function get_youtube_id($link)
        {
            preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $link, $id);
            if (!empty($id)) {
                return $id = $id[0];
            }
            return $link;
        }


        /**
         * 
         * check the given url is valid youtube url or not
         * 
         * @param  $url youtube url
         * @return True if valid otherwise false
         * 
         */
        public function validateYouTubeUrl($url)
        {
            $url_parsed_arr = parse_url($yt_url);
           if ($url_parsed_arr['host'] == "www.youtube.com" && $url_parsed_arr['path'] == "/watch" && substr($url_parsed_arr['query'], 0, 2) == "v=" && substr($url_parsed_arr['query'], 2) != "") {
                return true;
           } else {
               return false;
           }
        }


    } // class ends here


    // initialize the class
    new TubeCatcher();


}// class check if ends here