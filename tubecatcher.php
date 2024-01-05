<?php


/**
 * Plugin Name: TubeCatcher
 * Version: 0.1.0
 * Description: A WordPress plugin to download videos from the YouTube.
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
use YouTube\Exception\VideoNotFoundException;


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
        private $plugin_name;


        /**
         * a constructor to initialize the files
         */
        public function __construct()
        {

            // initiate the YouTube video info
            $this->youtube_video_info = new GetVideoInfo();

            // initiate the YouTube video downloader
            $this->youtube_downloader = new VideoDownloader();

            // initiate plugin name
            $this->plugin_name = plugin_basename( __FILE__ );

        }


        /**
         * register actions and filters
         * 
         */
        public function register()
        {

            // enqueue scripts
            add_action("wp_enqueue_scripts", [$this, 'enqueue']);

            //shortcode
            add_shortcode('tubecatcher', [$this, 'shortcode']);

            // This is for authenticated users
            add_action('wp_ajax_tubecathcer_ajax_form_action', [$this, 'tubecatcher_ajax_form']);

            // This is for unauthenticated users.
            add_action('wp_ajax_nopriv_tubecathcer_ajax_form_action', [$this, 'tubecatcher_ajax_form']);

            // register menu with hook
            add_action('admin_menu', [$this, 'tubecatcher_register_menu_page']);

        }



        /**
         *
         * Admin settings page to show some configurations
         * 
         */

        public function tubecatcher_settings_page()
        {
            // code...
        }



        /**
         *
         * A register plugin to add menu or settings pages to WP Admin
         * 
         */

        public function tubecatcher_register_menu_page()
        {
            // register plugin settings page
            add_menu_page( 'TubeCatcher Settings', 'TubeCatcher', 'manage_options', 'tubecatcher', [$this, 'tubecatcher_settings_page'], 'dashicons-video-alt3', 30);
        }



        /**
         *
         * enqueue all the scripts and the styles
         * 
         */

        public function enqueue()
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

                    try{

                        $video_id = $this->get_youtube_id($_POST["tubecatcher_video_url"]);

                        // check successfully extract the YouTube video id
                        if($video_id == null){
                            echo json_encode([
                                "error" => true,
                                "error_type" => 'field',
                                "message" => "Please provide a valid YouTube URL."
                            ]);
                            wp_die();
                        }

                        // get the video info 
                        $video_info = $this->youtube_video_info->getInfo($video_id);

                        // get YouTube video downloadable links
                        $download_links = $this->youtube_downloader->fetchDownloadLinks($video_id);
                        

                        // check found any data for the video
                        if(count($video_info) > 0 && count($download_links) > 0){

                            $video_info['download_links'] = $download_links;

                            echo json_encode([
                                "error" => false,
                                "data"  => $video_info
                            ]);

                            wp_die();

                        }else{
                            echo json_encode([
                                "error" => true,
                                "error_type" => 'message',
                                "message" => "Not able to fetch video's details, try another one."
                            ]);
                            wp_die();
                        }

                    }catch(VideoNotFoundException $e){

                        echo json_encode([
                                "error" => true,
                                "error_type" => 'message',
                                "message" => "Sorry couldn't find the video"
                            ]);
                            wp_die();

                    }


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
            ?>
<div class="container tubecatcher-container">
    <div class="card shadow tubecatcher-card">
        <div class="card-body tubecatcher-card-body">
            <div class="card-title tubecatcher-card-title">YouTube video downloader</div>
            <div class="tubecatcher-error"></div>
            <form name="tubecatcher-ajax-form" method="post" action="" class="tubecatcher-ajax-form">
                <?php wp_nonce_field( 'tubecatcher_action_nonce', 'tubecatcher_nonce_field' ); ?>
                <div class="mb-3">
                    <label for="tubecatcher_video_url" class="tubecatcher-form-label">YouTube Link</label>
                    <input type="url" id="tubecatcher_video_url" name="tubecatcher_video_url" class="form-control tubecatcher-input"
                        placeholder="https://www.youtube.com/watch?v=jADTdg-o8i0" />
                    <p class="invalid-feedback tubecatcher_video_url_feedback"></p>
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-danger tubecatcher-card-btn">Get Video</button>
                </div>
            </form>
        </div>
    </div>
    <!-- video info box -->
    <div class="tubecatcher-container-info-box">
    </div>
    <!-- end video info box -->
</div>
<?php
        }


        /**
         * extract YouTube video id from the YouTube vide URL
         *
         * @param $link
         * @return string|null
         */
        public function get_youtube_id($link)
        {
            $pattern = '/(?:youtube\.com\/(?:[^\/\n\s]+\/\s*[^\/\n\s]+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
            preg_match($pattern, $link, $matches);
            return isset($matches[1]) ? $matches[1] : null;
        }


        /**
         * 
         * check the given url is valid YouTube url or not
         * 
         * @param  $url YouTube url
         * @return false|int if valid otherwise false
         * 
         */
        public function validateYouTubeUrl($url)
        {
            $pattern = '/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|v\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
            return preg_match($pattern, $url);
        }


    } // class ends here


    // initialize the class
    $tubecatcher = new TubeCatcher();
    $tubecatcher->register();

}// class check if ends here