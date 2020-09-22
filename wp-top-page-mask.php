<?php
/*
Plugin Name: WP Top Page Mask
Plugin URI: https://github.com/lsii/wp-top-page-mask
Description: Wordpressのトップページへのアクセスを任意のページに遷移させる
Author: lsii
Version: 0.1
Author URI: https://github.com/lsii
*/

class TopPageMask {

    function __construct () {
      add_action('admin_menu', array($this, 'add_pages'));
      add_action('wp_head', array($this, 'hook_javascript'));
    }

    function hook_javascript() {
        ?>
            <script>
                let rurl = '<?php echo $this->get_rurl(); ?>'
                let surls = [
                    '<?php echo site_url('/'); ?>',
                    '<?php echo site_url('/wordpress/'); ?>',
                ]
                let hrefRegexp = (new RegExp("https?:\/\/" + location.host + location.pathname + location.search))
                let isTopUrl = surls.some(pattern => hrefRegexp.test(pattern))
                if (isTopUrl) {
                    location.href = rurl
                }
            </script>
        <?php
    }

    function add_pages () {
      add_menu_page('TopPageMask', 'TopPageMask',  'level_8', __FILE__, array($this,'show_top_page_mask_option_page'), '', 26);
    }

    function get_rurl () {
        $opt = get_option('show_rurl_options');
        return isset($opt['text']) ? $opt['text']: null;
    }

    function show_top_page_mask_option_page () {
        if (isset($_POST['show_rurl_options'])) {
            check_admin_referer('showrurloptions');
            $opt = $_POST['show_rurl_options'];
            update_option('show_rurl_options', $opt);
            ?>
                <div class="updated fade">
                    <p>
                        <strong><?php _e('Options saved.'); ?></strong>
                    </p>
                </div>
            <?php
        }
        ?>
            <div class="wrap">
                <div id="icon-options-general" class="icon32"><br /></div>
                <h2>トップページマスク設定</h2>
                <form action="" method="post">
                    <?php
                        wp_nonce_field('showrurloptions');
                        $opt = get_option('show_rurl_options');
                        $show_text = isset($opt['text']) ? $opt['text']: null;
                    ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">
                                <label for="inputtext">マスクURL</label>
                            </th>
                            <td>
                                <input name="show_rurl_options[text]" type="text" id="inputtext" value="<?php  echo $show_text ?>" class="regular-text" />
                            </td>
                        </tr>
                    </table>
                    <p class="submit"><input type="submit" name="Submit" class="button-primary" value="変更を保存" /></p>
                </form>
            </div>
        <?php
    }

}

$top_page_mask = new TopPageMask;
