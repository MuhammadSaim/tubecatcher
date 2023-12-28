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

defined('ABSPATH') or die("Hey, you can't access this file, you silly human");

// autoload the composer
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}



if (!class_exists('TubeCatcher')) {
    

    class TubeCatcher
    {
        

        /**
         * a constructor to initialize the files
         */
        public function __construct()
        {
            //shortcode
            add_shortcode('tubecatcher', [$this, 'shortcode']);
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
            wp_enqueue_style('tubecatcher-style', plugins_url('assets/css/style.css', __FILE__), [], null, 'all');
            // js
            wp_enqueue_script('tubecatcher-bootstrap-script', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', [], true);
            wp_enqueue_script('tubecatcher-script', plugins_url('assets/js/script.js', __FILE__), ['jquery'], true);
            ?>
<div class="container tubecatcher-container">
    <div class="card shadow tubecatcher-card">
        <div class="card-body tubecatcher-card-body">
            <div class="card-title tubecatcher-card-title">YouTube video downloader</div>
            <form method="post" action="" class="tubecatcher-ajax-form">
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
    <div class="card shodow mt-5 tubecatcher-card-info-box">
        <div class="row">
            <div class="col-md-4 col-sm-12">
                <img src="" alt="">
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


    } // class ends here


    // initialize the class
    new TubeCatcher();


}// class check if ends here