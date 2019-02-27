<?php

require_once AD_INSERTER_PLUGIN_DIR.'constants.php';

function generate_settings_form (){

  if (defined ('AI_ADSENSE_API')) {
    require_once AD_INSERTER_PLUGIN_DIR.'includes/adsense-api.php';
  }

  global $ai_db_options, $block_object, $ai_wp_data, $ai_db_options_extract;
  global $rating_value, $rating_string, $rating_css, $ai_custom_hooks;

  wp_enqueue_media();

  $save_url = $_SERVER ['REQUEST_URI'];
  if (isset ($_GET ['tab'])) {
    $save_url = preg_replace ("/&tab=\d+/", "", $save_url);
  }

  $generate_all = false;
  if (isset ($_GET ['generate-all']) && $_GET ['generate-all'] == 1) {
    $generate_all = true;
  }

  $subpage = 'main';
  $start =  1;
  $end   = 16;
  if (function_exists ('ai_settings_parameters')) ai_settings_parameters ($subpage, $start, $end);

  if (isset ($_POST ['ai-active-tab'])) {
    $active_tabs = json_decode ($_POST ['ai-active-tab']);
    if ($active_tabs == null) $active_tabs = array ($start, 0);
  }

  if (isset ($_GET ['settings'])) {
    $active_tab = $_GET ['settings'];
    if (isset ($_GET ['single'])) {
      $start = $active_tab;
      $end   = $active_tab;
    } else {
        $start = intval (($active_tab - 1) / 16) * 16 + 1;
        $end   = $start + 15;
      }
  }
  elseif (isset ($_GET ['tab'])) $active_tab = $_GET ['tab']; else
    $active_tab = isset ($active_tabs [0]) ? $active_tabs [0] : $start;
  if (!is_numeric ($active_tab)) $active_tab = 1;
  if ($active_tab != 0)
    if ($active_tab < $start || $active_tab > $end) $active_tab = $start;

  $active_tab_0 = isset ($active_tabs [1]) ? $active_tabs [1] : 0;

  $adH  = $block_object [AI_HEADER_OPTION_NAME];
  $adF  = $block_object [AI_FOOTER_OPTION_NAME];

  if (defined ('AI_ADBLOCKING_DETECTION') && AI_ADBLOCKING_DETECTION) {
    $adA  = $block_object [AI_ADB_MESSAGE_OPTION_NAME];
  }

  $syntax_highlighter_theme = get_syntax_highlighter_theme ();
  $block_class_name         = get_block_class_name ();
  $block_class              = get_block_class ();
  $block_number_class       = get_block_number_class ();
  $inline_styles            = get_inline_styles ();

  $default = $block_object [0];

  $exceptions = false;
  $block_exceptions = array ();
  if (ai_current_user_role_ok () && (!is_multisite() || is_main_site () || multisite_exceptions_enabled ())) {
    $args = array (
      'public'    => true,
      '_builtin'  => false
    );
    $custom_post_types = get_post_types ($args, 'names', 'and');
    $screens = array_values (array_merge (array ('post', 'page'), $custom_post_types));

    $args = array (
      'posts_per_page'   => 100,
      'offset'           => 0,
      'category'         => '',
      'category_name'    => '',
      'orderby'          => 'type',
      'order'            => 'ASC',
      'include'          => '',
      'exclude'          => '',
      'meta_key'         => '_adinserter_block_exceptions',
      'meta_value'       => '',
      'post_type'        => $screens,
      'post_mime_type'   => '',
      'post_parent'      => '',
      'author'           => '',
      'author_name'      => '',
      'post_status'      => '',
      'suppress_filters' => true
    );
    $posts_pages = get_posts ($args);

    $exceptions = array ();
    foreach ($posts_pages as $page) {
      $post_meta = get_post_meta ($page->ID, '_adinserter_block_exceptions', true);
      if ($post_meta == '') continue;
      $post_type_object = get_post_type_object ($page->post_type);
      $exceptions [$page->ID] = array ('type' => $page->post_type, 'name' => $post_type_object->labels->singular_name, 'title' => $page->post_title, 'blocks' => $post_meta);

      $selected_blocks = explode (",", $post_meta);
      foreach ($selected_blocks as $selected_block) {
        $block_exceptions [$selected_block][$page->ID] = array ('type' => $page->post_type, 'name' => $post_type_object->labels->singular_name, 'title' => $page->post_title);
      }
    }
  }

  if ($rating_string = get_transient (AI_TRANSIENT_RATING)) {
    if ($rating_string < 1 && $rating_string > 5) $rating_string = '';
  }
  $rating_css = $rating_string == '' ? 'width: 100%;' : 'width: '.number_format ($rating_string * 20, 4).'%;';
  $rating_value = $rating_string == '' ? '' : number_format ($rating_string, 1);

  if (isset ($ai_db_options_extract [AI_EXTRACT_USED_BLOCKS])) $used_blocks = unserialize ($ai_db_options_extract [AI_EXTRACT_USED_BLOCKS]); else $used_blocks = array ();

  if (!isset ($_GET ['settings'])): // start of code only for normal settings

  if (function_exists ('ai_admin_settings_notices')) ai_admin_settings_notices ();
?>

<div id="ai-data" style="display: none;" version="<?php echo AD_INSERTER_VERSION; ?>" theme="<?php echo $syntax_highlighter_theme; ?>" js_debugging="<?php echo $ai_wp_data [AI_BACKEND_JS_DEBUGGING] ? '1' : '0'; ?>" ></div>
<?php
  if (function_exists ('ai_data_2')) ai_data_2 ();
?>

<div id="ai-clipboard" style="display: none;"></div>

<div style="clear: both;"></div>

<div id="ai-settings" style="float: left;">

<form id="ai-form" class="no-select rounded" style="float: left;" action="<?php echo $save_url; ?>" method="post" name="ai_form" start="<?php echo $start; ?>" end="<?php echo $end; ?>" nonce="<?php echo wp_create_nonce ("adinserter_data"); ?>">

  <div id="header" class="ai-form header rounded">
<?php
  if (function_exists ('ai_settings_header')) ai_settings_header ($start, $active_tab); else { ?>

    <div style="float: left;">
      <h2 id="plugin_name" style="display: inline-block; margin: 5px 0;"><?php echo AD_INSERTER_NAME . ' ' . AD_INSERTER_VERSION ?></h2>
    </div>
    <div id="header-buttons">
      <img id="ai-loading" src="<?php echo AD_INSERTER_PLUGIN_IMAGES_URL; ?>loading.gif" style="width: 24px; height: 24px; vertical-align: middle; margin-right: 20px; display: none;" />
      <button type="button" class="ai-top-button" style="display: none; margin: 0 10px 0 0; width: 62px; outline-color: transparent;" onclick="window.open('http://adinserter.pro/documentation')" title="<?php echo AD_INSERTER_NAME; ?> Documentation">Doc</button>
      <button type="button" class="ai-top-button" style="display: none; margin: 0 10px 0 0; width: 62px; outline-color: transparent;" onclick="window.open('https://wordpress.org/support/plugin/ad-inserter')" title="<?php echo AD_INSERTER_NAME; ?> support forum">Support</button>
      <button type="button" class="ai-top-button" style="display: none; margin: 0 10px 0 0; width: 62px; outline-color: transparent;" onclick="window.open('https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LHGZEMRTR7WB4')" title="Support Free Ad Inserter development. If you are making money with Ad Inserter consider donating some small amount. Even 1 dollar counts. Thank you!">Donate</button>
      <button type="button" class="ai-top-button" style="display: none; margin: 0 10px 0 0; width: 62px; outline-color: transparent;" onclick="window.open('https://wordpress.org/support/plugin/ad-inserter/reviews/')" title="If you like Ad Inserter and have a moment, please help me spread the word by reviewing the plugin on WordPres">Review</button>
      <button type="button" class="ai-top-button" style="display: none; margin: 0 10px 0 0; width: 62px; outline-color: transparent;" onclick="window.open('http://adinserter.pro/')" title="Need more code blocks, GEO targeting, impression and click tracking? Upgrade to Ad Inserter Pro">Go&nbsp;Pro</button>
      <button id="ai-list" type="button" class="ai-top-button" style="width: 62px; display: none; margin-right: 0px; outline-color: transparent;" title="Show list of all code blocks"><span>Blocks</span></button>
    </div>

    <div style="clear: both;"></div>
<?php
  }
?>
  </div>

  <div id="javascript-warning" class="ai-form rounded" style="display: none;">
    <h2 id="javascript-version" style="float: left; color: red;" title="Loaded plugin javascript version">&nbsp;</h2>
    <div style="float: right; text-align: right; margin: 8px 5px 0px 0;">
        <span id="javascript-version-parameter" style="display: none;">Wrong or <a href="https://adinserter.pro/documentation#missing-version-parameter" class="simple-link" target="_blank">missing version parameter</a> for the javscript file, probably due to inappropriate caching.<br /></span>
        <span id="javascript-version-parameter-missing" style="display: none;">Missing version parameter for the javscript file, probably due to inappropriate caching.<br /></span>
        Incompatible (old) javscript file loaded, probably due to inappropriate caching.<br />
        Please delete browser's cache and all other caches used and then reload this page.
    </div>
    <div style="clear: both;"></div>
  </div>

  <div id="css-warning" class="ai-form rounded" style="display: none;">
    <h2 id="css-version" style="float: left; color: red;" title="Loaded plugin CSS version">&nbsp;</h2>
    <div style="float: right; text-align: right; margin: 8px 5px 0px 0;">
        <span id="css-version-parameter" style="display: none;">Wrong version parameter for the CSS file, probably due to inappropriate caching.<br /></span>
        <span id="css-version-parameter-missing" style="display: none;">Missing version parameter for the CSS file, probably due to inappropriate caching.<br /></span>
        Incompatible (old) CSS file loaded, probably due to inappropriate caching.<br />
        Please delete browser's cache and all other caches used and then reload this page.
    </div>
    <div style="clear: both;"></div>
  </div>

  <div id="blocked-warning" class="ai-form warning-enabled rounded">
    <h2 class="blocked-warning-text" style="float: left; color: red; margin: 7px 0;" title="Error loading page">WARNING</h2>
    <div style="float: right; text-align: right; width: 630px; margin: 8px 5px 0px 0;">
       Page may <a href="https://adinserter.pro/documentation#page-blocked" class="simple-link" target="_blank">not be loaded properly</a>.
       Check ad blocking software that may block CSS, Javascript or image files.
    </div>
    <div style="clear: both;"></div>
  </div>

  <div id="rotation-tabs" style="display: none;">
    <ul>
      <li class="ai-rotate-option"><a></a></li>
    </ul>

    <div class="responsive-table rounded">
      <table class="ai-settings-table" style="">
        <tr>
          <td style="padding-right: 7px;">
            Option Name
          </td>
          <td style="width: 100%;">
            <input class="option-name" style="width: 100%;" type="text" size="50" maxlength="200" />
          </td>
          <td style="padding-left: 7px;">
            Share
          </td>
          <td>
            <input class="option-share" style="width: 42px;" type="text" maxlength="2" title="Option share in percents - 0 means option disabled, if share for one option is not defined it will be calculated automatically. Leave all share fields empty for equal option shares." /> %
          </td>
        </tr>
      </table>
      <div style="clear: both;"></div>
    </div>

  </div>

<div id="ai-error-container" class="rounded" style="border-color: red; display: none;"></div>

<div id="ai-container">

<?php endif; // of code only for normal settings  ?>

  <div id="ai-tab-container" class="ai-form rounded" style="padding-bottom: 1px;">

  <div id="dummy-tabs" style="height: 30px; padding: .2em .2em 0;"></div>

  <div id="ai-scroll-tabs" class="scroll_tabs_theme_light" style="display: none;">
<?php

  for ($block = $start; $block <= $end; $block ++){
    echo "    <span id='ai-scroll-tab-$block' rel='$block'>$block</span>";
  }
?>
    <span rel='0'>0</span>
  </div>

  <ul id="ai-tabs" style="display: none;">
<?php

  $sidebars_with_widget = get_sidebar_widgets ();

//  $sidebar_widgets = wp_get_sidebars_widgets();
//  $widget_options = get_option ('widget_ai_widget');

//  $sidebars_with_widgets = array ();
////  for ($block = $start; $block <= $end; $block ++){
//  for ($block = 1; $block <= AD_INSERTER_BLOCKS; $block ++){
//    $sidebars_with_widget [$block]= array ();
//  }
//  foreach ($sidebar_widgets as $sidebar_index => $sidebar_widget) {
//    if (is_array ($sidebar_widget) && isset ($GLOBALS ['wp_registered_sidebars'][$sidebar_index]['name'])) {
//      $sidebar_name = $GLOBALS ['wp_registered_sidebars'][$sidebar_index]['name'];
//      if ($sidebar_name != "") {
//        foreach ($sidebar_widget as $widget) {
//          if (preg_match ("/ai_widget-([\d]+)/", $widget, $widget_id)) {
//            if (isset ($widget_id [1]) && is_numeric ($widget_id [1])) {
//              $widget_option = $widget_options [$widget_id [1]];
//              $widget_block = $widget_option ['block'];
////              if ($widget_block >= $start && $widget_block <= $end && !in_array ($sidebar_name, $sidebars_with_widget [$widget_block])) {
//              if ($widget_block >= 1 && $widget_block <= AD_INSERTER_BLOCKS && !in_array ($sidebar_name, $sidebars_with_widget [$widget_block])) {
//                $sidebars_with_widget [$widget_block] []= $sidebar_name;
//              }
//            }
//          }
//        }
//      }
//    }
//  }

  $manual_widget        = array ();
  $manual_shortcode     = array ();
  $manual_php_function  = array ();
  $manual               = array ();
  $sidebars             = array ();

  for ($block = $start; $block <= $end; $block ++) {
    $obj = $block_object [$block];

    $automatic = $obj->get_automatic_insertion() != AI_AUTOMATIC_INSERTION_DISABLED;

    $manual_widget        [$block] = $obj->get_enable_widget()    == AI_ENABLED;
    $manual_shortcode     [$block] = $obj->get_enable_manual()    == AI_ENABLED;
    $manual_php_function  [$block] = $obj->get_enable_php_call()  == AI_ENABLED;
    $manual               [$block] = ($manual_widget [$block] && !empty ($sidebars_with_widget [$block])) || $manual_shortcode [$block] || $manual_php_function [$block];

    $style = "";
    $ad_name = "";
    $sidebars [$block] = "";
    if ($automatic && $manual [$block]) $style = "font-weight: bold; color: #c4f;";
      elseif ($automatic) $style = "font-weight: bold; color: #e44;";
        elseif ($manual [$block]) $style = "font-weight: bold; color: #66f;";

    if (!empty ($sidebars_with_widget [$block])) $sidebars [$block] = implode (", ", $sidebars_with_widget [$block]);

//    if (!wp_is_mobile ()) {
//      $ad_name = $obj->get_ad_name();

//      $ad_name_functions = false;
//      if ($automatic) {
//        $ad_name .= ": ".$obj->get_automatic_insertion_text ();
//        $ad_name_functions = true;
//      }

//      //if (!empty ($sidebars_with_widget [$block])) $sidebars [$block] = implode (", ", $sidebars_with_widget [$block]);
//      if ($manual_widget [$block]) {
//        if ($sidebars [$block] != "") {
//          $ad_name .= $ad_name_functions ? ", " : ": ";
//          $ad_name .= "Widget used in: [".$sidebars [$block]."]";
//          $ad_name_functions = true;
//        }
//      } else {
//          if (!empty ($sidebars_with_widget [$block])) {
//            $ad_name .= $ad_name_functions ? ", " : ": ";
//            $ad_name .= "Widget DISABLED but used in: [".$sidebars [$block]."]";
//            $ad_name_functions = true;
//          }
//        }

//      if ($manual_shortcode     [$block]) {
//        $ad_name .= $ad_name_functions ? ", " : ": ";
//        $ad_name .= "Shortcode";
//        $ad_name_functions = true;
//      }
//      if ($manual_php_function  [$block]) {
//        $ad_name .= $ad_name_functions ? ", " : ": ";
//        $ad_name .= "PHP function";
//        $ad_name_functions = true;
//      }
//    }

    echo "
      <li id=\"ai-tab$block\" class=\"ai-tab\" title=\"$ad_name\"><a href=\"#tab-$block\"><span style=\"", $style, "\">$block</span></a></li>";

  }

  $enabled_k = count ($ai_custom_hooks) != 0;
  $enabled_h = $adH->get_enable_manual () && $adH->get_ad_data() != "";
  $enabled_f = $adF->get_enable_manual () && $adF->get_ad_data() != "";
  if ($enabled_h || $enabled_f) $class_hfa = " on"; else $class_hfa = "";

  $title_hfa = "";
  if ($enabled_h) $title_hfa .= ", Header code";
  if ($enabled_f) $title_hfa .= ", Footer code";
  $header_code_disabled = !$adH->get_enable_manual () && $adH->get_ad_data() != "";
  $footer_code_disabled = !$adF->get_enable_manual () && $adF->get_ad_data() != "";

  if (defined ('AI_ADBLOCKING_DETECTION') && AI_ADBLOCKING_DETECTION) {
    $enabled_a = $ai_wp_data [AI_ADB_DETECTION];
    if ($enabled_a) $title_hfa .= ", Ad blocking detection code";
    if ($enabled_a) $class_hfa = " on";
  }

?>
      <li id="ai-tab0" class="ai-tab" title="<?php echo AD_INSERTER_NAME ?> General Settings<?php echo $title_hfa ?>" style=" margin: 1px 0 0 0;"><a href="#tab-0" style="padding: 5px 14px 6px 12px;"><div class="ai-icon-gear<?php echo $class_hfa ?>"></div></a></li>
  </ul>

<?php

  for ($block = $start; $block <= $end + 1; $block ++){

    if ($block <= $end) {
      $default->number = $block;
      $default->wp_options [AI_OPTION_BLOCK_NAME] = AD_NAME." ".$block;

      $tab_visible = $block == $active_tab || $generate_all;

      $obj = $block_object [$block];
    } else {
        $block = 999;

        $sidebars [$block] = "";

        $manual_widget        [$block] = $obj->get_enable_widget()    == AI_ENABLED;
        $manual_shortcode     [$block] = $obj->get_enable_manual()    == AI_ENABLED;
        $manual_php_function  [$block] = $obj->get_enable_php_call()  == AI_ENABLED;
        $manual               [$block] = ($manual_widget [$block] && !empty ($sidebars_with_widget [$block])) || $manual_shortcode [$block] || $manual_php_function [$block];

        $default->number = 0;
        $default->wp_options [AI_OPTION_BLOCK_NAME] = AD_NAME." 0";

        $tab_visible = false;

        $obj = $default;
      }

    $client_side_devices = $obj->get_detection_client_side () == AI_ENABLED;
    $server_side_devices = $obj->get_detection_server_side () == AI_ENABLED;
    if ($client_side_devices) $client_side_style = "font-weight: bold; color: #66f;"; else $client_side_style = "";
    if ($server_side_devices) $server_side_style = "font-weight: bold; color: #66f;"; else $server_side_style = "";

    $show_devices = $client_side_devices || $server_side_devices == AI_ENABLED;
    if ($show_devices) $devices_style = "font-weight: bold; color: #66f;"; else $devices_style = "";

    $cat_list       = $obj->get_ad_block_cat();
    $tag_list       = $obj->get_ad_block_tag();
    $taxonomy_list  = $obj->get_ad_block_taxonomy();
    $id_list        = $obj->get_id_list();
    $url_list       = $obj->get_ad_url_list();
    $url_parameter_list = $obj->get_url_parameter_list();
    $domain_list = $obj->get_ad_domain_list();
    if (function_exists ('ai_lists')) $lists = ai_lists ($obj); else $lists = false;
    $show_lists = $cat_list != '' || $tag_list != '' || $taxonomy_list != '' || $id_list != '' || $url_list != '' || $url_parameter_list != '' || $domain_list != '' || $lists;
    if ($show_lists) $lists_style = "font-weight: bold; color: #66f;"; else $lists_style = "";

    $show_manual = $manual [$block];
    if ($show_manual) $manual_style = "font-weight: bold; color: " . ($manual_widget [$block] || empty ($sidebars_with_widget [$block]) ? "#66f;" : "#e44;"); else $manual_style = "";

    $insertion_options =
      $obj->get_maximum_insertions () ||
      $obj->get_display_for_users() != AD_DISPLAY_ALL_USERS ||
      $obj->get_enable_amp () == AI_ENABLED ||
      $obj->get_enable_ajax () != AI_ENABLED ||
      $obj->get_enable_404 () == AI_ENABLED ||
      $obj->get_enable_feed () == AI_ENABLED ||
      $obj->get_disable_caching ();

    $word_count_options =
      intval ($obj->get_minimum_words()) != 0 ||
      intval ($obj->get_maximum_words()) != 0;

    $scheduling_active = $obj->get_scheduling() != AI_SCHEDULING_OFF;

    $filter_active = $obj->get_call_filter() || $obj->get_inverted_filter() != 0;

    $adb_block_action_active = $obj->get_adb_block_action () != AI_ADB_BLOCK_ACTION_DO_NOTHING;

    $display_options = $obj->get_show_label () || $obj->get_close_button ();

    $show_misc =
      $insertion_options ||
      $word_count_options ||
      $scheduling_active ||
      $filter_active ||
      $adb_block_action_active ||
      $display_options;

    if ($show_misc) $misc_style = "font-weight: bold; color: #66f;"; else $misc_style = "";

    if ($insertion_options)       $insertion_style  = "font-weight: bold; color: #66f;"; else $insertion_style = "";
    if ($word_count_options)      $word_count_style = "font-weight: bold; color: #66f;"; else $word_count_style = "";
    if ($scheduling_active)       $scheduling_style = "font-weight: bold; color: #66f;"; else $scheduling_style = "";
    if ($filter_active)           $filter_style     = "font-weight: bold; color: #66f;"; else $filter_style = "";
    if ($adb_block_action_active) $adb_style        = "font-weight: bold; color: #66f;"; else $adb_style = "";
    if ($display_options)         $display_style    = "font-weight: bold; color: #66f;"; else $display_style = "";
    $general_style = '';

    $automatic_insertion = $obj->get_automatic_insertion();

    $paragraph_settings =
      $automatic_insertion == AI_AUTOMATIC_INSERTION_BEFORE_PARAGRAPH ||
      $automatic_insertion == AI_AUTOMATIC_INSERTION_AFTER_PARAGRAPH;

    $paragraph_counting =
      $obj->get_direction_type()            != $default->get_direction_type() ||
      $obj->get_paragraph_tags()            != $default->get_paragraph_tags() ||
      $obj->get_minimum_paragraph_words()   != $default->get_minimum_paragraph_words() ||
      $obj->get_maximum_paragraph_words()   != $default->get_maximum_paragraph_words() ||
      $obj->get_paragraph_text_type()       != $default->get_paragraph_text_type() ||
      $obj->get_paragraph_text()            != $default->get_paragraph_text() ||
      $obj->get_paragraph_number_minimum()  != $default->get_paragraph_number_minimum() ||
      $obj->get_minimum_words_above()       != $default->get_minimum_words_above() ||
      $obj->get_count_inside_blockquote()   != $default->get_count_inside_blockquote();

    $paragraph_clearance =
      ($obj->get_avoid_text_above() != $default->get_avoid_text_above() && intval ($obj->get_avoid_paragraphs_above()) != 0) ||
      ($obj->get_avoid_text_below() != $default->get_avoid_text_below() && intval ($obj->get_avoid_paragraphs_below()) != 0);

    $html_settings =
      $automatic_insertion == AI_AUTOMATIC_INSERTION_BEFORE_HTML_ELEMENT ||
      $automatic_insertion == AI_AUTOMATIC_INSERTION_AFTER_HTML_ELEMENT;

    $html_element_insertion = $obj->get_html_element_insertion ();
    $server_side_insertion = $obj->get_server_side_insertion ();

    $filter_type = $obj->get_filter_type();

?>
<div id="tab-<?php echo $block; ?>" style="padding: 0;<?php echo $tab_visible ? "" : " display: none;" ?>">
  <div class="ai-toolbars">

    <div id="ai-main-toolbar-<?php echo $block; ?>" class="max-input" style="margin: 8px 0 0 2px; height: 28px; width: 731px;">
      <span id="name-label-container-<?php echo $block; ?>" style="display: table-cell; width: 100%; padding: 0; font-weight: bold; cursor: pointer;">
        <input id="name-edit-<?php echo $block; ?>" style="width: 100%; vertical-align: middle; font-size: 14px; display: none;" type="text" name="<?php echo AI_OPTION_BLOCK_NAME, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_ad_name(); ?>" value="<?php echo $obj->get_ad_name() ?>" size="56" maxlength="120" />
        <span id="name-label-<?php echo $block; ?>" class="no-select" style="width: 100%; max-width: 490px; vertical-align: middle; font-size: 14px; display: inline-block; margin-top: 4px; margin-left: 7px; white-space: nowrap; overflow: hidden;"><?php echo $obj->get_ad_name() ?></span>
      </span>
<?php if (AI_SYNTAX_HIGHLIGHTING) : ?>
      <span class="ai-toolbar-button ai-settings">
        <input type="checkbox" value="0" id="simple-editor-<?php echo $block; ?>" class="simple-editor-button" style="display: none;" />
        <label class="checkbox-button" for="simple-editor-<?php echo $block; ?>" title="Toggle Syntax Highlighting / Simple editor for mobile devices"><span class="checkbox-icon icon-tablet"></span></label>
      </span>
<?php endif; ?>

<?php if (defined ('AI_CODE_GENERATOR')) : ?>
      <span class="ai-toolbar-button ai-settings">
        <input type="checkbox" id="tools-button-<?php echo $block; ?>" style="display: none;" />
        <label class="checkbox-button tools-button" for="tools-button-<?php echo $block; ?>" title="Toggle tools"><span class="checkbox-icon icon-tools"></span></label>
      </span>
<?php endif; ?>

<?php if (!is_multisite() || is_main_site () || multisite_php_processing ()) : ?>
      <span class="ai-toolbar-button ai-settings">
        <input type="hidden"   name="<?php echo AI_OPTION_PROCESS_PHP, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
        <input type="checkbox" name="<?php echo AI_OPTION_PROCESS_PHP, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_process_php (); ?>" id="process-php-<?php echo $block; ?>" <?php if ($obj->get_process_php () == AI_ENABLED) echo 'checked '; ?> style="display: none;" />
        <label class="checkbox-button" for="process-php-<?php echo $block; ?>" title="Process PHP code in block"><span class="checkbox-icon icon-php<?php if ($obj->get_process_php () == AI_ENABLED) echo ' on'; ?>"></span></label>
      </span>
<?php endif; ?>
<?php if (function_exists ('ai_settings_top_buttons_2')) ai_settings_top_buttons_2 ($block, $obj, $default); ?>
    </div>

    <div class="ai-settings">
      <div id="ai-tools-toolbar-<?php echo $block; ?>" class="ai-tools-toolbar max-input" style="margin: 8px 0 0 2px; height: 28px;  width: 729px; padding: 0 0 0 2px; display: none;">
<?php if (function_exists ('ai_settings_top_buttons_1')) ai_settings_top_buttons_1 ($block, $obj, $default); ?>
<?php if (defined ('AI_CODE_GENERATOR')) : ?>
        <span class="ai-toolbar-button ai-button-left">
          <input type="checkbox" id="code-generator-<?php echo $block; ?>" style="display: none;" />
          <label class="checkbox-button code-generator-button" for="code-generator-<?php echo $block; ?>" title="Toggle code generator"><span class="checkbox-icon icon-code"></span></label>
        </span>
        <span class="ai-toolbar-button ai-button-left">
          <input type="checkbox" id="rotation-<?php echo $block; ?>" style="display: none;" />
          <label class="checkbox-button rotation-button" for="rotation-<?php echo $block; ?>" title="Toggle rotation editor"><span class="checkbox-icon icon-rotation"></span></label>
        </span>
        <span class="ai-toolbar-button ai-button-left">
          <input type="checkbox" id="visual-editor-<?php echo $block; ?>" style="display: none;" />
          <label class="checkbox-button" for="visual-editor-<?php echo $block; ?>" title="Open visual HTML editor"><span class="checkbox-icon icon-edit"></span></label>
        </span>


<?php if (defined ('AI_ADSENSE_API')) : ?>
<?php if (defined ('AI_ADSENSE_AUTHORIZATION_CODE')) : ?>
        <span style="display: table-cell; width: 6%;"></span>

        <span class="ai-toolbar-button ai-button-left">
          <input type="checkbox" id="ga-<?php echo $block; ?>" style="display: none;" />
          <label class="checkbox-button adsense-list" for="ga-<?php echo $block; ?>" title="Show AdSense ad units" ><span class="checkbox-icon icon-adsense ai-bw"></span></label>
        </span>
<?php endif; ?>
<?php endif; ?>

        <span style="display: table-cell; width: 100%;"></span>

        <span class="ai-toolbar-button" style="padding-right: 15px;">
          <input type="checkbox" id="clear-block-<?php echo $block; ?>" style="display: none;" />
          <label class="checkbox-button" for="clear-block-<?php echo $block; ?>" title="Clear block"><span class="checkbox-icon icon-clear"></span></label>
        </span>

        <span class="ai-toolbar-button">
          <input type="checkbox" id="copy-block-<?php echo $block; ?>" class="ai-copy" style="display: none;" />
          <label class="checkbox-button" for="copy-block-<?php echo $block; ?>" title="Copy block"><span class="checkbox-icon icon-copy"></span></label>
        </span>
        <span class="ai-toolbar-button">
          <input type="checkbox" id="paste-name-<?php echo $block; ?>" style="display: none;" />
          <label class="checkbox-button" for="paste-name-<?php echo $block; ?>" title="Paste name"><span class="checkbox-icon icon-paste-name"></span></label>
        </span>
        <span class="ai-toolbar-button">
          <input type="checkbox" id="paste-code-<?php echo $block; ?>" style="display: none;" />
          <label class="checkbox-button" for="paste-code-<?php echo $block; ?>" title="Paste code"><span class="checkbox-icon icon-paste-code"></span></label>
        </span>
        <span class="ai-toolbar-button">
          <input type="checkbox" id="paste-settings-<?php echo $block; ?>" style="display: none;" />
          <label class="checkbox-button" for="paste-settings-<?php echo $block; ?>" title="Paste settings"><span class="checkbox-icon icon-paste-settings"></span></label>
        </span>
        <span class="ai-toolbar-button">
          <input type="checkbox" id="paste-block-<?php echo $block; ?>" style="display: none;" />
          <label class="checkbox-button" for="paste-block-<?php echo $block; ?>" title="Paste block (name, code and settings)"><span class="checkbox-icon icon-paste"></span></label>
        </span>
<?php endif; ?>
      </div>
    </div>

  </div>

<?php if (function_exists ('ai_settings_container')) ai_settings_container ($block, $obj); ?>

  <div id="settings-<?php echo $block; ?>">

<?php if (defined ('AI_CODE_GENERATOR')) : ?>
  <div id="ai-rotation-container-<?php echo $block; ?>" class='ai-rotate' style="padding: 0; margin: 8px 0; border: 0; display: none;">

    <div class="max-input" style="height: 28px; position: absolute; top: 4px; left: -6px;">
      <span style="display: table-cell; width: 100%;"></span>
      <span class="ai-toolbar-button">
        <input type="checkbox" id="remove-option-<?php echo $block; ?>" style="display: none;" />
        <label class="checkbox-button" for="remove-option-<?php echo $block; ?>" title="Remove option"><span class="checkbox-icon icon-minus"></span></label>
      </span>
      <span class="ai-toolbar-button">
        <input type="checkbox" id="add-option-<?php echo $block; ?>" style="display: none;" />
        <label class="checkbox-button" for="add-option-<?php echo $block; ?>" title="Add option"><span class="checkbox-icon icon-plus"></span></label>
      </span>
    </div>

    <ul>
    </ul>

  </div>

  <div id="ai-code-generator-container-<?php echo $block; ?>" style="padding: 0; margin: 8px 0; border: 0; display: none;">

    <div class="max-input" style="height: 28px; position: absolute; top: 4px; left: -6px;">
      <span style="display: table-cell; width: 100%;"></span>
      <span class="ai-toolbar-button">
        <input type="checkbox" id="import-code-<?php echo $block; ?>" style="display: none;" />
        <label class="checkbox-button" for="import-code-<?php echo $block; ?>" title="Import code"><span class="checkbox-icon icon-import"></span></label>
      </span>
      <span class="ai-toolbar-button">
        <input type="checkbox" id="generate-code-<?php echo $block; ?>" style="display: none;" />
        <label class="checkbox-button" for="generate-code-<?php echo $block; ?>" title="Generate code"><span class="checkbox-icon icon-generate"></span></label>
      </span>
    </div>

    <ul>
      <li id="ai-banner-<?php echo $block; ?>"><a href="#tab-banner-<?php echo $block; ?>">Banner</a></li>
      <li id="ai-adsense-pub-id-<?php echo $block; ?>"><a href="#tab-adsense-<?php echo $block; ?>">AdSense</a></li>
    </ul>

    <div id="tab-banner-<?php echo $block; ?>" class="ai-banner ai-banner-top responsive-table rounded">
      <div class="banner-preview">
        <a id="banner-link-<?php echo $block; ?>" class="clear-link" target="_blank"><img id="banner-image-<?php echo $block; ?>" src="" style="display: none;" /></a>
      </div>
      <table class="ai-settings-table">
        <tr>
          <td style="padding-right: 7px;">
            Image
          </td>
          <td style="width: 98%;">
            <input id="banner-image-url-<?php echo $block; ?>" style="width: 100%;" type="text" size="50" maxlength="200" />
          </td>
        </tr>
        <tr>
          <td>
            Link
          </td>
          <td>
            <input id="banner-url-<?php echo $block; ?>" style="width: 100%;" type="text" size="50" maxlength="200" />
          </td>
        </tr>
        <tr>
          <td>
            <input type="checkbox" id="open-new-tab-<?php echo $block; ?>" />
          </td>
          <td>
            <label for="open-new-tab-<?php echo $block; ?>" style="display: inline-block; margin-top: 6px;">Open link in a new tab</label>
            <button id="select-image-button-<?php echo $block; ?>" type="button" class='ai-button select-image' style="display: none; width: 120px; float: right; margin: 7px 0 0 0;">Select Image</button>
            <button id="select-placeholder-button-<?php echo $block; ?>" type="button" class='ai-button select-image' style="display: none; width: 120px; float: right; margin: 7px 10px 0 0;">Select Placeholder</button>
          </td>
        </tr>
      </table>
      <div style="clear: both;"></div>
    </div>

    <div id="tab-adsense-<?php echo $block; ?>" class="responsive-table rounded">

      <table class="ai-settings-table left">
        <tr>
          <td>
            Comment
          </td>
          <td style="width: 100%; padding-left: 7px;">
            <input id="adsense-comment-<?php echo $block; ?>" style="width: 100%;" type="text" size="30" maxlength="50" />
          </td>
        </tr>

        <tr>
          <td>
            Publisher ID
          </td>
          <td style="width: 100%; padding-left: 7px;">
            <input id="adsense-publisher-id-<?php echo $block; ?>" style="width: 100%;" type="text" size="30" maxlength="30" />
          </td>
        </tr>

        <tr>
          <td>
            Ad Slot ID
          </td>
          <td style="padding-left: 7px;">
            <input id="adsense-ad-slot-id-<?php echo $block; ?>" style="width: 100%;" type="text" size="30" maxlength="30" />
          </td>
        </tr>

        <tr>
          <td>
            Ad Type
          </td>
          <td style="padding-left: 7px;">
            <select id="adsense-type-<?php echo $block; ?>">
               <option value="<?php echo AI_ADSENSE_STANDARD; ?>" selected><?php echo AI_TEXT_STANDARD; ?></option>
               <option value="<?php echo AI_ADSENSE_LINK; ?>"><?php echo AI_TEXT_LINK; ?></option>
               <option value="<?php echo AI_ADSENSE_IN_ARTICLE; ?>"><?php echo AI_TEXT_IN_ARTICLE; ?></option>
               <option value="<?php echo AI_ADSENSE_IN_FEED; ?>"><?php echo AI_TEXT_IN_FEED; ?></option>
               <option value="<?php echo AI_ADSENSE_MATCHED_CONTENT; ?>"><?php echo AI_TEXT_MATCHED_CONTENT; ?></option>
               <option value="<?php echo AI_ADSENSE_AUTO; ?>"><?php echo AI_TEXT_AUTO; ?></option>
            </select>
            <div class="adsense-size" style="float: right;">
              Size
              <select id="adsense-size-<?php echo $block; ?>">
                 <option value="<?php echo AI_ADSENSE_SIZE_FIXED; ?>" selected><?php echo AI_TEXT_FIXED; ?></option>
                 <option value="<?php echo AI_ADSENSE_SIZE_FIXED_BY_VIEWPORT; ?>"><?php echo AI_TEXT_FIXED_BY_VIEWPORT; ?></option>
                 <option value="<?php echo AI_ADSENSE_SIZE_RESPONSIVE; ?>"><?php echo AI_TEXT_RESPONSIVE; ?></option>
              </select>
            </div>
          </td>
        </tr>

        <tr>
          <td>
            AMP Ad
          </td>
          <td style="padding-left: 7px;">
            <select id="adsense-amp-<?php echo $block; ?>">
              <option value="<?php echo AI_ADSENSE_AMP_DISABLED; ?>" selected><?php echo AI_TEXT_DISABLED; ?></option>
              <option value="<?php echo AI_ADSENSE_AMP_ABOVE_THE_FOLD; ?>"><?php echo AI_TEXT_ABOVE_THE_FOLD; ?></option>
              <option value="<?php echo AI_ADSENSE_AMP_BELOW_THE_FOLD; ?>"><?php echo AI_TEXT_BELOW_THE_FOLD; ?></option>
            </select>
          </td>
        </tr>

        <tr>
          <td>
          </td>
          <td>
<?php if (defined ('AI_ADSENSE_API')) : ?>
<?php if (!defined ('AI_ADSENSE_AUTHORIZATION_CODE')) : ?>
            <button type="button" class='ai-button adsense-list' style="display: none; margin: 2px 0px 0px 7px;" title="Show AdSense ad units from your account">AdSense ad units</button>
<?php endif; ?>
<?php endif; ?>
          </td>
        </tr>
      </table>

      <table id="adsense-layout-<?php echo $block; ?>" class="ai-settings-table right">
        <tr>
          <td></td>
          <td>
            <input style="visibility: hidden;" type="text" size="1" maxlength="1" />
          </td>
        </tr>

        <tr>
          <td class="adsense-layout" style="padding-left: 7px;">
            Layout
          </td>
          <td class="adsense-layout" style="width: 100%; padding-left: 7px;">
            <input id="adsense-layout-<?php echo $block; ?>" style="width: 100%;" type="text" size="80" maxlength="100" />
          </td>
        </tr>

        <tr>
          <td class="adsense-layout" style="padding-left: 7px;">
            Layout Key
          </td>
          <td class="adsense-layout" style="padding-left: 7px;">
            <input id="adsense-layout-key-<?php echo $block; ?>" style="width: 100%;" type="text" size="80" maxlength="100" />
          </td>
        </tr>

        <tr>
          <td style="padding-left: 7px; padding-top: 1px; float: left;">
            <span class="adsense-fixed-size ad-size">
              <select class="adsense-ad-size fixed">
                 <option value="&nbsp;" selected></option>
                 <option value="300x250">300x250</option>
                 <option value="336x280">336x280</option>
                 <option value="728x90" >728x90</option>
                 <option value="300x600">300x600</option>
                 <option value="320x100">320x100</option>
                 <option value="468x60" >468x60</option>
                 <option value="234x60" >234x60</option>
                 <option value="125x125">125x125</option>
                 <option value="250x250">250x250</option>
                 <option value="200x200">200x200</option>
                 <option value="120x600">120x600</option>
                 <option value="160x600">160x600</option>
                 <option value="300x1050">300x1050</option>
                 <option value="320x50">320x50</option>
                 <option value="970x90">970x90</option>
                 <option value="970x250">970x250</option>
              </select>
            </span>
          </td>
          <td>
          </td>
        </tr>

      </table>

      <table id="adsense-viewports-<?php echo $block; ?>" class="ai-settings-table right" style="display: none; width: auto;">
<?php
      for ($viewport = 1; $viewport <= AD_INSERTER_VIEWPORTS; $viewport ++) {
        $viewport_name  = get_viewport_name ($viewport);
        $viewport_width = get_viewport_width ($viewport);
        if ($viewport_name != '') { ?>
        <tr class="adsense-viewport ad-size">
          <td style="max-width: 210px; padding-left: 10px; overflow: hidden;">
            <?php echo $viewport_name; ?>
          </td>
          <td style="padding-left: 7px;">
            <select class="adsense-ad-size">
               <option value="&nbsp;" selected></option>
               <option value="300x250">300x250</option>
               <option value="336x280">336x280</option>
               <option value="728x90" >728x90</option>
               <option value="300x600">300x600</option>
               <option value="320x100">320x100</option>
               <option value="468x60" >468x60</option>
               <option value="234x60" >234x60</option>
               <option value="125x125">125x125</option>
               <option value="250x250">250x250</option>
               <option value="200x200">200x200</option>
               <option value="120x600">120x600</option>
               <option value="160x600">160x600</option>
               <option value="300x1050">300x1050</option>
               <option value="320x50">320x50</option>
               <option value="970x90">970x90</option>
               <option value="970x250">970x250</option>
            </select>
          </td>
        </tr>
<?php   }
      }
?>
      </table>

      <div style="clear: both;"></div>

    </div>

<!--    <div style="padding: 0; min-height: 28px;">-->
<!--      <div style="float: left;">-->
<!--        <button id="import-button2-<?php echo $block; ?>" type="button" class='ai-button' style="display: none; margin-right: 4px;" title="">Import</button>-->
<!--      </div>-->
<!--      <div style="float: right;">-->
<!--        <button id="generate-button2-<?php echo $block; ?>" type="button" class='ai-button' style="display: none; margin-right: 4px;" title="">Generate</button>-->
<!--      </div>-->
<!--      <div style="clear: both;"></div>-->
<!--    </div>-->

  </div>
<?php endif; ?>

  <div style="margin: 8px 0;">
    <textarea id="block-<?php echo $block; ?>" class="simple-editor" style="background-color:#F9F9F9; font-family: Courier, 'Courier New', monospace; font-weight: bold;" name="<?php echo AI_OPTION_CODE, WP_FORM_FIELD_POSTFIX, $block; ?>" default=""><?php echo esc_textarea ($obj->get_ad_data()); ?></textarea>
  </div>

  <div style="padding: 0; min-height: 28px;">
    <div style="float: left;">
      <button id="lists-button-<?php echo $block; ?>" type="button" class='ai-button2' style="display: none; margin-right: 4px;" title="White/Black-list Category, Tag, Url, Referer (domain) or Country"><span style="<?php echo $lists_style; ?>">Lists</span></button>
      <button id="manual-button-<?php echo $block; ?>" type="button" class='ai-button2' style="display: none; margin-right: 4px;" title="Widget, Shortcode and PHP function call"><span style="<?php echo $manual_style; ?>">Manual</span></button>
      <button id="device-detection-button-<?php echo $block; ?>" class='ai-button2' type="button" style="display: none; margin-right: 4px;" title="Client/Server-side Device Detection (Desktop, Tablet, Phone,...)"><span style="<?php echo $devices_style; ?>">Devices</span></button>
      <button id="misc-button-<?php echo $block; ?>" type="button" class='ai-button2' style="display: none; margin-right: 4px;" title="Check for user status, Limit insertions, [error 404 page, Ajax requests, RSS feeds], Filter, Scheduling, General tag"><span style="<?php echo $misc_style; ?>">Misc</span></button>
      <button id="preview-button-<?php echo $block; ?>" type="button" class='ai-button2' style="display: none; margin-right: 4px;" title="Preview code and alignment" site-url="<?php echo wp_make_link_relative (get_site_url()); ?>">Preview</button>
    </div>
    <div style="float: right;">
<?php if (function_exists ('ai_settings_bottom_buttons')) ai_settings_bottom_buttons ($start, $end); else { ?>
      <input style="display: none; font-weight: bold;" name="<?php echo AI_FORM_SAVE; ?>" value="Save All Settings" type="submit" />
<?php } ?>
    </div>
    <div style="clear: both;"></div>
  </div>

  <div class="rounded">
    <div style="float: left;">
      Automatic Insertion
      <select class="ai-image-selection" style="width:200px; margin-bottom: 3px;" id="display-type-<?php echo $block; ?>" name="<?php echo AI_OPTION_AUTOMATIC_INSERTION, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_automatic_insertion(); ?>">
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-disabled" value="<?php echo AI_AUTOMATIC_INSERTION_DISABLED; ?>" data-title="<?php echo AI_TEXT_DISABLED; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_DISABLED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_DISABLED; ?></option>
<?php if (defined ('AI_BUFFERING') && get_output_buffering ()) : ?>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-above-header" value="<?php echo AI_AUTOMATIC_INSERTION_ABOVE_HEADER; ?>" data-title="<?php echo AI_TEXT_ABOVE_HEADER; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_ABOVE_HEADER) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_ABOVE_HEADER; ?></option>
<?php endif; ?>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-before-post" value="<?php echo AI_AUTOMATIC_INSERTION_BEFORE_POST; ?>" data-title="<?php echo AI_TEXT_BEFORE_POST; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_BEFORE_POST) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_BEFORE_POST; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-before-content" value="<?php echo AI_AUTOMATIC_INSERTION_BEFORE_CONTENT; ?>" data-title="<?php echo AI_TEXT_BEFORE_CONTENT; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_BEFORE_CONTENT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_BEFORE_CONTENT; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-before-paragraph" value="<?php echo AI_AUTOMATIC_INSERTION_BEFORE_PARAGRAPH; ?>" data-title="<?php echo AI_TEXT_BEFORE_PARAGRAPH; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_BEFORE_PARAGRAPH) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_BEFORE_PARAGRAPH; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-after-paragraph" value="<?php echo AI_AUTOMATIC_INSERTION_AFTER_PARAGRAPH; ?>" data-title="<?php echo AI_TEXT_AFTER_PARAGRAPH; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_AFTER_PARAGRAPH) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_AFTER_PARAGRAPH; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-after-content" value="<?php echo AI_AUTOMATIC_INSERTION_AFTER_CONTENT; ?>" data-title="<?php echo AI_TEXT_AFTER_CONTENT; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_AFTER_CONTENT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_AFTER_CONTENT; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-after-post" value="<?php echo AI_AUTOMATIC_INSERTION_AFTER_POST; ?>" data-title="<?php echo AI_TEXT_AFTER_POST; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_AFTER_POST) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_AFTER_POST; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-before-excerpts" value="<?php echo AI_AUTOMATIC_INSERTION_BEFORE_EXCERPT; ?>" data-title="<?php echo AI_TEXT_BEFORE_EXCERPT; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_BEFORE_EXCERPT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_BEFORE_EXCERPT; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-after-excerpts" value="<?php echo AI_AUTOMATIC_INSERTION_AFTER_EXCERPT; ?>" data-title="<?php echo AI_TEXT_AFTER_EXCERPT; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_AFTER_EXCERPT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_AFTER_EXCERPT; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-between-posts" value="<?php echo AI_AUTOMATIC_INSERTION_BETWEEN_POSTS; ?>" data-title="<?php echo AI_TEXT_BETWEEN_POSTS; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_BETWEEN_POSTS) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_BETWEEN_POSTS; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-before-comments" value="<?php echo AI_AUTOMATIC_INSERTION_BEFORE_COMMENTS; ?>" data-title="<?php echo AI_TEXT_BEFORE_COMMENTS; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_BEFORE_COMMENTS) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_BEFORE_COMMENTS; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-between-comments" value="<?php echo AI_AUTOMATIC_INSERTION_BETWEEN_COMMENTS; ?>" data-title="<?php echo AI_TEXT_BETWEEN_COMMENTS; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_BETWEEN_COMMENTS) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_BETWEEN_COMMENTS; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-after-comments" value="<?php echo AI_AUTOMATIC_INSERTION_AFTER_COMMENTS; ?>" data-title="<?php echo AI_TEXT_AFTER_COMMENTS; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_AFTER_COMMENTS) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_AFTER_COMMENTS; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-footer" value="<?php echo AI_AUTOMATIC_INSERTION_FOOTER; ?>" data-title="<?php echo AI_TEXT_FOOTER; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_FOOTER) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_FOOTER; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-before-html" value="<?php echo AI_AUTOMATIC_INSERTION_BEFORE_HTML_ELEMENT; ?>" data-title="<?php echo AI_TEXT_BEFORE_HTML_ELEMENT; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_BEFORE_HTML_ELEMENT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_BEFORE_HTML_ELEMENT; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-after-html" value="<?php echo AI_AUTOMATIC_INSERTION_AFTER_HTML_ELEMENT; ?>" data-title="<?php echo AI_TEXT_AFTER_HTML_ELEMENT; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_AFTER_HTML_ELEMENT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_AFTER_HTML_ELEMENT; ?></option>
<?php foreach ($ai_custom_hooks as $hook_index => $custom_hook) { ?>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-custom-hook" value="<?php echo AI_AUTOMATIC_INSERTION_CUSTOM_HOOK + $custom_hook ['index'] - 1; ?>" data-title="<?php echo $custom_hook ['name']; ?>" <?php echo ($automatic_insertion == AI_AUTOMATIC_INSERTION_CUSTOM_HOOK + $custom_hook ['index'] - 1) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo $custom_hook ['name']; ?></option>
<?php } ?>
      </select>
    </div>

    <div style="float: right;">
      Alignment and Style&nbsp;&nbsp;&nbsp;
      <select style="width:130px;" id="block-alignment-<?php echo $block; ?>" name="<?php echo AI_OPTION_ALIGNMENT_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_alignment_type(); ?>">
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-default" value="<?php echo AI_ALIGNMENT_DEFAULT; ?>" data-title="<?php echo AI_TEXT_DEFAULT; ?>" <?php echo ($obj->get_alignment_type() == AI_ALIGNMENT_DEFAULT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_DEFAULT; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-align-left" value="<?php echo AI_ALIGNMENT_LEFT; ?>" data-title="<?php echo AI_TEXT_LEFT; ?>" <?php echo ($obj->get_alignment_type() == AI_ALIGNMENT_LEFT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_LEFT; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-center" value="<?php echo AI_ALIGNMENT_CENTER; ?>" data-title="<?php echo AI_TEXT_CENTER; ?>" <?php echo ($obj->get_alignment_type() == AI_ALIGNMENT_CENTER) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_CENTER; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-align-right" value="<?php echo AI_ALIGNMENT_RIGHT; ?>" data-title="<?php echo AI_TEXT_RIGHT; ?>" <?php echo ($obj->get_alignment_type() == AI_ALIGNMENT_RIGHT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_RIGHT; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-float-left" value="<?php echo AI_ALIGNMENT_FLOAT_LEFT; ?>" data-title="<?php echo AI_TEXT_FLOAT_LEFT; ?>" <?php echo ($obj->get_alignment_type() == AI_ALIGNMENT_FLOAT_LEFT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_FLOAT_LEFT; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-float-right" value="<?php echo AI_ALIGNMENT_FLOAT_RIGHT; ?>" data-title="<?php echo AI_TEXT_FLOAT_RIGHT; ?>" <?php echo ($obj->get_alignment_type() == AI_ALIGNMENT_FLOAT_RIGHT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_FLOAT_RIGHT; ?></option>
<?php if (function_exists ('ai_style_options')) ai_style_options ($obj); ?>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-custom-css" value="<?php echo AI_ALIGNMENT_CUSTOM_CSS; ?>" data-title="<?php echo AI_TEXT_CUSTOM_CSS; ?>" <?php echo ($obj->get_alignment_type() == AI_ALIGNMENT_CUSTOM_CSS) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_CUSTOM_CSS; ?></option>
         <option data-img-src="<?php echo plugins_url ('css/images/blank.png', __FILE__); ?>" data-img-class="automatic-insertion im-no-wrapping" value="<?php echo AI_ALIGNMENT_NO_WRAPPING; ?>" data-title="<?php echo AI_TEXT_NO_WRAPPING; ?>" <?php echo ($obj->get_alignment_type() == AI_ALIGNMENT_NO_WRAPPING) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_NO_WRAPPING; ?></option>
      </select>
      &nbsp;
      <button id="show-css-button-<?php echo $block; ?>" type="button" class='ai-button' style="min-width: 60px; margin-right: 0px;">Show</button>
    </div>
    <div style="clear: both;"></div>

    <div id="icons-css-code-<?php echo $block; ?>" style="margin: 4px 0 0; display: none;">
      <div id="automatic-insertion-<?php echo $block; ?>"></div>
      <div id="alignment-style-<?php echo $block; ?>" style="margin-bottom: 4px;"></div>

<?php if (function_exists ('ai_sticky_position')) ai_sticky_position ($block, $obj, $default); ?>

      <div class="max-input">
        <span id="css-label-<?php echo $block; ?>" style="display: table-cell; width: 36px; padding: 0; height: 26px; vertical-align: middle; margin: 4px 0 0 0; font-size: 14px; font-weight: bold;">CSS</span>
        <input id="custom-css-<?php echo $block; ?>" style="width: 100%; display: none; font-family: Courier, 'Courier New', monospace; font-weight: bold;" type="text" name="<?php echo AI_OPTION_CUSTOM_CSS, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_custom_css(); ?>" value="<?php echo $obj->get_custom_css(); ?>" maxlength="500" title="Custom CSS code for wrapping div" />
        <span style="display: table-cell; vertical-align: middle; font-family: Courier, 'Courier New', monospace; font-size: 12px; font-weight: bold; cursor: pointer;">
          <span id="css-no-wrapping-<?php echo $block; ?>" class='css-code' style="height: 26px; padding-left: 7px; display: none;"></span>
          <span id="css-none-<?php echo $block; ?>" class='css-code-<?php echo $block; ?>' style="height: 18px; padding-left: 7px; display: none;" title="CSS code for wrapping div, click to edit"><?php echo $obj->alignment_style (AI_ALIGNMENT_DEFAULT); ?></span>
          <span id="css-left-<?php echo $block; ?>" class='css-code-<?php echo $block; ?>' style="height: 18px; padding-left: 7px; display: none;" title="CSS code for wrapping div, click to edit"><?php echo $obj->alignment_style (AI_ALIGNMENT_LEFT); ?></span>
          <span id="css-right-<?php echo $block; ?>" class='css-code-<?php echo $block; ?>' style="height: 18px; padding-left: 7px; display: none;" title="CSS code for wrapping div, click to edit"><?php echo $obj->alignment_style (AI_ALIGNMENT_RIGHT); ?></span>
          <span id="css-center-<?php echo $block; ?>" class='css-code-<?php echo $block; ?>' style="height: 18px; padding-left: 7px; display: none;" title="CSS code for wrapping div, click to edit"><?php echo $obj->alignment_style (AI_ALIGNMENT_CENTER); ?></span>
          <span id="css-float-left-<?php echo $block; ?>" class='css-code-<?php echo $block; ?>' style="height: 18px; padding-left: 7px; display: none;" title="CSS code for wrapping div, click to edit"><?php echo $obj->alignment_style (AI_ALIGNMENT_FLOAT_LEFT); ?></span>
          <span id="css-float-right-<?php echo $block; ?>" class='css-code-<?php echo $block; ?>' style="height: 18px; padding-right: 7px; display: none;" title="CSS code for wrapping div, click to edit"><?php echo $obj->alignment_style (AI_ALIGNMENT_FLOAT_RIGHT); ?></span>
<?php if (function_exists ('ai_style_css')) ai_style_css ($block, $obj); ?>
        </span>
        <span style="display:table-cell; width: 46px;" ><button id="edit-css-button-<?php echo $block; ?>" type="button" class='ai-button' style="display: table-cell; padding: 0; margin: 0 0 0 8px;">Edit</button></span>
      </div>
    </div>
  </div>

<?php if (function_exists ('ai_sticky_animation')) ai_sticky_animation ($block, $obj, $default); ?>

  <div class="responsive-table small-button rounded">
    <table>
      <tr>
        <td style="width: 70%; padding-bottom: 5px;">
          <input type="hidden" name="<?php echo AI_OPTION_DISPLAY_ON_POSTS, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
          <input type="checkbox" name="<?php echo AI_OPTION_DISPLAY_ON_POSTS, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_display_settings_post(); ?>" id="display-posts-<?php echo $block; ?>" title="Enable or disable insertion on posts" <?php if ($obj->get_display_settings_post()==AI_ENABLED) echo 'checked '; ?> />

          <select style="margin: 0 0 0 10px;" id="enabled-on-which-posts-<?php echo $block; ?>" name="<?php echo AI_OPTION_ENABLED_ON_WHICH_POSTS, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_ad_enabled_on_which_posts(); ?>" style="width:160px" title="Individual post exceptions (if enabled here) can be configured in post editor. Leave blank for no individual post exceptions.">
             <option value="<?php echo AI_NO_INDIVIDUAL_EXCEPTIONS; ?>" <?php echo ($obj->get_ad_enabled_on_which_posts()==AI_NO_INDIVIDUAL_EXCEPTIONS) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_NO_INDIVIDUAL_EXCEPTIONS; ?></option>
             <option value="<?php echo AI_INDIVIDUALLY_DISABLED; ?>" <?php echo ($obj->get_ad_enabled_on_which_posts()==AI_INDIVIDUALLY_DISABLED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_INDIVIDUALLY_DISABLED; ?></option>
             <option value="<?php echo AI_INDIVIDUALLY_ENABLED; ?>" <?php echo ($obj->get_ad_enabled_on_which_posts()==AI_INDIVIDUALLY_ENABLED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_INDIVIDUALLY_ENABLED; ?></option>
          </select>
          &nbsp;
          <label for="display-posts-<?php echo $block; ?>" title="Individual post exceptions (if enabled here) can be configured in post editor. Leave blank for no individual post exceptions.">Posts</label>

<?php
  if (!empty ($block_exceptions [$block])) {
?>
          <button id="exceptions-button-<?php echo $block; ?>" type="button" class='ai-button' style="display: none; width: 15px; height: 15px; margin-left: 20px;" title="Toggle list of individual exceptions"></button>
<?php
  }
?>

        </td>
        <td style="padding-left: 8px;">
        </td>
        <td style="padding-left: 8px;">
          <input type="hidden" name="<?php echo AI_OPTION_DISPLAY_ON_HOMEPAGE, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
          <input id= "display-homepage-<?php echo $block; ?>" style="margin-left: 10px;" type="checkbox" name="<?php echo AI_OPTION_DISPLAY_ON_HOMEPAGE, WP_FORM_FIELD_POSTFIX, $block; ?>" title="Enable or disable insertion on homepage: latest posts (including sub-pages), static page or theme homepage" value="1" default="<?php echo $default->get_display_settings_home(); ?>" <?php if ($obj->get_display_settings_home()==AI_ENABLED) echo 'checked '; ?> />
          <label for="display-homepage-<?php echo $block; ?>" title="Enable or disable insertion on homepage: latest posts (including sub-pages), static page or theme homepage">Homepage</label>
        </td>
        <td style="padding-left: 8px;">
          <input type="hidden" name="<?php echo AI_OPTION_DISPLAY_ON_CATEGORY_PAGES, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
          <input id= "display-category-<?php echo $block; ?>" style="margin-left: 10px;" type="checkbox" name="<?php echo AI_OPTION_DISPLAY_ON_CATEGORY_PAGES, WP_FORM_FIELD_POSTFIX, $block; ?>" title="Enable or disable insertion on category blog pages (including sub-pages)" value="1" default="<?php echo $default->get_display_settings_category(); ?>" <?php if ($obj->get_display_settings_category()==AI_ENABLED) echo 'checked '; ?> />
          <label for="display-category-<?php echo $block; ?>" title="Enable or disable insertion on category blog pages (including sub-pages)">Category pages</label>
        </td>
      </tr>

      <tr>
        <td style="width: 70%">
          <input type="hidden" name="<?php echo AI_OPTION_DISPLAY_ON_PAGES, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
          <input type="checkbox" name="<?php echo AI_OPTION_DISPLAY_ON_PAGES, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_display_settings_page(); ?>" id="display-pages-<?php echo $block; ?>" title="Enable or disable insertion on static pages" <?php if ($obj->get_display_settings_page()==AI_ENABLED) echo 'checked '; ?> />

          <select style="margin: 0 0 0 10px;" id="enabled-on-which-pages-<?php echo $block; ?>" name="<?php echo AI_OPTION_ENABLED_ON_WHICH_PAGES, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_ad_enabled_on_which_pages(); ?>" style="width:160px" title="Individual static page exceptions (if enabled here) can be configured in page editor. Leave blank for no individual page exceptions.">
             <option value="<?php echo AI_NO_INDIVIDUAL_EXCEPTIONS; ?>" <?php echo ($obj->get_ad_enabled_on_which_pages()==AI_NO_INDIVIDUAL_EXCEPTIONS) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_NO_INDIVIDUAL_EXCEPTIONS; ?></option>
             <option value="<?php echo AI_INDIVIDUALLY_DISABLED; ?>" <?php echo ($obj->get_ad_enabled_on_which_pages()==AI_INDIVIDUALLY_DISABLED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_INDIVIDUALLY_DISABLED; ?></option>
             <option value="<?php echo AI_INDIVIDUALLY_ENABLED; ?>" <?php echo ($obj->get_ad_enabled_on_which_pages()==AI_INDIVIDUALLY_ENABLED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_INDIVIDUALLY_ENABLED; ?></option>
          </select>
          &nbsp;
          <label for="display-pages-<?php echo $block; ?>" title="Individual static page exceptions (if enabled here) can be configured in page editor. Leave blank for no individual page exceptions.">Static pages</label>
        </td>
        <td style="padding-left: 8px;">
        </td>
        <td style="padding-left: 8px;">
          <input type="hidden" name="<?php echo AI_OPTION_DISPLAY_ON_SEARCH_PAGES, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
          <input id= "display-search-<?php echo $block; ?>" style="margin-left: 10px;" type="checkbox" name="<?php echo AI_OPTION_DISPLAY_ON_SEARCH_PAGES, WP_FORM_FIELD_POSTFIX, $block; ?>" title="Enable or disable insertion on search blog pages" value="1" default="<?php echo $default->get_display_settings_search(); ?>" <?php if ($obj->get_display_settings_search()==AI_ENABLED) echo 'checked '; ?> />
          <label for="display-search-<?php echo $block; ?>" title="Enable or disable insertion on search blog pages">Search pages</label>
        </td>
        <td style="padding-left: 8px;">
          <input type="hidden" name="<?php echo AI_OPTION_DISPLAY_ON_ARCHIVE_PAGES, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
          <input id= "display-archive-<?php echo $block; ?>" style="margin-left: 10px;" type="checkbox" name="<?php echo AI_OPTION_DISPLAY_ON_ARCHIVE_PAGES, WP_FORM_FIELD_POSTFIX, $block; ?>" title="Enable or disable insertion on tag or archive blog pages" value="1" default="<?php echo $default->get_display_settings_archive(); ?>" <?php if ($obj->get_display_settings_archive()==AI_ENABLED) echo 'checked '; ?> />
          <label for="display-archive-<?php echo $block; ?>" title="Enable or disable insertion on tag or archive blog pages">Tag / Archive pages</label>
        </td>
      </tr>
    </table>
  </div>

  <div id="block-exceptions-<?php echo $block; ?>" class="responsive-table rounded" style="display: none;">
<?php

  if (!empty ($block_exceptions [$block])) {
?>
    <table class="exceptions" cellspacing=0 cellpadding=0><tbody>
      <tr>
        <th class="id">ID</th><th class="type">Type</th><th class="page page-only">&nbsp;Title</th><th>
          <input id="clear-block-exceptions-<?php echo $block; ?>"
                  onclick="if (confirm('Are you sure you want to clear all exceptions for block <?php echo $block; ?>?')) {document.getElementById ('clear-block-exceptions-<?php echo $block; ?>').style.visibility = 'hidden'; document.getElementById ('clear-block-exceptions-<?php echo $block; ?>').style.fontSize = '1px'; document.getElementById ('clear-block-exceptions-<?php echo $block; ?>').value = '<?php echo $block; ?>'; return true;} return false"
                  title="Clear all exceptions for block <?php echo $block; ?>"
                  name="<?php echo AI_FORM_CLEAR_EXCEPTIONS; ?>"
                  value="&#x274C;"
                  type="submit"
                  style="padding: 1px 3px; border: 0; background: transparent; font-size: 8px; color: #e44;" /></th>
      </tr>
<?php
    foreach ($block_exceptions [$block] as $id => $exception) {
?>
      <tr>
        <td class="id"><a href="<?php
        echo get_permalink ($id); ?>" target="_blank" title="View" style="color: #222;"><?php
        echo $id; ?></a></td><td class="type"><?php
        echo $exception ['name']; ?></td><td class="page page-only"><a href="<?php
        echo get_edit_post_link ($id); ?>" target="_blank" title="Edit" style="margin-left: 2px; color: #222;"><?php
        echo $exception ['title']; ?></a></td><td></td>
      </tr>
<?php
    }
?>

    </tbody></table>
<?php
  };
?>
  </div>

  <div id="html-element-settings-<?php echo $block; ?>" class="rounded" style="<?php echo $html_settings ? "" : " display: none;" ?>">
    <div class="max-input" style="margin: 0 0 8px 0;">
      <span style="display: table-cell; width: 1px; white-space: nowrap;">
        HTML element(s)
        &nbsp;&nbsp;
      </span>
      <span style="display: table-cell;">
        <input
          type="text"
          name="<?php echo AI_OPTION_HTML_SELECTOR, WP_FORM_FIELD_POSTFIX, $block; ?>"
          default="<?php echo $default->get_html_selector (); ?>"
          value="<?php echo $obj->get_html_selector (); ?>"
          title="HTML element selector or comma separated list of selectors (.class, #id)"
          style="width: 100%;"
          maxlength="80" />
      </span>
    </div>
    <div class="max-input" style="margin: 8px 0 0 0;">
      <span style="display: table-cell; width: 1px; white-space: nowrap;">
        Insertion
        <select id="html-element-insertion-<?php echo $block; ?>" style="margin-bottom: 3px;" name="<?php echo AI_OPTION_HTML_ELEMENT_INSERTION, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_html_element_insertion (); ?>" title="Client-side insertion uses Javascript to insert code block when the page loads, server-side insertion inserts when the page is generated but needs Output buffering enabled">
           <option value="<?php echo AI_HTML_INSERTION_CLIENT_SIDE; ?>" <?php echo ($html_element_insertion == AI_HTML_INSERTION_CLIENT_SIDE) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_CLIENT_SIDE; ?></option>
           <option value="<?php echo AI_HTML_INSERTION_CLIENT_SIDE_DOM_READY; ?>" <?php echo ($html_element_insertion == AI_HTML_INSERTION_CLIENT_SIDE_DOM_READY) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_CLIENT_SIDE_DOM_READY; ?></option>
<?php if (defined ('AI_BUFFERING') && get_output_buffering ()) : ?>
           <option value="<?php echo AI_HTML_INSERTION_SEREVR_SIDE; ?>" <?php echo ($html_element_insertion == AI_HTML_INSERTION_SEREVR_SIDE) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_SERVER_SIDE; ?></option>
<?php endif; ?>
        </select>
      </span>
      <span id="server-side-insertion-<?php echo $block; ?>" style="display: table-cell; white-space: nowrap;<?php if ($html_element_insertion == AI_HTML_INSERTION_SEREVR_SIDE) echo 'display: none;'; ?>">
        &nbsp;
        Javascript code position
        <select style="max-width: 140px; margin-bottom: 3px;" name="<?php echo AI_OPTION_SERVER_SIDE_INSERTION, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_server_side_insertion (); ?>" title="Page position where the javascript code for client-side insertion will be inserted. Should be after the HTML element if not waiting for DOM ready.">
           <option value="<?php echo AI_AUTOMATIC_INSERTION_BEFORE_POST; ?>" <?php echo ($server_side_insertion == AI_AUTOMATIC_INSERTION_BEFORE_POST) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_BEFORE_POST; ?></option>
           <option value="<?php echo AI_AUTOMATIC_INSERTION_AFTER_POST; ?>" <?php echo ($server_side_insertion == AI_AUTOMATIC_INSERTION_AFTER_POST) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_AFTER_POST; ?></option>
           <option value="<?php echo AI_AUTOMATIC_INSERTION_FOOTER; ?>" <?php echo ($server_side_insertion == AI_AUTOMATIC_INSERTION_FOOTER) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_FOOTER; ?></option>
<?php foreach ($ai_custom_hooks as $hook_index => $custom_hook) { ?>
           <option value="<?php echo AI_AUTOMATIC_INSERTION_CUSTOM_HOOK + $custom_hook ['index'] - 1; ?>" <?php echo ($server_side_insertion == AI_AUTOMATIC_INSERTION_CUSTOM_HOOK + $custom_hook ['index'] - 1) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo $custom_hook ['name']; ?></option>
<?php } ?>
        </select>
      </span>
    </div>
  </div>

  <div id="paragraph-settings-<?php echo $block; ?>" class="rounded" style="<?php echo $paragraph_settings ? "" : " display: none;" ?>">
    <div style="float: left; margin-top: 1px;">
      Paragraph(s)
      <input
        type="text"
        name="<?php echo AI_OPTION_PARAGRAPH_NUMBER, WP_FORM_FIELD_POSTFIX, $block; ?>"
        default="<?php echo $default->get_paragraph_number(); ?>"
        value="<?php echo $obj->get_paragraph_number(); ?>"
        title="Paragraph number or comma separated paragraph numbers or empty (means all paragraphs): 1 to N means paragraph number, 0 means random paragraph, value between 0 and 1 means relative position on the page (0.2 means paragraph at 20% of page height, 0.5 means paragraph halfway down the page, 0.9 means paragraph at 90% of page paragraphs, etc.)"
        size="20"
        maxlength="50" />
    </div>

    <div style="float: right;">
      <button id="counting-button-<?php echo $block; ?>" type="button" class='ai-button' style="min-width: 85px; margin-right: 8px; display: none;">Counting</button>
      <button id="clearance-button-<?php echo $block; ?>" type="button" class='ai-button' style="min-width: 85px; margin-right: 0px; display: none;">Clearance</button>
    </div>

    <div style="clear: both;"></div>
  </div>

  <div id="paragraph-counting-<?php echo $block; ?>" class="rounded" style="<?php echo $paragraph_counting ? "" : "display: none;" ?>">
    <div class="max-input" style="margin: 0 0 8px 0;">
      <span style="display: table-cell; width: 1px; white-space: nowrap;">
        Count&nbsp;
        <select name="<?php echo AI_OPTION_DIRECTION_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_direction_type(); ?>">
          <option value="<?php echo AD_DIRECTION_FROM_TOP; ?>" <?php echo ($obj->get_direction_type()==AD_DIRECTION_FROM_TOP) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DIRECTION_FROM_TOP; ?></option>
          <option value="<?php echo AD_DIRECTION_FROM_BOTTOM; ?>" <?php echo ($obj->get_direction_type()==AD_DIRECTION_FROM_BOTTOM) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DIRECTION_FROM_BOTTOM; ?></option>
        </select>
        paragraphs with tags&nbsp;
      </span>
      <span style="display: table-cell;">
        <input
          style="width: 100%;"
          title="Comma separated HTML tag names, usually only 'p' tags are used"
          type="text" name="<?php echo AI_OPTION_PARAGRAPH_TAGS, WP_FORM_FIELD_POSTFIX, $block; ?>"
          default="<?php echo $default->get_paragraph_tags(); ?>"
          value="<?php echo $obj->get_paragraph_tags(); ?>"
          size="12"
          maxlength="50"/>
      </span>
      <span style="display: table-cell; width: 1px; white-space: nowrap;">
      &nbsp;
      that have between
      <input
        type="text"
        name="<?php echo AI_OPTION_MIN_PARAGRAPH_WORDS, WP_FORM_FIELD_POSTFIX, $block; ?>"
        default="<?php echo $default->get_minimum_paragraph_words(); ?>"
        value="<?php echo $obj->get_minimum_paragraph_words(); ?>"
        size="4"
        maxlength="5" />
      and
      <input
        type="text"
        name="<?php echo AI_OPTION_MAX_PARAGRAPH_WORDS, WP_FORM_FIELD_POSTFIX, $block; ?>"
        default="<?php echo $default->get_maximum_paragraph_words(); ?>"
        value="<?php echo $obj->get_maximum_paragraph_words(); ?>"
        title="Maximum number of paragraph words, leave empty for no limit"
        size="4"
        maxlength="5" />
      words
      </span>
    </div>

    <div class="max-input" style="margin: 8px 0 8px 0;">
      <span style="display: table-cell; width: 1px; white-space: nowrap;">
      and
        <select style="margin-bottom: 3px;" name="<?php echo AI_OPTION_PARAGRAPH_TEXT_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_paragraph_text_type(); ?>">
          <option value="<?php echo AD_CONTAIN; ?>" <?php echo ($obj->get_paragraph_text_type() == AD_CONTAIN) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_CONTAIN; ?></option>
          <option value="<?php echo AD_DO_NOT_CONTAIN; ?>" <?php echo ($obj->get_paragraph_text_type() == AD_DO_NOT_CONTAIN) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DO_NOT_CONTAIN; ?></option>
        </select>
      </span>
      <span class="small-input-tags" style="display: table-cell;">
      <input
        style="width: 100%;"
        title="Comma separated text"
        type="text"
        name="<?php echo AI_OPTION_PARAGRAPH_TEXT, WP_FORM_FIELD_POSTFIX, $block; ?>"
        default="<?php echo $default->get_paragraph_text(); ?>"
        value="<?php echo $obj->get_paragraph_text(); ?>"
        maxlength="200" />
      </span>
      <span style="display: table-cell; width: 1px; white-space: nowrap;">
        &nbsp;&nbsp;
        Minimum number of paragraphs
        <input
        type="text"
        name="<?php echo AI_OPTION_MIN_PARAGRAPHS, WP_FORM_FIELD_POSTFIX, $block; ?>"
        default="<?php echo $default->get_paragraph_number_minimum(); ?>"
        value="<?php echo $obj->get_paragraph_number_minimum() ?>"
        size="2"
        maxlength="3" />
      </span>
    </div>

    <div class="max-input" style="margin: 8px 0 0 0;">
      <span style="display: table-cell; width: 1px; white-space: nowrap;">
        <input type="hidden" name="<?php echo AI_OPTION_COUNT_INSIDE_BLOCKQUOTE, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
        <input id= "ignore_blockquote-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_COUNT_INSIDE_BLOCKQUOTE, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_count_inside_blockquote(); ?>" <?php if ($obj->get_count_inside_blockquote()==AI_ENABLED) echo 'checked '; ?> />
        <label for="ignore_blockquote-<?php echo $block; ?>" style="vertical-align: top;" title="Count also paragraphs inside <?php echo get_no_paragraph_counting_inside (); ?> elements - defined on general plugin settings page - Tab General">Count inside special elements</label>
      </span>

      <span class="small-input-tags" style="display: table-cell;">
      <input
        style="width: 100%; visibility: hidden;"
       />
      </span>

      <span style="display: table-cell; width: 1px; white-space: nowrap;">
        &nbsp;&nbsp;
        Minimum number of words in paragraphs above
        <input
        type="text"
        name="<?php echo AI_OPTION_MIN_WORDS_ABOVE, WP_FORM_FIELD_POSTFIX, $block; ?>"
        default="<?php echo $default->get_minimum_words_above(); ?>"
        value="<?php echo $obj->get_minimum_words_above() ?>"
        title="Used only with automatic insertion After paragraph and empty paragraph numbers"
        size="2"
        maxlength="4" />
      </span>
    </div>
  </div>

  <div id="paragraph-clearance-<?php echo $block; ?>" class="rounded" style="<?php echo $paragraph_clearance ? "" : "display: none;" ?>">
    <div class="max-input" style="margin: 0 0 8px 0">
      <span style="display: table-cell; width: 1px; white-space: nowrap;">
        In
        <input
        type="text"
        name="<?php echo AI_OPTION_AVOID_PARAGRAPHS_ABOVE, WP_FORM_FIELD_POSTFIX, $block; ?>"
        default="<?php echo $default->get_avoid_paragraphs_above(); ?>"
        value="<?php echo $obj->get_avoid_paragraphs_above(); ?>"
        title="Number of paragraphs above to check, leave empty to disable checking"
        size="2"
        maxlength="3" />
        paragraphs above avoid&nbsp;
      </span>
      <span style="display: table-cell;">
        <input
          style="width: 100%;"
          title="Comma separated text"
          type="text"
          name="<?php echo AI_OPTION_AVOID_TEXT_ABOVE, WP_FORM_FIELD_POSTFIX, $block; ?>"
          default="<?php echo $default->get_avoid_text_above(); ?>"
          value="<?php echo $obj->get_avoid_text_above(); ?>"
          maxlength="100" />
      </span>
    </div>

    <div class="max-input" style="margin: 8px 0">
      <span style="display: table-cell; width: 1px; white-space: nowrap;">
        In
        <input
        type="text"
        name="<?php echo AI_OPTION_AVOID_PARAGRAPHS_BELOW, WP_FORM_FIELD_POSTFIX, $block; ?>"
        default="<?php echo $default->get_avoid_paragraphs_below(); ?>"
        value="<?php echo $obj->get_avoid_paragraphs_below(); ?>"
        title="Number of paragraphs below to check, leave empty to disable checking"
        size="2"
        maxlength="3" />
        paragraphs below avoid&nbsp;
      </span>
      <span style="display: table-cell;">
        <input
          style="width: 100%;"
          title="Comma separated text"
          type="text"
          name="<?php echo AI_OPTION_AVOID_TEXT_BELOW, WP_FORM_FIELD_POSTFIX, $block; ?>"
          default="<?php echo $default->get_avoid_text_below(); ?>"
          value="<?php echo $obj->get_avoid_text_below(); ?>"
          maxlength="100" />
      </span>
    </div>

    <div style="margin: 8px 0 0 0;">
      If text is found
      <select  id="avoid-action-<?php echo $block; ?>" style="margin-bottom: 3px;" name="<?php echo AI_OPTION_AVOID_ACTION, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_avoid_action(); ?>">
        <option value="<?php echo AD_DO_NOT_INSERT; ?>" <?php echo ($obj->get_avoid_action() == AD_DO_NOT_INSERT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DO_NOT_INSERT; ?></option>
        <option value="<?php echo AD_TRY_TO_SHIFT_POSITION; ?>" <?php echo ($obj->get_avoid_action() == AD_TRY_TO_SHIFT_POSITION) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_TRY_TO_SHIFT_POSITION; ?></option>
      </select>
      <span id="check-up-to-<?php echo $block; ?>">
        &mdash; check up to
        <input
        type="text"
        name="<?php echo AI_OPTION_AVOID_TRY_LIMIT, WP_FORM_FIELD_POSTFIX, $block; ?>"
        default="<?php echo $default->get_avoid_try_limit(); ?>"
        value="<?php echo $obj->get_avoid_try_limit(); ?>"
        size="2"
        maxlength="3" />
        paragraphs
        <select style="margin-bottom: 3px;" name="<?php echo AI_OPTION_AVOID_DIRECTION, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_avoid_direction(); ?>">
          <option value="<?php echo AD_ABOVE; ?>" <?php echo ($obj->get_avoid_direction() == AD_ABOVE) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_ABOVE; ?></option>
          <option value="<?php echo AD_BELOW; ?>" <?php echo ($obj->get_avoid_direction() == AD_BELOW) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_BELOW; ?></option>
          <option value="<?php echo AD_ABOVE_AND_THEN_BELOW; ?>" <?php echo ($obj->get_avoid_direction() == AD_ABOVE_AND_THEN_BELOW) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_ABOVE_AND_THEN_BELOW; ?></option>
          <option value="<?php echo AD_BELOW_AND_THEN_ABOVE; ?>" <?php echo ($obj->get_avoid_direction() == AD_BELOW_AND_THEN_ABOVE) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_BELOW_AND_THEN_ABOVE; ?></option>
        </select>
      </span>
    </div>
  </div>

  <div class="responsive-table rounded" id="list-settings-<?php echo $block; ?>" style="<?php if (!$show_lists) echo ' display: none;'; ?>">
    <table>
      <tbody>
        <tr>
          <td>
            Categories
          </td>
          <td>
            <button id="category-button-<?php echo $block; ?>" type="button" class='ai-button' style="display: none; outline: transparent; float: right; margin-top: 1px; width: 15px; height: 15px;" title="Toggle category editor"></button>
          </td>
          <td style="padding-right: 7px; width: 65%;">
            <input id="category-list-<?php echo $block; ?>" class="ai-list-filter ai-list-custom" style="width: 100%;" title="Comma separated category slugs" type="text" name="<?php echo AI_OPTION_CATEGORY_LIST, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_ad_block_cat(); ?>" value="<?php echo $cat_list; ?>" size="54" maxlength="500" />
          </td>
          <td style="padding-right: 7px;">
            <input type="radio" name="<?php echo AI_OPTION_CATEGORY_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" id="category-blacklist-<?php echo $block; ?>" default="<?php echo $default->get_ad_block_cat_type() == AD_BLACK_LIST; ?>" value="<?php echo AD_BLACK_LIST; ?>" <?php if ($obj->get_ad_block_cat_type() == AD_BLACK_LIST) echo 'checked '; ?> />
            <label for="category-blacklist-<?php echo $block; ?>" title="Blacklist categories"><?php echo AD_BLACK_LIST; ?></label>
          </td>
          <td>
            <input type="radio" name="<?php echo AI_OPTION_CATEGORY_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" id="category-whitelist-<?php echo $block; ?>" default="<?php echo $default->get_ad_block_cat_type() == AD_WHITE_LIST; ?>" value="<?php echo AD_WHITE_LIST; ?>" <?php if ($obj->get_ad_block_cat_type() == AD_WHITE_LIST) echo 'checked '; ?> />
            <label for="category-whitelist-<?php echo $block; ?>" title="Whitelist categories"><?php echo AD_WHITE_LIST; ?></label>
          </td>
        </tr>
        <tr>
          <td colspan="5">
            <select id="category-select-<?php echo $block; ?>" multiple="multiple" style="display: none;">
            </select>
          </td>
        </tr>

        <tr>
          <td>
            Tags
          </td>
          <td>
            <button id="tag-button-<?php echo $block; ?>" type="button" class='ai-button' style="display: none; outline: transparent; float: right; margin-top: 1px; width: 15px; height: 15px;" title="Toggle tag editor"></button>
          </td>
          <td style="padding-right: 7px;">
            <input id="tag-list-<?php echo $block; ?>" class="ai-list-filter ai-list-custom" style="width: 100%;" title="Comma separated tag slugs" type="text" name="<?php echo AI_OPTION_TAG_LIST, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_ad_block_tag(); ?>" value="<?php echo $tag_list; ?>" size="54" maxlength="500"/>
          </td>
          <td style="padding-right: 7px;">
            <input type="radio" name="<?php echo AI_OPTION_TAG_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" id="tag-blacklist-<?php echo $block; ?>" default="<?php echo $default->get_ad_block_tag_type() == AD_BLACK_LIST; ?>" value="<?php echo AD_BLACK_LIST; ?>" <?php if ($obj->get_ad_block_tag_type() == AD_BLACK_LIST) echo 'checked '; ?> />
            <label for="tag-blacklist-<?php echo $block; ?>" title="Blacklist tags"><?php echo AD_BLACK_LIST; ?></label>
          </td>
          <td>
            <input type="radio" name="<?php echo AI_OPTION_TAG_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" id="tag-whitelist-<?php echo $block; ?>" default="<?php echo $default->get_ad_block_tag_type() == AD_WHITE_LIST; ?>" value="<?php echo AD_WHITE_LIST; ?>" <?php if ($obj->get_ad_block_tag_type() == AD_WHITE_LIST) echo 'checked '; ?> />
            <label for="tag-whitelist-<?php echo $block; ?>" title="Whitelist tags"><?php echo AD_WHITE_LIST; ?></label>
          </td>
        </tr>
        <tr>
          <td colspan="5">
            <select id="tag-select-<?php echo $block; ?>" multiple="multiple" style="display: none;">
            </select>
          </td>
        </tr>

        <tr>
          <td>
            Taxonomies
          </td>
          <td>
            <button id="taxonomy-button-<?php echo $block; ?>" type="button" class='ai-button' style="display: none; outline: transparent; float: right; margin-top: 1px; width: 15px; height: 15px;" title="Toggle taxonomy editor"></button>
          </td>
          <td style="padding-right: 7px;">
            <input id="taxonomy-list-<?php echo $block; ?>" class="ai-list-custom" style="width: 100%;" title="Comma separated slugs: taxonomy, term or taxonomy:term" type="text" name="<?php echo AI_OPTION_TAXONOMY_LIST, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_ad_block_taxonomy(); ?>" value="<?php echo $taxonomy_list; ?>" size="54" maxlength="500" />
          </td>
          <td style="padding-right: 7px;">
            <input type="radio" name="<?php echo AI_OPTION_TAXONOMY_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" id="taxonomy-blacklist-<?php echo $block; ?>" default="<?php echo $default->get_ad_block_taxonomy_type() == AD_BLACK_LIST; ?>" value="<?php echo AD_BLACK_LIST; ?>" <?php if ($obj->get_ad_block_taxonomy_type() == AD_BLACK_LIST) echo 'checked '; ?> />
            <label for="category-blacklist-<?php echo $block; ?>" title="Blacklist taxonomies"><?php echo AD_BLACK_LIST; ?></label>
          </td>
          <td>
            <input type="radio" name="<?php echo AI_OPTION_TAXONOMY_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" id="taxonomy-whitelist-<?php echo $block; ?>" default="<?php echo $default->get_ad_block_taxonomy_type() == AD_WHITE_LIST; ?>" value="<?php echo AD_WHITE_LIST; ?>" <?php if ($obj->get_ad_block_taxonomy_type() == AD_WHITE_LIST) echo 'checked '; ?> />
            <label for="category-whitelist-<?php echo $block; ?>" title="Whitelist taxonomies"><?php echo AD_WHITE_LIST; ?></label>
          </td>
        </tr>
        <tr>
          <td colspan="5">
            <select id="taxonomy-select-<?php echo $block; ?>" multiple="multiple" style="display: none;">
            </select>
          </td>
        </tr>

        <tr>
          <td>
            Post IDs
          </td>
          <td>
            <button id="id-button-<?php echo $block; ?>" type="button" class='ai-button' style="display: none; outline: transparent; float: right; margin-top: 1px; width: 15px; height: 15px;" title="Toggle post/page ID editor"></button>
          </td>
          <td style="padding-right: 7px;">
            <input id="id-list-<?php echo $block; ?>" class="ai-list-custom" style="width: 100%;" title="Comma separated post/page IDs" type="text" name="<?php echo AI_OPTION_ID_LIST, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_id_list(); ?>" value="<?php echo $id_list; ?>" size="54" maxlength="500"/>
          </td>
          <td style="padding-right: 7px;">
            <input type="radio" name="<?php echo AI_OPTION_ID_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" id="id-blacklist-<?php echo $block; ?>" default="<?php echo $default->get_id_list_type() == AD_BLACK_LIST; ?>" value="<?php echo AD_BLACK_LIST; ?>" <?php if ($obj->get_id_list_type() == AD_BLACK_LIST) echo 'checked '; ?> />
            <label for="id-blacklist-<?php echo $block; ?>" title="Blacklist IDs"><?php echo AD_BLACK_LIST; ?></label>
          </td>
          <td>
            <input type="radio" name="<?php echo AI_OPTION_ID_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" id="id-whitelist-<?php echo $block; ?>" default="<?php echo $default->get_id_list_type() == AD_WHITE_LIST; ?>" value="<?php echo AD_WHITE_LIST; ?>" <?php if ($obj->get_id_list_type() == AD_WHITE_LIST) echo 'checked '; ?> />
            <label for="id-whitelist-<?php echo $block; ?>" title="Whitelist IDs"><?php echo AD_WHITE_LIST; ?></label>
          </td>
        </tr>
        <tr>
          <td colspan="5">
            <select id="id-select-<?php echo $block; ?>" multiple="multiple" style="display: none;">
            </select>
          </td>
        </tr>

        <tr>
          <td>
            Urls
          </td>
          <td>
            <button id="url-button-<?php echo $block; ?>" type="button" class='ai-button' style="display: none; outline: transparent; float: right; margin-top: 1px; width: 15px; height: 15px;" title="Toggle url editor"></button>
          </td>
          <td style="padding-right: 7px;">
            <input id="url-list-<?php echo $block; ?>" class="ai-list-space ai-clean-protocol ai-clean-domain" style="width: 100%;" type="text" name="<?php echo AI_OPTION_URL_LIST, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_ad_url_list(); ?>" value="<?php echo $url_list; ?>" size="54" maxlength="500" title="Comma separated urls (page addresses) starting with / after domain name (e.g. /permalink-url, use only when you need to taget a specific url not accessible by other means). You can also use partial urls with * (/url-start*. *url-pattern*, *url-end)" />
          </td>
          <td style="padding-right: 7px;">
            <input type="radio" name="<?php echo AI_OPTION_URL_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" id="url-blacklist-<?php echo $block; ?>" default="<?php echo $default->get_ad_url_list_type() == AD_BLACK_LIST; ?>" value="<?php echo AD_BLACK_LIST; ?>" <?php if ($obj->get_ad_url_list_type() == AD_BLACK_LIST) echo 'checked '; ?> />
            <label for="url-blacklist-<?php echo $block; ?>" title="Blacklist urls"><?php echo AD_BLACK_LIST; ?></label>
          </td>
          <td>
            <input type="radio" name="<?php echo AI_OPTION_URL_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" id="url-whitelist-<?php echo $block; ?>" default="<?php echo $default->get_ad_url_list_type() == AD_WHITE_LIST; ?>" value="<?php echo AD_WHITE_LIST; ?>" <?php if ($obj->get_ad_url_list_type() == AD_WHITE_LIST) echo 'checked '; ?> />
            <label for="url-whitelist-<?php echo $block; ?>" title="Whitelist urls"><?php echo AD_WHITE_LIST; ?></label>
          </td>
        </tr>
        <tr>
          <td colspan="5">
            <textarea id="url-editor-<?php echo $block; ?>" style="width: 100%; height: 220px; font-family: Courier, 'Courier New', monospace; font-weight: bold; display: none;"></textarea>
          </td>
        </tr>

        <tr>
          <td>
            Url parameters
            &nbsp;
          </td>
          <td>
            <button id="url-parameter-button-<?php echo $block; ?>" type="button" class='ai-button' style="display: none; outline: transparent; float: right; margin-top: 1px; width: 15px; height: 15px;" title="Toggle url parameter editor"></button>
          </td>
          <td style="padding-right: 7px;">
            <input id="url-parameter-list-<?php echo $block; ?>" style="width: 100%;" title="Comma separated url query parameters or cookies with optional values (use 'prameter', 'prameter=value', 'cookie' or 'cookie=value')" type="text" name="<?php echo AI_OPTION_URL_PARAMETER_LIST, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_url_parameter_list(); ?>" value="<?php echo $url_parameter_list; ?>" size="54" maxlength="500"/>
          </td>
          <td style="padding-right: 7px;">
            <input type="radio" name="<?php echo AI_OPTION_URL_PARAMETER_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" id="url-parameter-blacklist-<?php echo $block; ?>" default="<?php echo $default->get_url_parameter_list_type() == AD_BLACK_LIST; ?>" value="<?php echo AD_BLACK_LIST; ?>" <?php if ($obj->get_url_parameter_list_type() == AD_BLACK_LIST) echo 'checked '; ?> />
            <label for="url-parameter-blacklist-<?php echo $block; ?>" title="Blacklist url parameters"><?php echo AD_BLACK_LIST; ?></label>
          </td>
          <td>
            <input type="radio" name="<?php echo AI_OPTION_URL_PARAMETER_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" id="url-parameter-whitelist-<?php echo $block; ?>" default="<?php echo $default->get_url_parameter_list_type() == AD_WHITE_LIST; ?>" value="<?php echo AD_WHITE_LIST; ?>" <?php if ($obj->get_url_parameter_list_type() == AD_WHITE_LIST) echo 'checked '; ?> />
            <label for="url-parameter-whitelist-<?php echo $block; ?>" title="Whitelist url parameters"><?php echo AD_WHITE_LIST; ?></label>
          </td>
        </tr>
        <tr>
          <td colspan="5">
            <textarea id="url-parameter-editor-<?php echo $block; ?>" style="width: 100%; height: 220px; font-family: Courier, 'Courier New', monospace; font-weight: bold; display: none;"></textarea>
          </td>
        </tr>

        <tr>
          <td>
            Referers
          </td>
          <td>
            <button id="referer-button-<?php echo $block; ?>" type="button" class='ai-button' style="display: none; outline: transparent; float: right; margin-top: 1px; width: 15px; height: 15px;" title="Toggle referer editor"></button>
          </td>
          <td style="padding-right: 7px;">
            <input id="referer-list-<?php echo $block; ?>" class="ai-clean-protocol ai-only-domain ai-list-sort" style="width: 100%;" title="Comma separated domains, use # for no referer" type="text" name="<?php echo AI_OPTION_DOMAIN_LIST, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_ad_domain_list(); ?>" value="<?php echo $domain_list; ?>" size="54" maxlength="500"/>
          </td>
          <td style="padding-right: 7px;">
            <input type="radio" name="<?php echo AI_OPTION_DOMAIN_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" id="referer-blacklist-<?php echo $block; ?>" default="<?php echo $default->get_ad_domain_list_type() == AD_BLACK_LIST; ?>" value="<?php echo AD_BLACK_LIST; ?>" <?php if ($obj->get_ad_domain_list_type() == AD_BLACK_LIST) echo 'checked '; ?> />
            <label for="referer-blacklist-<?php echo $block; ?>" title="Blacklist referers"><?php echo AD_BLACK_LIST; ?></label>
          </td>
          <td>
            <input type="radio" name="<?php echo AI_OPTION_DOMAIN_LIST_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" id="referer-whitelist-<?php echo $block; ?>" default="<?php echo $default->get_ad_domain_list_type() == AD_WHITE_LIST; ?>" value="<?php echo AD_WHITE_LIST; ?>" <?php if ($obj->get_ad_domain_list_type() == AD_WHITE_LIST) echo 'checked '; ?> />
            <label for="referer-whitelist-<?php echo $block; ?>" title="Whitelist referers"><?php echo AD_WHITE_LIST; ?></label>
          </td>
        </tr>
        <tr>
          <td colspan="5">
            <textarea id="referer-editor-<?php echo $block; ?>" style="width: 100%; height: 220px; font-family: Courier, 'Courier New', monospace; font-weight: bold; display: none;"></textarea>
          </td>
        </tr>

<?php if (function_exists ('ai_list_rows')) ai_list_rows ($block, $default, $obj); ?>
      </tbody>
    </table>
  </div>

  <div id="manual-settings-<?php echo $block; ?>" class="small-button rounded" style="text-align: left;<?php if (!$show_manual) echo ' display: none;'; ?>">
    <table>
      <tr>
        <td style="padding: 4px 10px 4px 0;">
          <input type="hidden" name="<?php echo AI_OPTION_ENABLE_WIDGET, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
          <input id="enable-widget-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_ENABLE_WIDGET, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_enable_widget(); ?>" <?php if ($obj->get_enable_widget () == AI_ENABLED) echo 'checked '; ?> />
          <label for="enable-widget-<?php echo $block; ?>" title="Enable or disable widget for this code block">
            Widget
          </label>
        </td>
        <td>
          <pre class="ai-sidebars" style= "margin: 0; display: inline; color: blue; white-space: pre-wrap; word-wrap: break-word;" title="Sidebars (or widget positions) where this widged is used"><?php echo $sidebars [$block], !empty ($sidebars [$block]) ? " &nbsp;" : ""; ?></pre>
        </td>
      </tr>
      <tr>
        <td style="padding: 4px 10px 4px 0;">
          <input type="hidden"   name="<?php echo AI_OPTION_ENABLE_MANUAL, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
          <input type="checkbox" id="enable-shortcode-<?php echo $block; ?>" name="<?php echo AI_OPTION_ENABLE_MANUAL, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_enable_manual(); ?>" <?php if ($obj->get_enable_manual () == AI_ENABLED) echo 'checked '; ?> />
          <label for="enable-shortcode-<?php echo $block; ?>" title="Enable or disable shortcode for manual insertion of this code block in posts and pages">
            Shortcode
          </label>
        </td>
        <td>
          <pre class="select ai-block-number" style="margin: 0 5px 0 0; display: inline; color: blue; font-size: 11px; white-space: pre-wrap; word-wrap: break-word;">[adinserter block="<?php echo $block; ?>"]</pre>
          <div class="copy-blocker"></div>
          <span class="copy-blocker">or</span>
          <pre class="select ai-block-name" style="margin: 0 0 0 20px; display: inline; color: blue; white-space: pre-wrap; word-wrap: break-word;">[adinserter name="<?php echo $obj->get_ad_name(); ?>"]</pre>
          <div class="copy-blocker"></div>
        </td>
      </tr>
      <tr>
        <td style="padding: 4px 10px 4px 0;">
          <input type="hidden" name="<?php echo AI_OPTION_ENABLE_PHP_CALL, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
          <input id="enable-php-call-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_ENABLE_PHP_CALL, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_enable_php_call(); ?>" <?php if ($manual_php_function [$block] == AI_ENABLED) echo 'checked '; ?> />
          <label for="enable-php-call-<?php echo $block; ?>" title="Enable or disable PHP function call to insert this code block at any position in template file. If function is disabled for block it will return empty string.">
            PHP function
          </label>
        </td>
        <td class="select">
          <pre class="ai-block-number" style="margin: 0; display: inline; color: blue; font-size: 11px; white-space: pre-wrap; word-wrap: break-word;">&lt;?php if (function_exists ('adinserter')) echo adinserter (<?php echo $block; ?>); ?&gt;</pre>
          <div class="copy-blocker"></div>
        </td>
      </tr>
    </table>
  </div>

  <div id="device-detection-settings-<?php echo $block; ?>" style="<?php if (!$show_devices) echo 'display: none;'; ?>">

    <div id="ai-devices-container-<?php echo $block; ?>" style="padding: 0; margin 8px 0 0 0; border: 0;">
      <ul id="ai-devices-tabs-<?php echo $block; ?>" style="display: none;">
        <li id="ai-client-side-detection-<?php echo $block; ?>"><a href="#tab-client-side-<?php echo $block; ?>"><span style="<?php echo $client_side_style; ?>">Client-side device detection</span></a></li>
        <li id="ai-server-side-detection<?php echo $block; ?>"><a href="#tab-server-side-<?php echo $block; ?>"><span style="<?php echo $server_side_style; ?>">Server-side device detection</span></a></li>
      </ul>

      <div id="tab-client-side-<?php echo $block; ?>" class="rounded">
        <div style="float: left; margin-top: 0px;">
          <input type="hidden" name="<?php echo AI_OPTION_DETECT_CLIENT_SIDE, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
          <input id="client-side-detection-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_DETECT_CLIENT_SIDE, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_detection_client_side(); ?>" <?php if ($obj->get_detection_client_side ()==AI_ENABLED) echo 'checked '; ?> />
          <label for="client-side-detection-<?php echo $block; ?>" style="vertical-align: baseline;">Use client-side detection to </label>

          <select id="client-side-action-<?php echo $block; ?>" name="<?php echo AI_OPTION_CLIENT_SIDE_ACTION, WP_FORM_FIELD_POSTFIX, $block; ?>" style="margin: -4px 1px -2px 1px;" default="<?php echo $default->get_client_side_action (); ?>" title="Either show/hide or inseret when the page is loaded on wanted viewports">
            <option value="<?php echo AI_CLIENT_SIDE_ACTION_SHOW; ?>" <?php echo ($obj->get_client_side_action () == AI_CLIENT_SIDE_ACTION_SHOW) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo strtolower (AI_TEXT_SHOW); ?></option>
            <option value="<?php echo AI_CLIENT_SIDE_ACTION_INSERT; ?>" <?php echo ($obj->get_client_side_action () == AI_CLIENT_SIDE_ACTION_INSERT) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo strtolower (AI_TEXT_INSERT); ?></option>
          </select>

          <label style="vertical-align: baseline;"> only on</label>
        </div>

        <div style="float: left; margin: -5px 0 -2px 0;">
<?php

      $viewports = array ();
      for ($viewport = 1; $viewport <= AD_INSERTER_VIEWPORTS; $viewport ++) {
        $viewport_name = get_viewport_name ($viewport);
        if ($viewport_name != '') $viewports [$viewport] = $viewport_name;
      }
      $number_of_viewports = count ($viewports);
      $columns = 3;

?>
          <table>
            <tbody>
<?php

      $column = 0;
      foreach ($viewports as $viewport => $viewport_name) {
        if ($column % $columns == 0) {
?>
              <tr>
<?php
        }
?>
                <td style='padding: 2px 0 0 20px;'>
                  <input type="hidden" name="<?php echo AI_OPTION_DETECT_VIEWPORT, '_', $viewport, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
                  <input type="checkbox" name="<?php echo AI_OPTION_DETECT_VIEWPORT, '_', $viewport, WP_FORM_FIELD_POSTFIX, $block; ?>" id="viewport-<?php echo $viewport, "-", $block; ?>" value="1" default="<?php echo $default->get_detection_viewport ($viewport); ?>" <?php if ($obj->get_detection_viewport ($viewport)==AI_ENABLED) echo 'checked '; ?> />
                  <label for="viewport-<?php echo $viewport, "-", $block; ?>" title="Device min width <?php echo get_viewport_width ($viewport); ?> px"><?php echo $viewport_name; ?></label>
                </td>
<?php
        $column ++;
      }
      if ($column % $columns != 0) {
        for ($fill = 1; $fill <= $columns - $column % $columns; $fill++) {
?>
                <td> </td>
<?php
        }
?>
              </tr>
<?php
      }
?>
            </tbody>
          </table>
        </div>
        <div style="clear: both"></div>
      </div>

      <div id="tab-server-side-<?php echo $block; ?>" class="rounded">
        <input type="hidden" name="<?php echo AI_OPTION_DETECT_SERVER_SIDE, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
        <input type="checkbox" name="<?php echo AI_OPTION_DETECT_SERVER_SIDE, WP_FORM_FIELD_POSTFIX, $block; ?>" id="server-side-detection-<?php echo $block; ?>" value="1" default="<?php echo $default->get_detection_server_side(); ?>" <?php if ($obj->get_detection_server_side ()==AI_ENABLED) echo 'checked '; ?> />
        <label for="server-side-detection-<?php echo $block; ?>" style="vertical-align: baseline;">Use server-side detection to insert code only for </label>

          <select id="display-for-devices-<?php echo $block; ?>" name="<?php echo AI_OPTION_DISPLAY_FOR_DEVICES, WP_FORM_FIELD_POSTFIX, $block; ?>" style="margin: -4px 1px -2px 1px;" default="<?php echo $default->get_display_for_devices(); ?>">
            <option value="<?php echo AD_DISPLAY_DESKTOP_DEVICES; ?>" <?php echo ($obj->get_display_for_devices() == AD_DISPLAY_DESKTOP_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_DESKTOP_DEVICES; ?></option>
            <option value="<?php echo AD_DISPLAY_MOBILE_DEVICES; ?>" <?php echo ($obj->get_display_for_devices() == AD_DISPLAY_MOBILE_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_MOBILE_DEVICES; ?></option>
            <option value="<?php echo AD_DISPLAY_TABLET_DEVICES; ?>" <?php echo ($obj->get_display_for_devices() == AD_DISPLAY_TABLET_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_TABLET_DEVICES; ?></option>
            <option value="<?php echo AD_DISPLAY_PHONE_DEVICES; ?>" <?php echo ($obj->get_display_for_devices() == AD_DISPLAY_PHONE_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_PHONE_DEVICES; ?></option>
            <option value="<?php echo AD_DISPLAY_DESKTOP_TABLET_DEVICES; ?>" <?php echo ($obj->get_display_for_devices() == AD_DISPLAY_DESKTOP_TABLET_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_DESKTOP_TABLET_DEVICES; ?></option>
            <option value="<?php echo AD_DISPLAY_DESKTOP_PHONE_DEVICES; ?>" <?php echo ($obj->get_display_for_devices() == AD_DISPLAY_DESKTOP_PHONE_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_DESKTOP_PHONE_DEVICES; ?></option>
          </select>
          devices
      </div>
    </div>

  </div>

  <div id="misc-settings-<?php echo $block; ?>" style="<?php if (!$show_misc) echo 'display: none;'; ?>">
    <div id="ai-misc-container-<?php echo $block; ?>" style="padding: 0; margin 8px 0 0 0; border: 0;">
      <ul id="ai-misc-tabs-<?php echo $block; ?>" style="display: none;">
        <li id="ai-misc-insertion-<?php echo $block; ?>"><a href="#tab-insertion-<?php echo $block; ?>"><span style="<?php echo $insertion_style; ?>">Insertion</span></a></li>
        <li id="ai-misc-filter-<?php echo $block; ?>"><a href="#tab-filter-<?php echo $block; ?>"><span style="<?php echo $filter_style; ?>">Filter</span></a></li>
        <li id="ai-misc-word-count-<?php echo $block; ?>"><a href="#tab-word-count-<?php echo $block; ?>"><span style="<?php echo $word_count_style; ?>">Word Count</span></a></li>
        <li id="ai-misc-scheduling-<?php echo $block; ?>"><a href="#tab-scheduling-<?php echo $block; ?>"><span style="<?php echo $scheduling_style; ?>">Scheduling</span></a></li>
        <li id="ai-misc-display-<?php echo $block; ?>"><a href="#tab-display-<?php echo $block; ?>"><span style="<?php echo $display_style; ?>">Display</span></a></li>
        <?php if (function_exists ('ai_adb_action_0')) ai_adb_action_0 ($block, $adb_style); ?>
        <li id="ai-misc-general-<?php echo $block; ?>"><a href="#tab-general-<?php echo $block; ?>"><span style="<?php echo $general_style; ?>">General</span></a></li>
      </ul>

      <div id="tab-insertion-<?php echo $block; ?>" class="max-input" style="padding: 0;">
        <div class="rounded">
          <table class="responsive-table" style="width: 70%">
            <tbody>
              <tr>
                <td>
                  <input type="hidden" name="<?php echo AI_OPTION_ENABLE_AMP, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
                  <input style="" id="enable-amp-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_ENABLE_AMP, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_enable_amp(true); ?>" <?php if ($obj->get_enable_amp (true) == AI_ENABLED) echo 'checked '; ?> />
                  <label for="enable-amp-<?php echo $block; ?>" style="<?php if (!$obj->get_enable_amp (true) && $obj->get_enable_amp ()) echo ' color: red;' ?>" title="<?php if (!$obj->get_enable_amp (true) && $obj->get_enable_amp ()) echo "Old settings for AMP pages detected. " ?>To insert different codes on normal and AMP pages separate them with [ADINSERTER AMP] separator. Here you can enable insertion on AMP pages only when you need to insert THE SAME CODE also on AMP pages (no AMP separator).">AMP pages</label>
                </td>
                <td>
                  <input type="hidden" name="<?php echo AI_OPTION_ENABLE_AJAX, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
                  <input style="margin-left: 10px;" id="enable-ajax-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_ENABLE_AJAX, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_enable_ajax(); ?>" <?php if ($obj->get_enable_ajax () == AI_ENABLED) echo 'checked '; ?> />
                  <label for="enable-ajax-<?php echo $block; ?>" title="Enable or disable insertion for Ajax requests">Ajax requests</label>
                </td>
                <td>
                  <input type="hidden" name="<?php echo AI_OPTION_ENABLE_FEED, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
                  <input style="margin-left: 10px;" id="enable-feed-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_ENABLE_FEED, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_enable_feed(); ?>" <?php if ($obj->get_enable_feed () == AI_ENABLED) echo 'checked '; ?> />
                  <label for="enable-feed-<?php echo $block; ?>" title="Enable or disable insertion in RSS feeds">RSS Feed</label>
                </td>
                <td>
                  <input type="hidden" name="<?php echo AI_OPTION_ENABLE_404, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
                  <input style="margin-left: 10px;" id="enable-404-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_ENABLE_404, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_enable_404(); ?>" <?php if ($obj->get_enable_404 () == AI_ENABLED) echo 'checked '; ?> />
                  <label for="enable-404-<?php echo $block; ?>" title="Enable or disable insertion on page for Error 404: Page not found">Error 404 page</label>
                </td>
                <td>
              </tr>
            </tbody>
          </table>

        </div>
        <div class="rounded">

          <table class="responsive-table" style="width: 100%">
            <tbody>
              <tr>
                <td>
                  Insert for
                  <select id="display-for-users-<?php echo $block; ?>" style="margin: 0 1px; width:160px" name="<?php echo AI_OPTION_DISPLAY_FOR_USERS, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_display_for_users(); ?>">
                     <option value="<?php echo AD_DISPLAY_ALL_USERS; ?>" <?php echo ($obj->get_display_for_users()==AD_DISPLAY_ALL_USERS) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_ALL_USERS; ?></option>
                     <option value="<?php echo AD_DISPLAY_LOGGED_IN_USERS; ?>" <?php echo ($obj->get_display_for_users()==AD_DISPLAY_LOGGED_IN_USERS) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_LOGGED_IN_USERS; ?></option>
                     <option value="<?php echo AD_DISPLAY_NOT_LOGGED_IN_USERS; ?>" <?php echo ($obj->get_display_for_users()==AD_DISPLAY_NOT_LOGGED_IN_USERS) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_NOT_LOGGED_IN_USERS; ?></option>
                     <option value="<?php echo AD_DISPLAY_ADMINISTRATORS; ?>" <?php echo ($obj->get_display_for_users()==AD_DISPLAY_ADMINISTRATORS) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_ADMINISTRATORS; ?></option>
                  </select>
                </td>
                <td>
                  Max <input type="text" style="width: 32px;" name="<?php echo AI_OPTION_MAXIMUM_INSERTIONS, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_maximum_insertions (); ?>" value="<?php echo $obj->get_maximum_insertions (); ?>" size="1" maxlength="3" title="Empty or 0 means no limit" /> insertions
                </td>
                <td style="width: 50%">
                  <span style="float: right;">
                    <input type="hidden" name="<?php echo AI_OPTION_DISABLE_CACHING, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
                    <input id="disable-caching-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_DISABLE_CACHING, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_disable_caching (); ?>" <?php if ($obj->get_disable_caching () == AI_ENABLED) echo 'checked '; ?> />
                    <label for="disable-caching-<?php echo $block; ?>" title="Disable caching for WP Super Cache, W3 Total Cache and WP Rocket plugins">Disable caching</label>
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div id="tab-filter-<?php echo $block; ?>" class="rounded">
        <div class="max-input">
          <span style="display: table-cell;">
            Filter insertions
          </span>
          <span style="display: table-cell;">
            <input style="width: 100%; padding-right: 10px;" type="text" name="<?php echo AI_OPTION_EXCERPT_NUMBER, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_call_filter(); ?>" value="<?php echo $obj->get_call_filter(); ?>" title= "Filter insertions by specifying wanted calls for this block - single number, comma separated numbers or %N for every N insertions - empty means all insertions / no filter. Set Counter for filter to Auto if you are using only one insertion type." size="12" maxlength="36" />
          </span>
          <span style="display: table-cell;">
            &nbsp;&nbsp;&nbsp;using
            <select id="filter-type-<?php echo $block; ?>" style="width:160px; margin: 0 1px;" name="<?php echo AI_OPTION_FILTER_TYPE, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_filter_type(); ?>">
               <option value="<?php echo AI_FILTER_AUTO; ?>" <?php echo ($filter_type == AI_FILTER_AUTO) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_AUTO; ?></option>
               <option value="<?php echo AI_FILTER_PHP_FUNCTION_CALLS; ?>" <?php echo ($filter_type == AI_FILTER_PHP_FUNCTION_CALLS) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_PHP_FUNCTION_CALLS; ?></option>
               <option value="<?php echo AI_FILTER_CONTENT_PROCESSING; ?>" <?php echo ($filter_type == AI_FILTER_CONTENT_PROCESSING) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_CONTENT_PROCESSING; ?></option>
               <option value="<?php echo AI_FILTER_EXCERPT_PROCESSING; ?>" <?php echo ($filter_type == AI_FILTER_EXCERPT_PROCESSING) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_EXCERPT_PROCESSING; ?></option>
               <option value="<?php echo AI_FILTER_BEFORE_POST_PROCESSING; ?>" <?php echo ($filter_type == AI_FILTER_BEFORE_POST_PROCESSING) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_BEFORE_POST_PROCESSING; ?></option>
               <option value="<?php echo AI_FILTER_AFTER_POST_PROCESSING; ?>" <?php echo ($filter_type == AI_FILTER_AFTER_POST_PROCESSING) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_AFTER_POST_PROCESSING; ?></option>
               <option value="<?php echo AI_FILTER_WIDGET_DRAWING; ?>" <?php echo ($filter_type == AI_FILTER_WIDGET_DRAWING) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_WIDGET_DRAWING; ?></option>
               <option value="<?php echo AI_FILTER_SUBPAGES; ?>" <?php echo ($filter_type == AI_FILTER_SUBPAGES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_SUBPAGES; ?></option>
               <option value="<?php echo AI_FILTER_POSTS; ?>" <?php echo ($filter_type == AI_FILTER_POSTS) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_POSTS; ?></option>
               <option value="<?php echo AI_FILTER_PARAGRAPHS; ?>" <?php echo ($filter_type == AI_FILTER_PARAGRAPHS) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_PARAGRAPHS; ?></option>
               <option value="<?php echo AI_FILTER_COMMENTS; ?>" <?php echo ($filter_type == AI_FILTER_COMMENTS) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_COMMENTS; ?></option>
            </select>
            counter
          </span>
          <span style="display: table-cell;">
            <input type="hidden" name="<?php echo AI_OPTION_INVERTED_FILTER, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
            <input style="margin-left: 10px;" type="checkbox" name="<?php echo AI_OPTION_INVERTED_FILTER, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_inverted_filter(); ?>" <?php if ($obj->get_inverted_filter () == AI_ENABLED) echo 'checked '; ?> />
            <label for="enable-ajax-<?php echo $block; ?>" style="vertical-align: top;" title="If checked specified calls are unwanted">Invert filter</label>
          </span>
        </div>
      </div>

      <div id="tab-word-count-<?php echo $block; ?>" class="rounded">
        Post/Static page must have between
        <input type="text" name="<?php echo AI_OPTION_MIN_WORDS, WP_FORM_FIELD_POSTFIX, $block; ?>" style="margin: 0 1px;" default="<?php echo $default->get_minimum_words(); ?>" value="<?php echo $obj->get_minimum_words() ?>" title="Minimum number of post/static page words, leave empty for no limit" size="4" maxlength="6" />
        and
        <input type="text" name="<?php echo AI_OPTION_MAX_WORDS, WP_FORM_FIELD_POSTFIX, $block; ?>" style="margin: 0 1px;" default="<?php echo $default->get_maximum_words(); ?>" value="<?php echo $obj->get_maximum_words() ?>" title="Maximum number of post/static page words, leave empty for no limit" size="4" maxlength="6" />
        words
      </div>

      <div id="tab-scheduling-<?php echo $block; ?>" class="rounded" style="min-height: 24px;">
        <select id="scheduling-<?php echo $block; ?>" style="margin: 0 1px;" name="<?php echo AI_OPTION_SCHEDULING, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_scheduling(); ?>">
          <option value="<?php echo AI_SCHEDULING_OFF; ?>" <?php echo ($obj->get_scheduling() == AI_SCHEDULING_OFF) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_OFF; ?></option>
          <option value="<?php echo AI_SCHEDULING_DELAY; ?>" <?php echo ($obj->get_scheduling() == AI_SCHEDULING_DELAY) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_DELAY_INSERTION; ?></option>
<?php if (function_exists ('ai_scheduling_options')) ai_scheduling_options ($obj); ?>
        </select>

        <span id="scheduling-delay-<?php echo $block; ?>">
          for <input type="text" name="<?php echo AI_OPTION_AFTER_DAYS, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_ad_after_day(); ?>" value="<?php echo $obj->get_ad_after_day(); ?>" size="2" maxlength="3" /> days after publishing
        </span>
        <span id="scheduling-delay-warning-<?php echo $block; ?>" style="color: #d00; display: none;">&nbsp;&nbsp; Not available</span>

<?php if (function_exists ('ai_scheduling_data')) ai_scheduling_data ($block, $obj, $default); ?>
      </div>

      <div id="tab-display-<?php echo $block; ?>" class="rounded">

        <table class="responsive-table" style="width: 100%;" cellspacing=0 cellpadding=0 >
          <tbody>
            <tr>
              <td style="width: 10%;">
                <input type="hidden" name="<?php echo AI_OPTION_SHOW_LABEL, WP_FORM_FIELD_POSTFIX, $block; ?>" value="0" />
                <input style="" id="show-label-<?php echo $block; ?>" type="checkbox" name="<?php echo AI_OPTION_SHOW_LABEL, WP_FORM_FIELD_POSTFIX, $block; ?>" value="1" default="<?php echo $default->get_show_label (); ?>" <?php if ($obj->get_show_label () == AI_ENABLED) echo 'checked '; ?> />
                <label for="show-label-<?php echo $block; ?>">Ad label</label>
              </td>
              <td>
<?php if (function_exists ('ai_display_close')) ai_display_close ($block, $obj, $default, 'close-button-'.$block, AI_OPTION_CLOSE_BUTTON . WP_FORM_FIELD_POSTFIX . $block); ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

<?php if (function_exists ('ai_adb_action')) ai_adb_action ($block, $obj, $default); ?>

      <div id="tab-general-<?php echo $block; ?>" class="rounded">
        <div class="max-input">
          <span style="display: table-cell; width: 1px; white-space: nowrap;">
            General tag
            &nbsp;
          </span>
          <span style="display: table-cell;">
            <input style="width: 100%; max-width: 140px;" type="text" name="<?php echo AI_OPTION_GENERAL_TAG, WP_FORM_FIELD_POSTFIX, $block; ?>" default="<?php echo $default->get_ad_general_tag(); ?>" value="<?php echo $obj->get_ad_general_tag(); ?>" size="12" maxlength="40" title="Used for [adinserter data=''] shortcodes when no data is found" />
          </span>
        </div>
      </div>

    </div>
  </div>

  <div id="no-wrapping-warning-<?php echo $block; ?>" class="rounded" style="display: none;">
     <span style="margin-top: 5px;"><strong><span style="color: red;">WARNING:</span> No Wrapping</strong> style has no wrapping code needed for client-side device detection!</span>
  </div>

<?php if (function_exists ('ai_warnings')) ai_warnings ($block); ?>

  </div>
</div>
<?php
  }
?>
<div id="tab-0" style="padding: 0;<?php echo $tab_visible ? "" : " display: none;" ?>">
  <div style="margin: 16px 0 16px 4px;">
    <h3 style="margin: 0; float: left;"><?php echo AD_INSERTER_NAME ?> Settings <?php if (isset ($ai_db_options [AI_OPTION_GLOBAL]['VERSION'])) echo (int) ($ai_db_options [AI_OPTION_GLOBAL]['VERSION'][0].$ai_db_options [AI_OPTION_GLOBAL]['VERSION'][1]), '.',
                                        (int) ($ai_db_options [AI_OPTION_GLOBAL]['VERSION'][2].$ai_db_options [AI_OPTION_GLOBAL]['VERSION'][3]), '.',
                                        (int) ($ai_db_options [AI_OPTION_GLOBAL]['VERSION'][4].$ai_db_options [AI_OPTION_GLOBAL]['VERSION'][5]); ?></h3>
    <h4 style="margin: 0px; float: right;<?php if (defined ('AI_GENERATE_EXTRACT')) echo ' color: #00f;'; ?>" title="Settings timestamp"><?php echo isset ($ai_db_options [AI_OPTION_GLOBAL]['TIMESTAMP']) ? date ("Y-m-d H:i:s", $ai_db_options [AI_OPTION_GLOBAL]['TIMESTAMP'] + get_option ('gmt_offset') * 3600) : "";?></h4>
    <div style="clear: both;"></div>
  </div>

  <div style="margin: 16px 0;">
    <div style="float: right;">
<?php if (function_exists ('ai_settings_global_buttons')) ai_settings_global_buttons (); ?>
      <input style="display: none; font-weight: bold;" name="<?php echo AI_FORM_SAVE; ?>" value="Save Settings" type="submit" style="width:120px; font-weight: bold;" />
    </div>

    <div style="float: left;">
      <input onclick="if (confirm('Are you sure you want to reset all settings?')) return true; return false;" name="<?php echo AI_FORM_CLEAR; ?>" value="Reset All Settings" type="submit" style="display: none; width:125px; font-weight: bold; color: #e44;" />
<?php if (function_exists ('ai_settings_global_actions')) ai_settings_global_actions (); ?>
    </div>

    <div style="clear: both;"></div>
  </div>

<?php
  if (function_exists ('ai_global_settings')) ai_global_settings ();

  if ($enabled_k) $style_k = "font-weight: bold; color: #66f;"; else $style_k = "";
  if ($enabled_h) $style_h = "font-weight: bold; color: #66f;"; else if ($header_code_disabled) $style_h = "font-weight: bold; color: #f66;"; else $style_h = "";
  if ($enabled_f) $style_f = "font-weight: bold; color: #66f;"; else if ($footer_code_disabled) $style_f = "font-weight: bold; color: #f66;"; else $style_f = "";
  if (defined ('AI_ADBLOCKING_DETECTION') && AI_ADBLOCKING_DETECTION) {
    $adb_action = get_adb_action (true);
    if ($enabled_a) $style_a = "font-weight: bold; color: " . ($adb_action == AI_ADB_ACTION_NONE ? "#66f;" : "#c0f;"); else $style_a = "";
  }
  if (false) $style_d = "font-weight: bold; color: #e44;"; else $style_d = "";
?>

  <div id="ai-plugin-settings-tab-container" style="padding: 0; margin 8px 0 0 0; border: 0;">
    <ul id="ai-plugin-settings-tabs" style="display: none;">
      <li id="ai-g" class="ai-plugin-tab"><a href="#tab-general">General</a></li>
      <li id="ai-v" class="ai-plugin-tab"><a href="#tab-viewports">Viewports</a></li>
      <li id="ai-k" class="ai-plugin-tab"><a href="#tab-hooks"><span style="<?php echo $style_k ?>">Hooks</span></a></li>
      <li id="ai-h" class="ai-plugin-tab"><a href="#tab-header"><span style="<?php echo $style_h ?>">Header</span></a></li>
      <li id="ai-f" class="ai-plugin-tab"><a href="#tab-footer"><span style="<?php echo $style_f ?>">Footer</span></a></li>
<?php if (function_exists ('ai_plugin_settings_tab')) ai_plugin_settings_tab ($exceptions); ?>
<?php if (defined ('AI_ADBLOCKING_DETECTION') && AI_ADBLOCKING_DETECTION) { ?>
      <li id="ai-a" class="ai-plugin-tab"><a href="#tab-adblocking"><span style="<?php echo $style_a ?>">Ad Blocking</span></a></li>
<?php } ?>
      <li id="ai-d" class="ai-plugin-tab"><a href="#tab-debugging"><span style="<?php echo $style_d ?>">Debugging</span></a></li>
    </ul>

    <div id="tab-general" style="padding: 0;">

    <div class="rounded">
      <table class="ai-settings-table ai-values" style="width: 100%;">
<?php if (function_exists ('ai_general_settings')) ai_general_settings (); ?>
        <tr>
          <td>
          Plugin priority
          </td>
          <td>
            <input type="text" name="plugin_priority" value="<?php echo get_plugin_priority (); ?>"  default="<?php echo DEFAULT_PLUGIN_PRIORITY; ?>" size="6" maxlength="6" />
          </td>
        </tr>
        <tr>
          <td>
          Output buffering
          </td>
          <td>
            <select id="output-buffering" name="output-buffering"  default="<?php echo DEFAULT_OUTPUT_BUFFERING; ?>" title="Needed for position Above header but may not work with all themes">
              <option value="<?php echo AI_OUTPUT_BUFFERING_DISABLED; ?>" <?php echo get_output_buffering()  == AI_OUTPUT_BUFFERING_DISABLED ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_DISABLED; ?></option>
              <option value="<?php echo AI_OUTPUT_BUFFERING_ENABLED; ?>" <?php echo get_output_buffering() == AI_OUTPUT_BUFFERING_ENABLED ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_ENABLED; ?></option>
            </select>
          </td>
        </tr>
        <tr>
          <td>
            Syntax highlighting theme
          </td>
          <td>
            <select
                id="syntax-highlighter-theme"
                name="syntax-highlighter-theme"
                value="Value">
                <optgroup label="None">
                    <option value="<?php echo AI_OPTION_DISABLED; ?>" <?php echo ($syntax_highlighter_theme == AI_OPTION_DISABLED) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>No Syntax Highlighting</option>
                </optgroup>
                <optgroup label="Light">
                    <option value="chrome" <?php echo ($syntax_highlighter_theme == 'chrome') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Chrome</option>
                    <option value="clouds" <?php echo ($syntax_highlighter_theme == 'clouds') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Clouds</option>
                    <option value="crimson_editor" <?php echo ($syntax_highlighter_theme == 'crimson_editor') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Crimson Editor</option>
                    <option value="dawn" <?php echo ($syntax_highlighter_theme == 'dawn') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Dawn</option>
                    <option value="dreamweaver" <?php echo ($syntax_highlighter_theme == 'dreamweaver') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Dreamweaver</option>
                    <option value="eclipse" <?php echo ($syntax_highlighter_theme == 'eclipse') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Eclipse</option>
                    <option value="github" <?php echo ($syntax_highlighter_theme == 'github') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>GitHub</option>
                    <option value="katzenmilch" <?php echo ($syntax_highlighter_theme == 'katzenmilch') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Katzenmilch</option>
                    <option value="kuroir" <?php echo ($syntax_highlighter_theme == 'kuroir') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Kuroir</option>
                    <option value="solarized_light" <?php echo ($syntax_highlighter_theme == 'solarized_light') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Solarized Light</option>
                    <option value="textmate" <?php echo ($syntax_highlighter_theme == 'textmate') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Textmate</option>
                    <option value="tomorrow" <?php echo ($syntax_highlighter_theme == 'tomorrow') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Tomorrow</option>
                    <option value="xcode" <?php echo ($syntax_highlighter_theme == 'xcode') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>XCode</option>
                </optgroup>
                <optgroup label="Dark">
                    <option value="ad_inserter" <?php echo ($syntax_highlighter_theme == 'ad_inserter') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Ad Inserter</option>
                    <option value="chaos" <?php echo ($syntax_highlighter_theme == 'chaos') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Chaos</option>
                    <option value="clouds_midnight" <?php echo ($syntax_highlighter_theme == 'clouds_midnight') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Clouds Midnight</option>
                    <option value="cobalt" <?php echo ($syntax_highlighter_theme == 'cobalt') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Cobalt</option>
                    <option value="idle_fingers" <?php echo ($syntax_highlighter_theme == 'idle_fingers') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Idle Fingers</option>
                    <option value="kr_theme" <?php echo ($syntax_highlighter_theme == 'kr_theme') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>krTheme</option>
                    <option value="merbivore" <?php echo ($syntax_highlighter_theme == 'merbivore') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Merbivore</option>
                    <option value="merbivore_soft" <?php echo ($syntax_highlighter_theme == 'merbivore_soft') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Merbivore Soft</option>
                    <option value="mono_industrial" <?php echo ($syntax_highlighter_theme == 'mono_industrial') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Mono Industrial</option>
                    <option value="monokai" <?php echo ($syntax_highlighter_theme == 'monokai') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Monokai</option>
                    <option value="pastel_on_dark" <?php echo ($syntax_highlighter_theme == 'pastel_on_dark') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Pastel on Dark</option>
                    <option value="solarized_dark" <?php echo ($syntax_highlighter_theme == 'solarized_dark') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Solarized Dark</option>
                    <option value="terminal" <?php echo ($syntax_highlighter_theme == 'terminal') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Terminal</option>
                    <option value="tomorrow_night" <?php echo ($syntax_highlighter_theme == 'tomorrow_night') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Tomorrow Night</option>
                    <option value="tomorrow_night_blue" <?php echo ($syntax_highlighter_theme == 'tomorrow_night_blue') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Tomorrow Night Blue</option>
                    <option value="tomorrow_night_bright" <?php echo ($syntax_highlighter_theme == 'tomorrow_night_bright') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Tomorrow Night Bright</option>
                    <option value="tomorrow_night_eighties" <?php echo ($syntax_highlighter_theme == 'tomorrow_night_eighties') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Tomorrow Night 80s</option>
                    <option value="twilight" <?php echo ($syntax_highlighter_theme == 'twilight') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Twilight</option>
                    <option value="vibrant_ink" <?php echo ($syntax_highlighter_theme == 'vibrant_ink') ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>>Vibrant Ink</option>
                </optgroup>
            </select>
          </td>
        </tr>
        <tr>
          <td>
            Min. user role for ind. exceptions editing
          </td>
          <td>
            <select style="margin-bottom: 3px;" id="minimum-user-role" name="minimum-user-role" selected-value="1" data="<?php echo get_minimum_user_role (); ?>" default="<?php echo DEFAULT_MINIMUM_USER_ROLE; ?>" style="width:300px">
              <?php wp_dropdown_roles (get_minimum_user_role ()); ?>
            </select>
          </td>
        </tr>
        <tr>
          <td>
          Sticky widget mode
          </td>
          <td>
            <select name="sticky-widget-mode"  default="<?php echo DEFAULT_STICKY_WIDGET_MODE; ?>" title="CSS mode is the best approach but may not work with all themes. JavaScript mode works with most themes but may reload ads on page load.">
              <option value="<?php echo AI_STICKY_WIDGET_MODE_CSS; ?>" <?php echo get_sticky_widget_mode()  == AI_STICKY_WIDGET_MODE_CSS ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_CSS; ?></option>
              <option value="<?php echo AI_STICKY_WIDGET_MODE_JS; ?>" <?php echo get_sticky_widget_mode() == AI_STICKY_WIDGET_MODE_JS ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_JS; ?></option>
            </select>
          </td>
        </tr>
        <tr>
          <td>
          Sticky widget top margin
          </td>
          <td>
            <input type="text" name="sticky-widget-margin" value="<?php echo get_sticky_widget_margin (); ?>"  default="<?php echo DEFAULT_STICKY_WIDGET_MARGIN; ?>" size="6" maxlength="4" /> px
          </td>
        </tr>
        <tr>
          <td>
          Dynamic blocks
          </td>
          <td>
            <select id="dynamic_blocks" name="dynamic_blocks" default="<?php echo DEFAULT_DYNAMIC_BLOCKS; ?>">
              <option value="<?php echo AI_DYNAMIC_BLOCKS_SERVER_SIDE; ?>" <?php echo get_dynamic_blocks()      == AI_DYNAMIC_BLOCKS_SERVER_SIDE ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_SERVER_SIDE; ?></option>
              <option value="<?php echo AI_DYNAMIC_BLOCKS_SERVER_SIDE_W3TC; ?>" <?php echo get_dynamic_blocks() == AI_DYNAMIC_BLOCKS_SERVER_SIDE_W3TC ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_SERVER_SIDE_W3TC; ?></option>
              <option value="<?php echo AI_DYNAMIC_BLOCKS_CLIENT_SIDE; ?>" <?php echo get_dynamic_blocks()      == AI_DYNAMIC_BLOCKS_CLIENT_SIDE ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_CLIENT_SIDE; ?></option>
            </select>
          </td>
        </tr>
        <tr>
          <td>
          Functions for paragraph counting
          </td>
          <td>
            <select id="paragraph_counting_functions" name="paragraph_counting_functions"  default="<?php echo DEFAULT_PARAGRAPH_COUNTING_FUNCTIONS; ?>" title="Standard PHP functions are faster and work in most cases, use Multibyte functions if paragraphs are not counted properly on non-english pages.">
              <option value="<?php echo AI_STANDARD_PARAGRAPH_COUNTING_FUNCTIONS; ?>" <?php echo get_paragraph_counting_functions()  == AI_STANDARD_PARAGRAPH_COUNTING_FUNCTIONS ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_STANDARD; ?></option>
              <option value="<?php echo AI_MULTIBYTE_PARAGRAPH_COUNTING_FUNCTIONS; ?>" <?php echo get_paragraph_counting_functions() == AI_MULTIBYTE_PARAGRAPH_COUNTING_FUNCTIONS ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_MULTIBYTE; ?></option>
            </select>
          </td>
        </tr>
        <tr>
          <td>
          No paragraph counting inside
          </td>
          <td>
            <input type="text" name="no-paragraph-counting-inside" style="width: 100%;" value="<?php echo get_no_paragraph_counting_inside (); ?>"  default="<?php echo DEFAULT_NO_PARAGRAPH_COUNTING_INSIDE; ?>" size="60" maxlength="80" />
          </td>
        </tr>
        <tr>
          <td>
          Ad label
          </td>
          <td>
            <input type="text" name="ad-label" style="width: 100%;" value="<?php echo get_ad_label (); ?>"  default="<?php echo DEFAULT_AD_TITLE; ?>" size="60" maxlength="500" />
          </td>
        </tr>

<?php if (function_exists ('ai_general_settings_2')) ai_general_settings_2 (); ?>

      </table>
    </div>

    <div class="rounded">
      <table class="ai-settings-table" style="width: 100%;">
        <tr>
          <td>
            <span title="CSS Class Name for the wrapping div">Block class name</span>
            <input id="block-class-name" class="ai-block-code-demo" style="margin-left: 5px;" type="text" name="block-class-name" value="<?php echo $block_class_name; ?>" default="<?php echo DEFAULT_BLOCK_CLASS_NAME; ?>" size="15" maxlength="40" />
          </td>
          <td>
            <span title="Include general plugin block class">Block class</span>
            <input type="hidden" name="block-class" value="0" />
            <input id="block-class" class="ai-block-code-demo" style="margin-left: 5px;" type="checkbox" name="block-class" value="1" default="<?php echo DEFAULT_BLOCK_CLASS; ?>" <?php if ($block_class == AI_ENABLED) echo 'checked '; ?> />
          </td>
          <td>
            <span title="Include block number class">Block number class</span>
            <input type="hidden" name="block-number-class" value="0" />
            <input id="block-number-class" class="ai-block-code-demo" style="margin-left: 5px;" type="checkbox" name="block-number-class" value="1" default="<?php echo DEFAULT_BLOCK_NUMBER_CLASS; ?>" <?php if ($block_number_class == AI_ENABLED) echo 'checked '; ?> />
          </td>
          <td>
            <span title="Instead of alignment classes generate inline alignment styles for code blocks">Inline styles</span>
            <input type="hidden" name="inline-styles" value="0" />
            <input id="inline-styles" class="ai-block-code-demo" style="margin-left: 5px;" type="checkbox" name="inline-styles" value="1" default="<?php echo DEFAULT_INLINE_STYLES; ?>" <?php if ($inline_styles == AI_ENABLED) echo 'checked '; ?> />
          </td>
        </tr>
      </table>
      <div style="margin-top: 8px;">Preview of the block wrapping code</div>
      <pre style="margin: 0; padding: 5px; background: #eee; color: #00f;" title="Wrapping div"><span id="ai-block-code-demo" ><?php echo ai_block_code_demo ($block_class_name, $block_class, $block_number_class, $inline_styles); ?></span>
  <span style="color: #222;">BLOCK CODE</span>
&lt;/div&gt;</pre>
    </div>

    </div>

    <div id="tab-viewports" class="rounded">
      <div style="margin: 0 0 8px 0;">
        <strong>Viewport Settings used for client-side device detection</strong>
      </div>
<?php
  for ($viewport = 1; $viewport <= AD_INSERTER_VIEWPORTS; $viewport ++) {
    $bottom_margin = $viewport == AD_INSERTER_VIEWPORTS ? 0 : 4;
?>
      <div style="margin: 4px 0 <?php echo $bottom_margin; ?>px 0;">
          Viewport <?php echo $viewport; ?> name&nbsp;&nbsp;&nbsp;
            <input style="margin-left: 0px;" type="text" name="viewport-name-<?php echo $viewport; ?>" value="<?php echo get_viewport_name ($viewport); ?>" default="<?php echo defined ("DEFAULT_VIEWPORT_NAME_" . $viewport) ? constant ("DEFAULT_VIEWPORT_NAME_" . $viewport) : ""; ?>" size="15" maxlength="40" />
            <?php if ($viewport == AD_INSERTER_VIEWPORTS) echo '<span style="display: none;">' ?>
             &nbsp;&nbsp; min width
            <input type="text" id="option-length-<?php echo $viewport; ?>" name="viewport-width-<?php echo $viewport; ?>" value="<?php echo get_viewport_width ($viewport); ?>" default="<?php echo defined ("DEFAULT_VIEWPORT_WIDTH_" . $viewport) ? constant ("DEFAULT_VIEWPORT_WIDTH_" . $viewport) : ""; ?>" size="4" maxlength="4" /> px
            <?php if ($viewport == AD_INSERTER_VIEWPORTS) echo '</span>' ?>
        </div>
<?php
  }
?>
    </div>

    <div id="tab-hooks" class="rounded">
      <div style="margin: 0 0 8px 0;">
        <strong>Custom Hooks</strong>
      </div>

      <table>
        <tbody>
<?php
  for ($hook = 1; $hook <= AD_INSERTER_HOOKS; $hook ++) {
?>
          <tr>
            <td style="padding: 0 0 2px 0;">
              <input type="hidden"   name="hook-enabled-<?php echo $hook; ?>" value="0" />
              <input type="checkbox" name="hook-enabled-<?php echo $hook; ?>" value="1" default="<?php echo AI_DISABLED; ?>" id="hook-enabled-<?php echo $hook; ?>" title="Enable or disable hook" <?php if (get_hook_enabled ($hook) == AI_ENABLED) echo 'checked '; ?> />
            </td>
            <td style="white-space: nowrap;">
              <label for="hook-enabled-<?php echo $hook; ?>" title="Enable or disable hook">Hook <?php echo $hook; ?> name</label>
            </td>
            <td style="width: 25%;">
              <input style="width: 100%;" title="Hook name for automatic insertion selection" type="text" name="hook-name-<?php echo $hook; ?>" default="" value="<?php echo get_hook_name ($hook); ?>" size="30" maxlength="80" />
            </td>
            <td style="padding-left: 7px;">
              action
            </td>
            <td style="">
              <input style="width: 100%;" title="Action tag as used in the do_action () function" type="text" name="hook-action-<?php echo $hook; ?>" default="" value="<?php echo get_hook_action ($hook); ?>" size="30" maxlength="80" />
            </td>
            <td style="padding-left: 7px;">
              priority
            </td>
            <td>
              <input title="Priority for the hook (default is 10)" type="text" name="hook-priority-<?php echo $hook; ?>" default="<?php echo DEFAULT_CUSTOM_HOOK_PRIORITY; ?>" value="<?php echo get_hook_priority ($hook); ?>" size="5" maxlength="7" />
            </td>
          </tr>
<?php
  }
?>
        </tbody>
      </table>
    </div>

    <div id="tab-header" style="margin: 0px 0; padding: 0; ">
      <div style="margin: 8px 0;">
        <div style="float: right;">
      <?php if (AI_SYNTAX_HIGHLIGHTING) : ?>
          <input type="checkbox" value="0" id="simple-editor-h" class="simple-editor-button" style="display: none;" />
          <label class="checkbox-button" style="margin-left: 10px;" for="simple-editor-h" title="Toggle Syntax Highlighting / Simple editor for mobile devices"><span class="checkbox-icon icon-tablet"></span></label>
      <?php endif; ?>

          <input type="hidden"   name="<?php echo AI_OPTION_ENABLE_MANUAL, '_block_h'; ?>" value="0" />
          <input type="checkbox" name="<?php echo AI_OPTION_ENABLE_MANUAL, '_block_h'; ?>" id="enable-header" value="1" default="<?php echo $default->get_enable_manual(); ?>" <?php if ($adH->get_enable_manual () == AI_ENABLED) echo 'checked '; ?> style="display: none;" />
          <label class="checkbox-button" style="margin-left: 10px;" for="enable-header" title="Enable or disable insertion of this code into HTML page header"><span class="checkbox-icon icon-enabled<?php if ($adH->get_enable_manual () == AI_ENABLED) echo ' on'; ?>"></span></label>

          <input type="hidden"   name="<?php echo AI_OPTION_PROCESS_PHP, '_block_h'; ?>" value="0" />
          <input type="checkbox" name="<?php echo AI_OPTION_PROCESS_PHP, '_block_h'; ?>" value="1" id="process-php-h" default="<?php echo $default->get_process_php (); ?>" <?php if ($adH->get_process_php () == AI_ENABLED) echo 'checked '; ?> style="display: none;" />
          <label class="checkbox-button" style="margin-left: 10px;" for="process-php-h" title="Process PHP code"><span class="checkbox-icon icon-php<?php if ($adH->get_process_php () == AI_ENABLED) echo ' on'; ?>"></span></label>
        </div>

        <div>
          <h3 style="margin: 8px 0 8px 2px;">HTML Page Header Code</h3>
        </div>
      </div>

      <div style="margin: 8px 0; width: 100%;">
        <div style="float: left;">
          Code in the <pre style="display: inline; color: blue;">&lt;head&gt;&lt;/head&gt;</pre> section of the HTML page
          <?php if ($header_code_disabled) echo '<span style="color: #f00;">DISABLED</span>'; ?>
        </div>

        <div style="clear: both;"></div>
      </div>

      <div style="margin: 8px 0;">
        <textarea id="block-h" name="<?php echo AI_OPTION_CODE, '_block_h'; ?>" class="simple-editor" style="background-color:#F9F9F9; font-family: Courier, 'Courier New', monospace; font-weight: bold;" default=""><?php echo esc_textarea ($adH->get_ad_data()); ?></textarea>
      </div>

      <div id="device-detection-settings-h" class="rounded">
        <input type="hidden" name="<?php echo AI_OPTION_DETECT_SERVER_SIDE, WP_FORM_FIELD_POSTFIX, AI_HEADER_OPTION_NAME; ?>" value="0" />
        <input type="checkbox" name="<?php echo AI_OPTION_DETECT_SERVER_SIDE, WP_FORM_FIELD_POSTFIX, AI_HEADER_OPTION_NAME; ?>" id="server-side-detection-h" style="margin-top: 1px;" value="1" default="<?php echo $default->get_detection_server_side(); ?>" <?php if ($adH->get_detection_server_side ()==AI_ENABLED) echo 'checked '; ?> />
        <label for="server-side-detection-h">Use server-side detection to insert code only for </label>
        <select id="display-for-devices-h" name="<?php echo AI_OPTION_DISPLAY_FOR_DEVICES, WP_FORM_FIELD_POSTFIX, AI_HEADER_OPTION_NAME; ?>" default="<?php echo $default->get_display_for_devices(); ?>" >
          <option value="<?php echo AD_DISPLAY_DESKTOP_DEVICES; ?>" <?php echo ($adH->get_display_for_devices() == AD_DISPLAY_DESKTOP_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_DESKTOP_DEVICES; ?></option>
          <option value="<?php echo AD_DISPLAY_MOBILE_DEVICES; ?>" <?php echo ($adH->get_display_for_devices() == AD_DISPLAY_MOBILE_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_MOBILE_DEVICES; ?></option>
          <option value="<?php echo AD_DISPLAY_TABLET_DEVICES; ?>" <?php echo ($adH->get_display_for_devices() == AD_DISPLAY_TABLET_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_TABLET_DEVICES; ?></option>
          <option value="<?php echo AD_DISPLAY_PHONE_DEVICES; ?>" <?php echo ($adH->get_display_for_devices() == AD_DISPLAY_PHONE_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_PHONE_DEVICES; ?></option>
          <option value="<?php echo AD_DISPLAY_DESKTOP_TABLET_DEVICES; ?>" <?php echo ($adH->get_display_for_devices() == AD_DISPLAY_DESKTOP_TABLET_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_DESKTOP_TABLET_DEVICES; ?></option>
          <option value="<?php echo AD_DISPLAY_DESKTOP_PHONE_DEVICES; ?>" <?php echo ($adH->get_display_for_devices() == AD_DISPLAY_DESKTOP_PHONE_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_DESKTOP_PHONE_DEVICES; ?></option>
        </select>
        devices

        <span style="float: right; margin-top: 2px;">
          <input type="hidden" name="<?php echo AI_OPTION_ENABLE_404, '_block_h'; ?>" value="0" />
          <input style="margin-left: 10px; margin-top: 1px;" type="checkbox" name="<?php echo AI_OPTION_ENABLE_404, '_block_h'; ?>" id="enable-header-404" value="1" default="<?php echo $default->get_enable_404(); ?>" <?php if ($adH->get_enable_404 () == AI_ENABLED) echo 'checked '; ?> />
          <label for="enable-header-404" title="Enable or disable insertion of this code into HTML page header on page for Error 404: Page not found">Insert on Error 404 page</label>
        </span>
      </div>
    </div>

    <div id="tab-footer" style="margin: 0px 0; padding: 0; ">
      <div style="margin: 8px 0;">
        <div style="float: right;">
    <?php if (AI_SYNTAX_HIGHLIGHTING) : ?>
          <input type="checkbox" value="0" id="simple-editor-f" class="simple-editor-button" style="display: none;" />
          <label class="checkbox-button" style="margin-left: 10px;" for="simple-editor-f" title="Toggle Syntax Highlighting / Simple editor for mobile devices"><span class="checkbox-icon icon-tablet"></span></label>
    <?php endif; ?>

          <input type="hidden"   name="<?php echo AI_OPTION_ENABLE_MANUAL, '_block_f'; ?>" value="0" />
          <input type="checkbox" name="<?php echo AI_OPTION_ENABLE_MANUAL, '_block_f'; ?>" id="enable-footer" value="1" default="<?php echo $default->get_enable_manual(); ?>" <?php if ($adF->get_enable_manual () == AI_ENABLED) echo 'checked '; ?> style="display: none;" />
          <label class="checkbox-button" style="margin-left: 10px;" for="enable-footer" title="Enable or disable insertion of this code into HTML page footer"><span class="checkbox-icon icon-enabled<?php if ($adF->get_enable_manual () == AI_ENABLED) echo ' on'; ?>"></span></label>

          <input type="hidden"   name="<?php echo AI_OPTION_PROCESS_PHP, '_block_f'; ?>" value="0" />
          <input type="checkbox" name="<?php echo AI_OPTION_PROCESS_PHP, '_block_f'; ?>" value="1" id="process-php-f" default="<?php echo $default->get_process_php (); ?>" <?php if ($adF->get_process_php () == AI_ENABLED) echo 'checked '; ?> style="display: none;" />
          <label class="checkbox-button" style="margin-left: 10px;" for="process-php-f" title="Process PHP code"><span class="checkbox-icon icon-php<?php if ($adF->get_process_php () == AI_ENABLED) echo ' on'; ?>"></span></label>
        </div>

        <div>
          <h3 style="margin: 8px 0 8px 2px;">HTML Page Footer Code</h3>
        </div>
      </div>

      <div style="margin: 8px 0; width: 100%;">
        <div style="float: left;">
          Code before the <pre style="display: inline; color: blue;">&lt;/body&gt;</pre> tag of the the HTML page
          <?php if ($footer_code_disabled) echo '<span style="color: #f00;">DISABLED</span>'; ?>
        </div>

        <div style="clear: both;"></div>
      </div>

      <div style="margin: 8px 0;">
        <textarea id="block-f" name="<?php echo AI_OPTION_CODE, '_block_f'; ?>" class="simple-editor" style="background-color:#F9F9F9; font-family: Courier, 'Courier New', monospace; font-weight: bold;" default=""><?php echo esc_textarea ($adF->get_ad_data()); ?></textarea>
      </div>

      <div id="device-detection-settings-f" class="rounded">
        <input type="hidden" name="<?php echo AI_OPTION_DETECT_SERVER_SIDE, WP_FORM_FIELD_POSTFIX, AI_FOOTER_OPTION_NAME; ?>" value="0" />
        <input style="margin-top: 1px;" type="checkbox" name="<?php echo AI_OPTION_DETECT_SERVER_SIDE, WP_FORM_FIELD_POSTFIX, AI_FOOTER_OPTION_NAME; ?>" id="server-side-detection-f" value="1" default="<?php echo $default->get_detection_server_side(); ?>" <?php if ($adF->get_detection_server_side ()==AI_ENABLED) echo 'checked '; ?> />
        <label for="server-side-detection-f">Use server-side detection to insert code only for </label>
        <select id="display-for-devices-f" name="<?php echo AI_OPTION_DISPLAY_FOR_DEVICES, WP_FORM_FIELD_POSTFIX, AI_FOOTER_OPTION_NAME; ?>" default="<?php echo $default->get_display_for_devices(); ?>" >
          <option value="<?php echo AD_DISPLAY_DESKTOP_DEVICES; ?>" <?php echo ($adF->get_display_for_devices() == AD_DISPLAY_DESKTOP_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_DESKTOP_DEVICES; ?></option>
          <option value="<?php echo AD_DISPLAY_MOBILE_DEVICES; ?>" <?php echo ($adF->get_display_for_devices() == AD_DISPLAY_MOBILE_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_MOBILE_DEVICES; ?></option>
          <option value="<?php echo AD_DISPLAY_TABLET_DEVICES; ?>" <?php echo ($adF->get_display_for_devices() == AD_DISPLAY_TABLET_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_TABLET_DEVICES; ?></option>
          <option value="<?php echo AD_DISPLAY_PHONE_DEVICES; ?>" <?php echo ($adF->get_display_for_devices() == AD_DISPLAY_PHONE_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_PHONE_DEVICES; ?></option>
          <option value="<?php echo AD_DISPLAY_DESKTOP_TABLET_DEVICES; ?>" <?php echo ($adF->get_display_for_devices() == AD_DISPLAY_DESKTOP_TABLET_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_DESKTOP_TABLET_DEVICES; ?></option>
          <option value="<?php echo AD_DISPLAY_DESKTOP_PHONE_DEVICES; ?>" <?php echo ($adF->get_display_for_devices() == AD_DISPLAY_DESKTOP_PHONE_DEVICES) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AD_DISPLAY_DESKTOP_PHONE_DEVICES; ?></option>
        </select>
        devices

        <span style="float: right; margin-top: 2px;">
          <input type="hidden" name="<?php echo AI_OPTION_ENABLE_404, '_block_f'; ?>" value="0" />
          <input style="margin-left: 10px; margin-top: 1px;" type="checkbox" name="<?php echo AI_OPTION_ENABLE_404, '_block_f'; ?>" id="enable-footer-404" value="1" default="<?php echo $default->get_enable_404(); ?>" <?php if ($adF->get_enable_404 () == AI_ENABLED) echo 'checked '; ?> />
          <label for="enable-footer-404" title="Enable or disable insertion of this code into HTML page footer on page for Error 404: Page not found">Insert on Error 404 page</label>
        </span>
      </div>
    </div>

<?php if (function_exists ('ai_plugin_settings')) ai_plugin_settings ($start, $end, $exceptions); ?>

<?php if (defined ('AI_ADBLOCKING_DETECTION') && AI_ADBLOCKING_DETECTION) { ?>

    <div id="tab-adblocking" style="margin: 0px 0; padding: 0; ">
      <div style="margin: 8px 0;">
        <div style="float: right;">
          <input type="hidden"   name="<?php echo AI_OPTION_ENABLE_MANUAL, '_block_a'; ?>" value="0" />
          <input type="checkbox" name="<?php echo AI_OPTION_ENABLE_MANUAL, '_block_a'; ?>" id="enable-adb-detection" value="1" default="<?php echo $default->get_enable_manual(); ?>" <?php if ($adA->get_enable_manual () == AI_ENABLED) echo 'checked '; ?> style="display: none;" />
          <label class="checkbox-button" style="margin-left: 10px;" for="enable-adb-detection" title="Enable or disable detection of ad blocking"><span class="checkbox-icon icon-enabled<?php if ($adA->get_enable_manual () == AI_ENABLED) echo ' on'; ?>"></span></label>
        </div>

        <div>
          <h3 style="margin: 8px 0 8px 2px;">Ad Blocking Detection</h3>
        </div>
      </div>

      <div class="rounded" style="margin: 16px 0 8px;">
        <table class="ai-settings-table" style="width: 100%;" cellpadding="0">
          <tr>
            <td style="width: 20%;">
              <label for="adb-action">Action</label>
            </td>
            <td>
              <select id="adb-action" name="<?php echo AI_OPTION_ADB_ACTION; ?>" title="Global action when ad blocking is detected" default="<?php echo AI_DEFAULT_ADB_ACTION; ?>" >
                <option value="<?php echo AI_ADB_ACTION_NONE; ?>" <?php echo ($adb_action == AI_ADB_ACTION_NONE) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_NONE; ?></option>
                <option value="<?php echo AI_ADB_ACTION_MESSAGE; ?>" <?php echo ($adb_action == AI_ADB_ACTION_MESSAGE) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_POPUP_MESSAGE; ?></option>
                <option value="<?php echo AI_ADB_ACTION_REDIRECTION; ?>" <?php echo ($adb_action == AI_ADB_ACTION_REDIRECTION) ? AD_SELECT_SELECTED : AD_EMPTY_VALUE; ?>><?php echo AI_TEXT_REDIRECTION; ?></option>
              </select>
            </td>
          </tr>
          <tr>
            <td>
              Delay Action
            </td>
            <td>
              <input style="width: 40px;" type="text" name="<?php echo AI_OPTION_ADB_DELAY_ACTION; ?>" title="Number of page views to delay action when ad blocking is detected. Leave empty for no delay (action fires on first page view). Sets cookie." value="<?php echo get_delay_action (); ?>"  default="" size="3" maxlength="5" /> page views
            </td>
          </tr>
          <tr>
            <td>
              No Action Period
            </td>
            <td>
              <input style="width: 40px;" type="text" name="<?php echo AI_OPTION_ADB_NO_ACTION_PERIOD; ?>" title="Number of days to supress action when ad blocking is detected. Leave empty for no no-action period (action fires always after defined page view delay). Sets cookie." value="<?php echo get_no_action_period (); ?>"  default="<?php echo AI_DEFAULT_ADB_NO_ACTION_PERIOD; ?>" size="3" maxlength="5" /> days
            </td>
          </tr>
          <tr>
            <td>
              Custom Selectors
            </td>
            <td>
              <input style="width: 100%;" type="text" name="<?php echo AI_OPTION_ADB_SELECTORS; ?>" title="Comma seprarated list of selectors (.class, #id) used for additional ad blocking detection. Invisible or zero height of the element means ad blocking is present." value="<?php echo get_adb_selectors (); ?>"  default="" size="50" maxlength="200" />
            </td>
          </tr>
<?php if (function_exists ('ai_adb_settings')) ai_adb_settings (); ?>
        </table>
      </div>

      <div id="adb-page-redirection" class="rounded">
        <table class="ai-settings-table" style="width: 100%;">
          <tr>
            <td style="width: 20%;">
              <label for="redirection-page">Redirection Page</label>
            </td>
            <td>
<?php
  $args = array(
    'depth'                 => 0,
    'child_of'              => 0,
    'selected'              => get_redirection_page (true),
    'echo'                  => 0,
    'name'                  => AI_OPTION_ADB_REDIRECTION_PAGE,
    'id'                    => 'redirection-page',
    'class'                 => null,
    'show_option_none'      => 'Custom Url',
    'show_option_no_change' => null,
    'option_none_value'     => '0',
  );
  $dropdown_pages = wp_dropdown_pages ($args);
  $dropdown_pages = str_replace ('<select ', '<select default="'.AI_DEFAULT_ADB_REDIRECTION_PAGE.'" title="Static page for redirection when ad blocking is detected. For other pages select Custom url and set it below." ', $dropdown_pages);

  echo $dropdown_pages;
?>
            </td>
          </tr>
        <tr>
          <td>
          Custom Redirection Url
          </td>
          <td>
            <input id="custom-redirection-url" style="width: 100%;" type="text" name="<?php echo AI_OPTION_ADB_CUSTOM_REDIRECTION_URL; ?>" value="<?php echo get_custom_redirection_url (); ?>"  default="" size="50" maxlength="200" />
          </td>
        </tr>
        </table>
      </div>

      <div id="adb-message">
        <div style="padding: 0; min-height: 28px;">
          <div style="float: left; margin: 10px 0 0 3px;">
            Message HTML code
          </div>
          <div style="float: right;">

      <?php if (AI_SYNTAX_HIGHLIGHTING) : ?>
            <input type="checkbox" value="0" id="simple-editor-a" class="simple-editor-button" style="display: none;" />
            <label class="checkbox-button" style="margin-left: 10px;" for="simple-editor-a" title="Toggle Syntax Highlighting / Simple editor for mobile devices"><span class="checkbox-icon icon-tablet"></span></label>
      <?php endif; ?>

            <input type="hidden"   name="<?php echo AI_OPTION_PROCESS_PHP, '_block_a'; ?>" value="0" />
            <input type="checkbox" name="<?php echo AI_OPTION_PROCESS_PHP, '_block_a'; ?>" value="1" id="process-php-a" default="<?php echo $default->get_process_php (); ?>" <?php if ($adA->get_process_php () == AI_ENABLED) echo 'checked '; ?> style="display: none;" />
            <label class="checkbox-button" style="margin-left: 10px;" for="process-php-a" title="Process PHP code"><span class="checkbox-icon icon-php<?php if ($adA->get_process_php () == AI_ENABLED) echo ' on'; ?>"></span></label>

            <button id="preview-button-adb" type="button" class='ai-button' style="display: none; margin: 0 4px 0 10px;" title="Preview message when ad blocking is detected" nonce="<?php echo wp_create_nonce ("adinserter_data"); ?>" site-url="<?php echo wp_make_link_relative (get_site_url()); ?>">Preview</button>
          </div>
          <div style="clear: both;"></div>
        </div>

        <div style="margin: 8px 0;">
          <textarea id="block-a" name="<?php echo AI_OPTION_CODE, '_block_a'; ?>" class="simple-editor small" style="background-color:#F9F9F9; font-family: Courier, 'Courier New', monospace; font-weight: bold;" default="<?php echo esc_textarea (AI_DEFAULT_ADB_MESSAGE); ?>"><?php echo esc_textarea ($adA->get_ad_data()); ?></textarea>
        </div>

        <div class="rounded">
          <table class="ai-settings-table" style="width: 100%;">
            <tr>
              <td style="width: 20%;">
              Message CSS
              </td>
              <td>
                <input id="message-css" style="width: 100%;" type="text" name="<?php echo AI_OPTION_ADB_MESSAGE_CSS; ?>" value="<?php echo get_message_css (); ?>"  default="<?php echo AI_DEFAULT_ADB_MESSAGE_CSS; ?>" size="50" maxlength="200" />
              </td>
            </tr>
            <tr>
              <td>
              Overlay CSS
              </td>
              <td>
                <input id="overlay-css" style="width: 100%;" type="text" name="<?php echo AI_OPTION_ADB_OVERLAY_CSS; ?>" value="<?php echo get_overlay_css (); ?>"  default="<?php echo AI_DEFAULT_ADB_OVERLAY_CSS; ?>" size="50" maxlength="200" />
              </td>
            </tr>
            <tr>
              <td>
                <label for="undismissible-message" title="Prevent visitors from closing the warning message">Undismissible Message</label>
              </td>
              <td>
                <input type="hidden" name="<?php echo AI_OPTION_ADB_UNDISMISSIBLE_MESSAGE; ?>" value="0" />
                <input type="checkbox" name="<?php echo AI_OPTION_ADB_UNDISMISSIBLE_MESSAGE; ?>" id="undismissible-message" value="1" default="<?php echo AI_DEFAULT_ADB_UNDISMISSIBLE_MESSAGE; ?>" <?php if (get_undismissible_message () == AI_ENABLED) echo 'checked '; ?> />
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>

<?php } ?>

    <div id="tab-debugging" class="rounded">
      <table class="ai-settings-table" style="width: 100%;">
        <tr>
          <td style="width: 30%;">
            <label for="admin-toolbar-debugging" title="Enable or disable debugging functions in admin toolbar">Debugging functions in admin toolbar</label>
          </td>
          <td>
            <input type="hidden" name="admin_toolbar_debugging" value="0" />
            <input type="checkbox" name="admin_toolbar_debugging" id="admin-toolbar-debugging" value="1" default="<?php echo DEFAULT_ADMIN_TOOLBAR_DEBUGGING; ?>" <?php if (get_admin_toolbar_debugging ()==AI_ENABLED) echo 'checked '; ?> />
          </td>
        </tr>
        <tr>
          <td>
            <label for="remote-debugging" title="Enable Debugger widget and code insertion debugging (blocks, positions, tags, processing) by url parameters for non-logged in users. Enable this option to allow other people to see Debugger widget, labeled blocks and positions in order to help you to diagnose problems. For logged in administrators debugging is always enabled.">Remote debugging</label>
          </td>
          <td>
            <input type="hidden" name="remote_debugging" value="0" />
            <input type="checkbox" name="remote_debugging" id="remote-debugging" value="1" default="<?php echo DEFAULT_REMOTE_DEBUGGING; ?>" <?php if (get_remote_debugging ()==AI_ENABLED) echo 'checked '; ?> />
          </td>
        </tr>
        <tr class="system-debugging" style="display: none;">
          <td>
            <label for="backend-js-debugging" title="Enable backend javascript console output">Backend javascript debugging</label>
          </td>
          <td>
            <input type="hidden" name="backend_js_debugging" value="0" />
            <input type="checkbox" name="backend_js_debugging" id="backend-js-debugging" value="1" default="<?php echo DEFAULT_BACKEND_JS_DEBUGGING; ?>" <?php if (get_backend_javascript_debugging ()==AI_ENABLED) echo 'checked '; ?> />
          </td>
        </tr>
        <tr class="system-debugging" style="display: none;">
          <td>
            <label for="frontend-js-debugging" title="Enable frontend javascript console output">Frontend javascript debugging</label>
          </td>
          <td>
            <input type="hidden" name="frontend_js_debugging" value="0" />
            <input type="checkbox" name="frontend_js_debugging" id="frontend-js-debugging" value="1" default="<?php echo DEFAULT_FRONTEND_JS_DEBUGGING; ?>" <?php if (get_frontend_javascript_debugging ()==AI_ENABLED) echo 'checked '; ?> />
          </td>
        </tr>
        <tr class="system-debugging" style="display: none;">
          <td>
            Installation
          </td>
          <td>
            <?php echo ($install_timestamp = get_option (AI_INSTALL_NAME)) !== false ? date ("Y-m-d H:i:s", $install_timestamp + get_option ('gmt_offset') * 3600) : ""; ?>
          </td>
        </tr>
        <tr class="system-debugging" style="display: none;">
          <td>
            Age
          </td>
          <td>
           <?php if (isset ($ai_wp_data [AI_INSTALL_TIME_DIFFERENCE])) printf ('%04d-%02d-%02d %02d:%02d:%02d (%d days)',
                                                                                      $ai_wp_data [AI_INSTALL_TIME_DIFFERENCE]->y,
                                                                                      $ai_wp_data [AI_INSTALL_TIME_DIFFERENCE]->m,
                                                                                      $ai_wp_data [AI_INSTALL_TIME_DIFFERENCE]->d,
                                                                                      $ai_wp_data [AI_INSTALL_TIME_DIFFERENCE]->h,
                                                                                      $ai_wp_data [AI_INSTALL_TIME_DIFFERENCE]->i,
                                                                                      $ai_wp_data [AI_INSTALL_TIME_DIFFERENCE]->s,
                                                                                      isset ($ai_wp_data [AI_DAYS_SINCE_INSTAL]) ? $ai_wp_data [AI_DAYS_SINCE_INSTAL] : null); ?>
          </td>
        </tr>
        <tr class="system-debugging" style="display: none;">
          <td>
            Used blocks
          </td>
          <td>
            <?php if (isset ($ai_db_options_extract [AI_EXTRACT_USED_BLOCKS])) echo count ($used_blocks), ' (', implode (', ', array_slice ($used_blocks, 0, 26)), count ($used_blocks) > 26 ? ',...' : '', ')'; ?>
          </td>
        </tr>
<?php if (function_exists ('ai_system_debugging')) ai_system_debugging (); ?>
      </table>
    </div>

  </div>
</div> <!-- tab-0 -->

  </div> <!-- ai-tab-container -->

<?php  if (!isset ($_GET ['settings'])): // start of code only for normal settings ?>

</div> <!-- ai-container -->

<?php

  $sidebar = 0;
  $number_of_used_blocks = count ($used_blocks);
  if (isset ($ai_wp_data [AI_DAYS_SINCE_INSTAL])) {
    if ($ai_wp_data [AI_DAYS_SINCE_INSTAL] >  2)
      $sidebar = 1;

    if ($number_of_used_blocks >=  4 && $ai_wp_data [AI_DAYS_SINCE_INSTAL] >  5 ||
                                        $ai_wp_data [AI_DAYS_SINCE_INSTAL] > 10)
      $sidebar = 2;

    if ($number_of_used_blocks >= 12 && $ai_wp_data [AI_DAYS_SINCE_INSTAL] >  7 ||
        $number_of_used_blocks >=  8 && $ai_wp_data [AI_DAYS_SINCE_INSTAL] > 10 ||
                                        $ai_wp_data [AI_DAYS_SINCE_INSTAL] > 15)
      $sidebar = 3;

    if ($ai_wp_data [AI_DAYS_SINCE_INSTAL] > 20)
      $sidebar = 4;

  } else {
      if ($number_of_used_blocks >= 3) $sidebar = 4;
    }

//  $sidebar = 4;

  if (!function_exists ('ai_settings_side'))  {

    switch ($sidebar) {
      case 0:
        break;
      case 1:
        break;
      case 2:
        sidebar_addense_alternative ();
        break;
      case 3:
        sidebar_support_review ();
        sidebar_addense_alternative ();
        break;
      case 4:
        sidebar_addense_alternative ();
        break;
    }

  }
?>

<input id="ai-active-tab" type="hidden" name="ai-active-tab" value="[<?php echo $active_tab, ',', $active_tab_0; ?>]" />
<?php wp_nonce_field ('save_adinserter_settings'); ?>

</form>

</div> <!-- #ai-settings -->

<div id="ai-sidebar" style="float: left;">

<?php
  if ($subpage == 'main') {
    code_block_list_container ();
    if (defined ('AI_ADSENSE_API')) {
      adsense_list_container ();
    }

    if (function_exists ('ai_settings_side')) {
      ai_settings_side ();
    } else {
      switch ($sidebar) {
        case 0:
          sidebar_help ();
          sidebar_pro ();
          break;
        case 1:
          sidebar_support_plugin ();
          sidebar_help ();
          sidebar_pro ();
          break;
        case 2:
          sidebar_support_plugin ();
          sidebar_help ();
          sidebar_pro ();
          break;
        case 3:
          sidebar_support_plugin ();
          sidebar_help ();
          sidebar_pro ();
          break;
        case 4:
          sidebar_support_plugin ();
          sidebar_support_review ();
          sidebar_help ();
          sidebar_pro ();
          break;
      }
    }
  }
?>

</div>

<script type="text/javascript">
  jQuery(document).ready(function($) {
    setTimeout (check_blocked_images, 400);
  });

  function check_blocked_images () {

    function replace_blocked_image (image_id, image_src, css_display) {
      var image_selector = "#" + image_id;
      if (jQuery (image_selector).length && !jQuery(image_selector + ":visible").length) {
        blocking_counter ++;
        var image = jQuery(image_selector);
        image.hide ().after (image.clone ().attr ('class', '').attr ("id", image_id + '-ajax').
        attr ('src', ajaxurl+'?action=ai_ajax_backend&image=' + image_src + '&ai_check=<?php echo wp_create_nonce ('adinserter_data'); ?>').
        css ('display', css_display));
      }
    }

    jQuery("#blocked-warning.warning-enabled").show ();
    jQuery("#blocked-warning.warning-enabled .blocked-warning-text").css ('color', '#00f');
<?php
    if (!function_exists ('ai_settings_side')) {
?>

    var blocking_counter = 0;
    replace_blocked_image ('ai-media-1',    'contextual-1.gif',     'block');
    replace_blocked_image ('ai-media-2',    'contextual-2.jpg',     'block');
    replace_blocked_image ('ai-pro-1',      'icon-256x256.jpg',     'block');
    replace_blocked_image ('ai-pro-2',      'ai-charts-250.png',    'block');
    replace_blocked_image ('ai-pro-3',      'ai-countries-250.png', 'block');
    replace_blocked_image ('ai-stars-img',  'stars.png',            'inline');
    replace_blocked_image ('ai-tw',         'twitter.png',          'inline');
    replace_blocked_image ('ai-fb',         'facebook.png',         'inline');
    if (blocking_counter > 5) {
      var message = 'Ad blocking test: ' + blocking_counter + ' images not loaded';
      console.log ('AD INSERTER:', message);
      jQuery("#blocked-warning").attr ('title', message).show ();
      jQuery("#blocked-warning .blocked-warning-text").css ('color', '#00f');
    }
<?php
    }
?>
  }
</script>

<?php

  endif; // end of code only for normal settings

  if (isset ($_POST [AI_FORM_SAVE])) {
    if (function_exists ('ai_save_settings')) ai_save_settings ();
  }

} // generate_settings_form ()

function get_sidebar_widgets () {
  $sidebar_widgets = wp_get_sidebars_widgets();
  $widget_options = get_option ('widget_ai_widget');

  $sidebars_with_widgets = array ();
//  for ($block = $start; $block <= $end; $block ++){
  for ($block = 1; $block <= AD_INSERTER_BLOCKS; $block ++){
    $sidebars_with_widget [$block]= array ();
  }
  foreach ($sidebar_widgets as $sidebar_index => $sidebar_widget) {
    if (is_array ($sidebar_widget) && isset ($GLOBALS ['wp_registered_sidebars'][$sidebar_index]['name'])) {
      $sidebar_name = $GLOBALS ['wp_registered_sidebars'][$sidebar_index]['name'];
      if ($sidebar_name != "") {
        foreach ($sidebar_widget as $widget) {
          if (preg_match ("/ai_widget-([\d]+)/", $widget, $widget_id)) {
            if (isset ($widget_id [1]) && is_numeric ($widget_id [1])) {
              $widget_option = $widget_options [$widget_id [1]];
              $widget_block = $widget_option ['block'];
//              if ($widget_block >= $start && $widget_block <= $end && !in_array ($sidebar_name, $sidebars_with_widget [$widget_block])) {
              if ($widget_block >= 1 && $widget_block <= AD_INSERTER_BLOCKS && !in_array ($sidebar_name, $sidebars_with_widget [$widget_block])) {
                $sidebars_with_widget [$widget_block] []= $sidebar_name;
              }
            }
          }
        }
      }
    }
  }

  return $sidebars_with_widget;
}

function code_block_list_container () {
?>
  <div id="ai-list-container" class="ai-form rounded" style="background: #fff; display: none;">
    <div id='ai-list-controls' class='ui-widget' style='margin: 0 auto 8px;'>
      <span style="vertical-align: middle; float: left;">
        <input id="ai-list-search" type="text" value="" size="40" maxlength="40" />
      </span>
      <span class="ai-toolbar-button small" style="vertical-align: middle; float: right;">
        <input type="checkbox" value="0" id="ai-load-all" style="display: none;" />
        <label class="checkbox-button" for="ai-load-all" title="Toggle active/all blocks"><span class="checkbox-icon size-16 icon-enabled-all on"></span></label>
      </span>
      <span class="ai-toolbar-button small" style="vertical-align: middle; float: right;">
        <input type="checkbox" value="0" id="ai-rearrange" style="display: none;" />
        <label class="checkbox-button" for="ai-rearrange" title="Rearrange block order"><span class="checkbox-icon size-16 icon-rearrange"></span></label>
      </span>
      <span id='list-rearrange-controls' class="ai-toolbar-button text" style='float: right; display: none;'>
        <span id='list-save' class="ai-toolbar-button text" style="vertical-align: middle; display: none;">
          <input type="checkbox" value="0" id="ai-save-changes" style="display: none;" />
          <label class="checkbox-button" for="ai-save-changes" style="padding: 1px 5px 1px 4px; margin-top: 0px;" title="Save new block order">Save Changes</label>
        </span>
      </span>
      <div style="clear: both;"></div>
    </div>

    <div id="ai-list-data">
      Loading...
    </div>
  </div>
<?php
}

if (defined ('AI_ADSENSE_API')) {

function adsense_list_container () {
?>
  <div id="adsense-list-container" class="ai-form rounded" style="background: #fff; display: none;">
    <div id='adsense-list-controls' class='ui-widget' style='margin: 0 auto 8px; display: none;'>
      <span style="vertical-align: middle; float: left;">
        <input id="adsense-list-search" type="text" value="" size="40" maxlength="40" />
      </span>

      <span class="ai-toolbar-button small" style="vertical-align: middle; float: right;">
        <input type="checkbox" value="0" id="adsense-load-all" style="display: none;" />
        <label class="checkbox-button" for="adsense-load-all" title="Toggle active/all ad units"><span class="checkbox-icon size-16 icon-enabled-all on"></span></label>
      </span>
      <span class="ai-toolbar-button small" style="vertical-align: middle; float: right;">
        <input type="checkbox" value="0" id="adsense-reload" style="display: none;" />
        <label class="checkbox-button" for="adsense-reload" title="Reload AdSense ad units"><span class="checkbox-icon size-16w icon-reload"></span></label>
      </span>
      <span class="ai-toolbar-button small" style="vertical-align: middle; float: right;">
          <input type="checkbox" value="" id="clear-adsense-authorization" style="display: none;" />
          <label class="checkbox-button" for="clear-adsense-authorization" title="Clear authorization to access AdSense account"><span class="list-button lb-size-16 ui-icon ui-icon-power"></span></label>
      </span>
      <span class="ai-toolbar-button small" style="vertical-align: middle; float: right;">
        <input type="checkbox" value="0" id="google-adsense" style="display: none;" />
        <label class="checkbox-button" id="google-adsense-button" for="google-adsense" title="Google AdSense Home" onclick="window.open('https://www.google.com/adsense/login')" ><span class="checkbox-icon size-img16 icon-adsense"></span></label>
      </span>
      <div style="clear: both;"></div>
    </div>

    <div id="adsense-list-data">
      Loading...
    </div>
  </div>
<?php
}

}


function code_block_list () {
  global $block_object, $wpdb, $ai_db_options_extract;

  if (isset ($_GET ["blocks-org"]) && isset ($_GET ["blocks-new"])) {
    $blocks_org = json_decode ($_GET ["blocks-org"]);
    $blocks_new = json_decode ($_GET ["blocks-new"]);

    if (!empty ($blocks_org) && count ($blocks_org) == count ($blocks_new)) {
      // Uodate widgets
      $current_options = get_option (AI_OPTION_NAME);
      $new_options     = $current_options;

      $error = false;
      foreach ($blocks_org as $index => $block) {
        $new_block = $blocks_new [$index];
        if ($block >= 1 && $block <= AD_INSERTER_BLOCKS && $new_block >= 1 && $new_block <= AD_INSERTER_BLOCKS) {
          $new_options [$block] = $current_options [$new_block];
        } else $error = true;
      }

      if (!$error) {
        // Update AI_OPTION_FALLBACK and AI_OPTION_ADB_BLOCK_REPLACEMENT
        for ($block = 1; $block <= AD_INSERTER_BLOCKS; $block ++) {
          if (isset ($new_options [$block][AI_OPTION_FALLBACK])) {
            $ai_option_fallback = $new_options [$block][AI_OPTION_FALLBACK];
            if ($ai_option_fallback != '')
              foreach ($blocks_new as $index => $org_block) {
                if ($ai_option_fallback == $org_block) {
                  $new_options [$block][AI_OPTION_FALLBACK] = $blocks_org [$index];
                }
              }
          }

          if (isset ($new_options [$block][AI_OPTION_ADB_BLOCK_REPLACEMENT])) {
            $ai_option_adb_block_replacement = $new_options [$block][AI_OPTION_ADB_BLOCK_REPLACEMENT];
            if ($ai_option_adb_block_replacement != '')
              foreach ($blocks_new as $index => $org_block) {
                if ($ai_option_adb_block_replacement == $org_block) {
                  $new_options [$block][AI_OPTION_ADB_BLOCK_REPLACEMENT] = $blocks_org [$index];
                }
              }
          }
        }

        update_option (AI_OPTION_NAME, $new_options);
        ai_load_settings ();

        $new_options [AI_OPTION_EXTRACT] = ai_generate_extract ($new_options);
        $ai_db_options_extract = $new_options [AI_OPTION_EXTRACT];

        $new_options [AI_OPTION_GLOBAL]['VIEWPORT_CSS']  = generate_viewport_css ();
        $new_options [AI_OPTION_GLOBAL]['ALIGNMENT_CSS'] = generate_alignment_css ();

        $new_options [AI_OPTION_GLOBAL]['TIMESTAMP'] = time ();

        update_option (AI_OPTION_NAME, $new_options);
        ai_load_settings ();


        $ai_widgets = get_option ('widget_ai_widget');
        if (is_array ($ai_widgets))
          foreach ($ai_widgets as $widget_index => $ai_widget) {
            if (isset ($ai_widget ['block'])) {
              $widget_block = $ai_widget ['block'];
              if ($widget_block >= 1 && $widget_block <= AD_INSERTER_BLOCKS) {
                foreach ($blocks_new as $index => $org_block) {
                  if ($widget_block == $org_block) {
                    $ai_widgets [$widget_index]['block'] = $blocks_org [$index];
                    break;
                  }
                }
              }
            }
          }
        update_option ('widget_ai_widget', $ai_widgets);

        if (defined ('AI_STATISTICS') && AI_STATISTICS) {
          // Update statistics - two passes to avoid duplicate entries

          $offset = 1000;

          // Lock table to prevent updates of old blocks
          $query  = 'LOCK TABLES ' . AI_STATISTICS_DB_TABLE . ' WRITE;';
          $update = $wpdb->query ($query);

          // Pass 1 - new blocks with offset
          $query  = 'UPDATE ' . AI_STATISTICS_DB_TABLE . ' SET block= CASE ';
          foreach ($blocks_new as $index => $org_block) {
            $new_block = $blocks_org [$index] + $offset;
            $query .= "WHEN block= $org_block THEN $new_block ";
          }
          $query .= 'ELSE block END;';
          $update = $wpdb->query ($query);

          // Pass 2 - remove offset
          $query  = 'UPDATE ' . AI_STATISTICS_DB_TABLE . " SET block = block - $offset WHERE block >= $offset;";
          $update = $wpdb->query ($query);

          // Unlock table
          $query  = 'UNLOCK TABLES;';
          $update = $wpdb->query ($query);
        }
      }
    }
  }

  $sidebars_with_widget = get_sidebar_widgets ();

  ob_start ();

  $search_text = trim ($_GET ["list"]);

  $show_all_blocks = isset ($_GET ["all"]) && $_GET ["all"];

  $start = trim ($_GET ["start"]);
  $end   = trim ($_GET ["end"]);

  if ($search_text != '') $search_array = explode (' ', $search_text); else $search_array = array ();

  $blocks = array ();
  $row_counter = 0;
  for ($block = 1; $block <= AD_INSERTER_BLOCKS; $block ++) {
    $obj = $block_object [$block];

    $automatic_insertion  = $obj->get_automatic_insertion () != AI_AUTOMATIC_INSERTION_DISABLED;

    $manual_widget        = $obj->get_enable_widget()    == AI_ENABLED;
    $manual_shortcode     = $obj->get_enable_manual()    == AI_ENABLED;
    $manual_php_function  = $obj->get_enable_php_call()  == AI_ENABLED;

    $block_used = $automatic_insertion || $manual_php_function || $manual_shortcode || $manual_widget && !empty ($sidebars_with_widget [$block]);

    if (!$show_all_blocks && !$block_used) continue;

    $block_text = $block . ' '. $obj->wp_options [AI_OPTION_BLOCK_NAME] . ' ' . $obj->get_automatic_insertion_text() . ' ' . $obj->get_alignment_type_text () . ' ' . implode (', ', $sidebars_with_widget [$block]);
    if (!empty ($sidebars_with_widget [$block])) $block_text .= ' widget';
    if ($manual_shortcode) $block_text .= ' shortcode';
    if ($manual_php_function) $block_text .= ' php';

    foreach ($search_array as $search_item) {
      if (stripos ($block_text, trim ($search_item)) === false) continue 2;
    }

    $blocks []= $block;
    $row_counter ++;
    $row_color = $row_counter % 2 == 0 ? '#eee' : '#fff';

    if (function_exists ('ai_settings_url_parameters')) $url_parameters = ai_settings_url_parameters ($block); else $url_parameters = "";
    $edit_url = admin_url ('options-general.php?page=ad-inserter.php') . $url_parameters . '&tab=' . $block;

    $visible_tab = $block >= $start && $block <= $end;

?>
        <tr style="background: <?php echo $row_color; ?>" data-block="<?php echo $block; ?>">
          <td style="min-width: 55px; color: <?php echo $block_used ? '#444' : '#ccc'; ?>;">
            <span class="ai-list-button">
              <label class="checkbox-button ai-copy-block" style="margin-top: -1px;" title="Copy block"><span class="checkbox-icon size-8"></span></label>
            </span>
            <span class="ai-list-button">
              <label class="checkbox-button ai-preview-block" style="margin-top: -1px;" title="Preview block"><span class="checkbox-icon size-8 icon-preview"></span></label>
            </span>
            <span  class="ai-list-button" style="text-align: right; width: 16px;"><?php echo $block; ?></span>
          </td>
<?php if ($visible_tab): ?>
          <td class="ai-tab-link" data-tab="<?php echo $block; ?>" style=" min-width: 120px; color: #0073aa; cursor: pointer; text-align: left; padding-left: 5px; max-width: 220px; white-space: nowrap; overflow: hidden;"><?php echo $obj->wp_options [AI_OPTION_BLOCK_NAME]; ?></td>
<?php else: ?>
          <td style="min-width: 120px; text-align: left;  padding-left: 5px; max-width: 280px; white-space: nowrap; overflow: hidden;"><a href="<?php echo $edit_url; ?>" style="text-decoration: none; box-shadow: 0 0 0;"><?php echo $obj->wp_options [AI_OPTION_BLOCK_NAME]; ?></a></td>
<?php endif ?>
          <td style="min-width: 180px; text-align: left; padding-left: 10px; max-width: 130px; white-space: nowrap; overflow: hidden; color: <?php echo $automatic_insertion ? '#666' : '#ccc'; ?>"><?php echo $obj->get_automatic_insertion_text(); ?></td>
          <td style="min-width: 110px; text-align: left; padding-left: 10px; max-width: 80px; white-space: nowrap; overflow: hidden; color: <?php echo $block_used ? '#444' : '#ccc'; ?>"><?php echo $obj->get_alignment_type_text (); ?></td>
          <td class="ai-dot" style="min-width: 15px; text-align: center; padding-left: 10px; vertical-align: top; color: <?php echo $manual_php_function ? '#8080ff' : '#ddd'; ?>;">&#9679;</td>
          <td class="ai-dot" style="min-width: 15px; text-align: center; padding-left: 10px; vertical-align: top; color: <?php echo $manual_shortcode ? '#ff8b8b' : '#ddd'; ?>;">&#9679;</td>
          <td class="ai-dot" style="min-width: 15px; text-align: center; padding-left: 10px; vertical-align: top; color: <?php echo $manual_widget ? (count ($sidebars_with_widget [$block]) ? '#7cda7c' : '#666') : '#ddd'; ?>;">&#9679;</td>
          <td style="text-align: left; padding-left: 10px; max-width: 160px; white-space: nowrap; overflow: hidden; color: <?php echo $manual_widget ? '#666' : '#ccc'; ?>;"><?php echo implode (', ', $sidebars_with_widget [$block]); ?></td>
        </tr>
<?php
  }
  $table_rows = ob_get_clean ();
?>

    <table id="ai-list-table" class="exceptions ai-sortable" cellspacing=0 cellpadding=0 style="width: 100%;" data-blocks="<?php echo json_encode ($blocks); ?>">
      <thead>
        <tr>
          <th style="text-align: left;">Block</th>
          <th style="text-align: left; padding-left: 5px;">Name</th>
          <th style="text-align: left; padding-left: 10px;">Automatic Insertion</th>
          <th style="text-align: left; padding-left: 10px;">Alignment</th>
          <th style="text-align: center; padding-left: 10px;" title="PHP function call">P</th>
          <th style="text-align: center; padding-left: 10px;" title="Shortcode">S</th>
          <th style="text-align: center; padding-left: 10px;" title="Widget">W</th>
          <th style="text-align: left; padding-left: 10px;">Widget Positions</th>
        </tr>
      </thead>
      <tbody>
<?php echo $table_rows; ?>
      </tbody>
    </table>
<?php
  if ($row_counter == 0) {
    if ($search_text == '')
      echo "<div style='margin: 10px 0 0 20px;'>No active code block</div>"; else
        echo "<div style='margin: 10px 0 0 20px;'>No code block matches search keywords</div>";
  }
}

if (defined ('AI_ADSENSE_API')) {

function ai_adsense_data (&$error) {
  require_once AD_INSERTER_PLUGIN_DIR.'includes/adsense-api.php';

  $error = 'AdSense not authorized';
  $ad_data = false;

  if (defined ('AI_ADSENSE_AUTHORIZATION_CODE')) {
    $error = '';

    $update_ad_units = isset ($_GET ["update_ad_units"]) ? $_GET ["update_ad_units"] ==  1 : false;

    $adsense = new adsense_api();

    $ad_data = get_transient (AI_TRANSIENT_ADSENSE_ADS);

    if ($ad_data === false || $update_ad_units) {
      $ad_units = $adsense->getAdUnits();
      $error    = $adsense->getError ();
      if ($error == '' && is_array ($ad_units)) {
        $ad_data = array ($adsense->getAdSensePublisherID (), $ad_units);
        set_transient (AI_TRANSIENT_ADSENSE_ADS, $ad_data, AI_TRANSIENT_ADSENSE_ADS_EXPIRATION);
      }
    }
  }

  return $ad_data;
}

function adsense_list () {
  require_once AD_INSERTER_PLUGIN_DIR.'includes/adsense-api.php';

  if (defined ('AI_ADSENSE_AUTHORIZATION_CODE')) {

    $publisher_id = '';
    $ad_units = array ();
    $error = '';

    $ad_data = ai_adsense_data ($error);

    if ($error == '') {

      $publisher_id = $ad_data [0];
      $ad_units     = $ad_data [1];

      $show_all_ad_units = isset ($_GET ["all"]) && $_GET ["all"];
?>

    <table id="ai-adsense-ad-units-table" class="exceptions" cellspacing=0 cellpadding=0 style="width: 100%;">
      <thead>
        <tr>
          <th style="text-align: left; width: 66px;">Ad unit</th>
          <th style="text-align: left;">Name</th>
          <th style="text-align: left;">Slot ID</th>
          <th style="text-align: left;">Type</th>
          <th style="text-align: left;">Size</th>
        </tr>
      </thead>
      <tbody>
<?php
      $row_counter = 0;
      foreach ($ad_units as $ad_unit) {

        if (!$show_all_ad_units && !$ad_unit ['active']) continue;

        $search_text = trim ($_GET ["adsense-list"]);
        if ($search_text != '') $search_array = explode (' ', $search_text); else $search_array = array ();
        $block_text = $ad_unit ['name'] . ' ' . $ad_unit ['code'] . ' ' . $ad_unit ['type'] . ' ' . $ad_unit ['size'];
        foreach ($search_array as $search_item) {
          if (stripos ($block_text, trim ($search_item)) === false) continue 2;
        }

        $row_counter ++;
        $row_color = $row_counter % 2 == 0 ? '#eee' : '#fff';

?>
        <tr style="background: <?php echo $row_color; ?>" data-id="ca-<?php echo $publisher_id, ':', $ad_unit ['code']; ?>" data-name="<?php echo base64_encode ($ad_unit ['name']); ?>">
          <td>
            <span class="ai-list-button">
              <label class="checkbox-button adsense-copy-code" style="margin-top: -1px;" title="Copy AdSense code"><span class="checkbox-icon size-8"></span></label>
            </span>
            <span class="ai-list-button">
              <label class="checkbox-button adsense-preview-code" style="margin-top: -1px;" title="Preview AdSense ad"><span class="checkbox-icon size-8 icon-preview"></span></label>
            </span>
            <span class="ai-list-button">
              <label class="checkbox-button adsense-get-code" style="margin-top: -1px;" title="Get AdSense code"><span class="checkbox-icon size-8 icon-get"></span></label>
            </span>
          </td>
          <td style="color: <?php echo $ad_unit ['active'] ? '#444' : '#ccc'; ?>;">
            <?php echo $ad_unit ['name']; ?>
          </td>
          <td class="select" style="text-align: left; color: <?php echo $ad_unit ['active'] ? '#444' : '#ccc'; ?>;">
            <span><?php echo $ad_unit ['code']; ?></span>
          </td>
          <td style="color: <?php echo $ad_unit ['active'] ? '#444' : '#ccc'; ?>;">
            <?php echo ucwords (strtolower (str_replace ('_', ', ', $ad_unit ['type']))); ?>
          </td>
          <td style="color: <?php echo $ad_unit ['active'] ? '#444' : '#ccc'; ?>;">
            <?php echo ucwords (strtolower ($ad_unit ['size'])); ?>
          </td>
        </tr>
<?php
     }
?>
      </tbody>
    </table>

    <div id="adsense-data" style="display: none;" data-publisher-id="<?php echo $publisher_id; ?>"></div>

<?php
    } else echo "<div style='margin: 10px 0 0 20px;'>$error</div>";

  }

  elseif (defined ('AI_ADSENSE_CLIENT_ID')) {
      $adsense = new adsense_api();
?>
    <table class="responsive-table" cellspacing=0 cellpadding=0 style="width: 100%;">
      <tbody>
        <tr>
          <td colspan="2" style="white-space: inherit;">
            <div class="rounded" style="margin: 0;">
              <h2 style="margin: 5px 0; float: left;"><strong><?php echo AD_INSERTER_NAME; ?></strong> AdSense Integration - Step 2</h2>
              <a href="https://www.google.com/adsense/login" class="simple-link" style="float: right;" target="_blank" title="Google AdSense Home"><img src="<?php echo AD_INSERTER_PLUGIN_IMAGES_URL; ?>ga-logo.png" style="margin: 3px 0 -4px 0;"/></a>
              <div style="clear: both;"></div>
            </div>
            <p style="text-align: justify;">Now you can authorize <?php echo AD_INSERTER_NAME; ?> to access your AdSense account. Click on the <strong>Get Authorization Code</strong> button to open a new window where you can allow access.
            When you get the code copy it to the field below and click on the button <strong>Authorize</strong>.</p>
            <p style="text-align: justify;">If you get error <strong>invalid client</strong> click on the button <strong>Clear and return to Step 1</strong> to re-enter Client ID and Client Secret.</p>
          </td>
        </tr>
        <tr>
          <td style="padding-right: 10px;">
            <button type="button" class="ai-top-button" style="display: none; width: 162px; outline-color: transparent;" onclick="window.open('<?php echo $adsense->getAuthUrl (); ?>')">Get Authorization Code</button>
          </td>
          <td>
            <input id="adsense-authorization-code" style="width: 100%;" type="text" value="" size="100" maxlength="200" title="Enter Authorization Code"/>
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>
            <button type="button" class="ai-top-button authorize-adsense clear-adsense" style="display: none; float: left; width: 162px; outline-color: transparent;">Clear and return to Step 1</button>
          </td>
          <td>
            <button type="button" class="ai-top-button authorize-adsense" style="display: none; float: right; width: 162px; outline-color: transparent;">Authorize</button>
          </td>
        </tr>
      </tbody>
    </table>

<?php
  }

  else {
?>
    <table class="responsive-table" cellspacing=0 cellpadding=0 style="width: 100%;">
      <tbody>
        <tr>
          <td colspan="2" style="white-space: inherit;">
            <div class="rounded" style="margin: 0;">
              <h2 style="margin: 5px 0; float: left;"><strong><?php echo AD_INSERTER_NAME; ?></strong> AdSense Integration - Step 1</h2>
              <a href="https://www.google.com/adsense/login" class="simple-link" style="float: right;" target="_blank" title="Google AdSense Home"><img src="<?php echo AD_INSERTER_PLUGIN_IMAGES_URL; ?>ga-logo.png" style="margin: 3px 0 -4px 0;"/></a>
              <div style="clear: both;"></div>
            </div>
            <p style="text-align: justify;">Here can <strong><?php echo AD_INSERTER_NAME; ?></strong> list configured AdSense ad units and get code for AdSense ads.
            To do this you need to authorize <?php echo AD_INSERTER_NAME; ?> to access your AdSense account. The first step is to create a Google API project in order to get Client ID and Client Secret.</p>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="white-space: inherit;">
            <ol>
              <li>Go to <a href="https://console.developers.google.com/" target="_blank">Google APIs and Services console</a></li>
              <li>Create <strong>Ad Inserter</strong> project - if the project and IDs are already created click on the <strong>Credentials</strong> in the sidebar and go to step 16</li>
              <li>Click on <strong>Select a project</strong> selection and then click on the <strong>+</strong> button to create a new project</li>
              <li>Enter <strong>Ad Inserter</strong> for project name and click on the <strong>Create</strong> button</li>
              <li>Click on project selection, wait for the project to be created and then and select <strong>Ad Inserter</strong> as the current project</li>
              <li>Click on <strong>ENABLE APIS AND SERVICES</strong></li>
              <li>Search for adsense and enable <strong>AdSense Management API</strong></li>
              <li>Click on <strong>Create credentials</strong></li>
              <li>For <strong>Where will you be calling the API from?</strong> select <strong>Other UI</strong></li>
              <li>For <strong>What data will you be accessing?</strong> select <strong>User data</strong></li>
              <li>Click on <strong>What credentials do I need?</strong></li>
              <li>Create an OAuth 2.0 client ID: For <strong>OAuth 2.0 client ID</strong> name enter <strong>Ad Inserter client</strong></li>
              <li>Set up the OAuth 2.0 consent screen: For <strong>Product name shown to users</strong> enter <strong>Ad Inserter</strong></li>
              <li>Click on <strong>Continue</strong></li>
              <li>Click on <strong>Done</strong></li>
              <li>Click on <strong>Ad Inserter client</strong> to get <strong>Client ID</strong> and <strong>Client secret</strong></li>
              <li>Copy them to the appropriate fields below</li>
            </ol>
          </td>
        </tr>
        <tr>
          <td style="padding-right: 10px;">
            Client ID
          </td>
          <td>
            <input id="adsense-client-id" style="width: 100%;" type="text" value="" size="100" maxlength="200" title="Client ID"/>
          </td>
        </tr>
        <tr>
          <td style="padding-right: 10px;">
            Client secret
          </td>
          <td>
            <input id="adsense-client-secret" style="width: 100%;" type="text" value="" size="100" maxlength="200" title="Enter Client secret"/>
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td></td>
          <td>
            <button type="button" id="save-client-ids" class="ai-top-button" style="display: none; float: right; width: 162px; outline-color: transparent;">Save</button>
          </td>
        </tr>
      </tbody>
    </table>

<?php
  }

}

}

function ai_adsense_code ($ad_slot_id) {
  if (defined ('AI_ADSENSE_API')) {
    require_once AD_INSERTER_PLUGIN_DIR.'includes/adsense-api.php';

    if (defined ('AI_ADSENSE_AUTHORIZATION_CODE')) {
      $adsense = new adsense_api();
      $code = $adsense->getAdCode ($ad_slot_id);
      echo json_encode (array ('code' => $code, 'error-message' => $adsense->getError ()));
    }
  }
}

function adsense_ad_name ($adsense_data) {
  if (defined ('AI_ADSENSE_API')) {
    $publisher_id = '';
    $ad_units = array ();
    $error = '';

    $ad_data = ai_adsense_data ($error);

    if ($error == '') {
      $publisher_id = $ad_data [0];
      $ad_units     = $ad_data [1];
      $ad_slot_names = array ('publisher_id' => $publisher_id);

      foreach ($ad_units as $ad_unit) {
        if ($ad_unit ['active'])
          $ad_slot_names [$ad_unit ['code']] = $ad_unit ['name'];

      }
      echo json_encode ($ad_slot_names);
    }
  }
}

function generate_list_options ($options) {
  $max_items = 2000;

  switch ($options) {
    case 'category':
      $category_data = get_categories ();
      $category_data = array_slice ($category_data, 0, $max_items);
      foreach ($category_data as $category) {
        echo "              <option value='{$category->slug}'>{$category->slug} ({$category->name})</option>\n";
      }
      break;

    case 'tag':
      $tag_data = get_tags ();
      $tag_data = array_slice ($tag_data, 0, $max_items);
      foreach ($tag_data as $tag) {
        echo "              <option value='{$tag->slug}'>{$tag->slug} ({$tag->name})</option>\n";
      }
      break;

    case 'taxonomy':
      $term_data = get_terms ();
      $taxonomies = array ();
      foreach ($term_data as $term) {
        if ($term->taxonomy == 'category') continue;
        if ($term->taxonomy == 'post_tag') continue;
        $taxonomies [strtolower ($term->taxonomy) . ':' . strtolower ($term->slug)] = $term->name;
        if (count ($taxonomies) >= $max_items) break;
      }

      $args = array (
        'public'    => true,
    //    '_builtin'  => false,
      );
      $custom_post_types = get_post_types ($args, 'objects', 'and');
      foreach ($custom_post_types as $custom_post_type => $custom_post_data) {
        $taxonomies ['post-type:' . strtolower ($custom_post_type)] = $custom_post_data->labels->singular_name;
      }

      $editable_roles = get_editable_roles ();
      foreach ($editable_roles as $editable_role_slug => $editable_role) {
        $taxonomies ['user-role:' . strtolower ($editable_role_slug)] = $editable_role ['name'];
      }

      $users = get_users ();
      foreach ($users as $user) {
        $taxonomies ['user:'   . strtolower ($user->data->user_login)] = $user->data->display_name;
        $taxonomies ['author:' . strtolower ($user->data->user_login)] = $user->data->display_name;
        if (count ($taxonomies) >= $max_items) break;
      }

      ksort ($taxonomies);

      foreach ($taxonomies as $taxonomy => $taxonomy_name) {
        if ($taxonomy_name != '')
          echo "              <option value='{$taxonomy}'>{$taxonomy} ({$taxonomy_name})</option>\n"; else
            echo "              <option value='{$taxonomy}'>{$taxonomy}</option>\n";
      }
      break;

    case 'id':
      $args = array (
        'public'    => true,
        '_builtin'  => false
      );
      $custom_post_types = get_post_types ($args, 'names', 'and');
      $screens = array_values (array_merge (array ('post', 'page'), $custom_post_types));

      $args = array (
        'posts_per_page'   => 3 * $max_items,
        'offset'           => 0,
        'category'         => '',
        'category_name'    => '',
        'orderby'          => 'ID',
        'order'            => 'ASC',
        'include'          => '',
        'exclude'          => '',
        'meta_key'         => '',
        'meta_value'       => '',
        'post_type'        => $screens,
        'post_mime_type'   => '',
        'post_parent'      => '',
        'author'           => '',
        'author_name'      => '',
        'post_status'      => '',
        'suppress_filters' => true,
      );
      $posts_pages = get_posts ($args);

      $counter = 0;
      foreach ($posts_pages as $post_page) {
        if ($post_page->post_title == '') continue;
        echo "              <option value='{$post_page->ID}'>{$post_page->ID} ({$post_page->post_type} \"{$post_page->post_title}\")</option>\n";
        $counter ++;
        if ($counter >= $max_items) break;
      }
      break;

    default:
      if (function_exists ('ai_generate_list_options')) ai_generate_list_options ($options);
      break;
  }
}


function sidebar_addense_alternative () { ?>

      <div class="ai-form header rounded">
        <div style="float: left;">
          <h2 style="display: inline-block; margin: 5px 0;">Blank Ad Blocks? Looking for AdSense alternative?</h2>
        </div>
        <div style="clear: both;"></div>
      </div>

      <div class="ai-form rounded" style="height: 90px; padding: 8px 4px 8px 12px;">
        <a href='http://bit.ly/2oF81Oh' class="clear-link" title="Looking for AdSense alternative?" target="_blank"><img id="ai-media-1" src="<?php echo AD_INSERTER_PLUGIN_IMAGES_URL; ?>contextual-1.gif" /></a>
      </div>

<?php
}

function sidebar_support_review () {
  if (!wp_is_mobile () && is_super_admin ()) {
?>
      <div class="ai-form header no-select rounded" style="position: relative; text-align: justify;">
        You've been using <strong>Ad Inserter</strong> for a while now, and I hope you're happy with it.
        Positive <a href="https://wordpress.org/support/plugin/ad-inserter/reviews/" style="text-decoration: none; box-shadow: 0 0 0;" target="_blank">reviews</a> are a great way to show your appreciation for my work.
        Besides being an incredible boost to my morale, they are also a great incentive to fix bugs and to add new features for better monetization of your website.
        Thank you! Igor <img draggable="false" class="emoji" alt="happy" src="https://s.w.org/images/core/emoji/2.3/svg/1f642.svg" style="margin-left: 5px!important;">
      </div>

<?php
  }
}

function sidebar_support_plugin () {
  global $rating_value, $rating_string, $rating_css;
?>

      <div class="ai-form header rounded no-select">
        <div style="float: left;">
          <h2 style="display: inline-block; margin: 7px 0;">Support plugin development</h2>
        </div>

        <div style="float: right;">
          <a href="https://twitter.com/AdInserter" class="clear-link" target="_blank"><img id="ai-tw" src="<?php echo AD_INSERTER_PLUGIN_IMAGES_URL; ?>twitter.png" style="vertical-align: middle; margin: 0 0 0 20px;" title="Ad Inserter on Twitter" alt="Ad Inserter on Twitter" /></a>
          <a href="https://www.facebook.com/AdInserter/" class="clear-link" target="_blank"><img id="ai-fb" src="<?php echo AD_INSERTER_PLUGIN_IMAGES_URL; ?>facebook.png" style="vertical-align: middle; margin: 0 0 0 10px;" title="Ad Inserter on Facebook" alt="Ad Inserter on Facebook" /></a>
        </div>
        <div style="float: right; margin-top: 2px;">
          <h2 style="display: inline-block; margin: 5px 0;">Follow Ad Inserter</h2>
        </div>

        <div style="float: right; margin: 8px 20px 0 0;">
            <div id="ai-stars" style="float: right; margin: 0 0 -3px 0; cursor: pointer; font-size: 11px;"><span><?php //echo $rating_value; ?></span><img id="ai-stars-img" src="<?php echo AD_INSERTER_PLUGIN_IMAGES_URL; ?>stars.png" style="margin: 0 0 -3px 10px;"/></div>

            <div id="ai-rating-bar" class="header" style="float: right; cursor: pointer; margin: 3px 0 0 0; width: 148px; display: none;" nonce="<?php echo wp_create_nonce ("adinserter_data"); ?>" site-url="<?php echo wp_make_link_relative (get_site_url()); ?>">
              <div class="header" style="background: #ccc;" title="Average rating of the plugin - Thank you!">
                <a href="https://wordpress.org/support/plugin/ad-inserter/reviews/" style="text-decoration: none; box-shadow: 0 0 0;" target="_blank">
                  <div id="rating-value" style="text-align: center; font-size: 11px; line-height: 12px; border-radius: 2px; background: #fddf87; height: 100%; <?php echo $rating_css; ?>"><span style=""><?php echo $rating_string; ?></span></div>
                </a>
              </div>
            </div>
        </div>

        <div style="clear: both;"></div>
      </div>

<?php
}

function sidebar_help () { ?>

      <div class="ai-form header rounded ai-help">
        <div style="float: left;">
          <h2 style="display: inline-block; margin: 5px 0;">
            Need help with settings?
            For quick start check <a href="https://adinserter.pro/code-editing" style="text-decoration: none; box-shadow: 0 0 0;" target="_blank">Code Editing</a> and
            <a href="https://adinserter.pro/settings" style="text-decoration: none; box-shadow: 0 0 0;" target="_blank">Common Settings</a>
          </h2>
          <div>
            <strong>New to <a href="https://adinserter.pro/adsense-ads" style="text-decoration: none; box-shadow: 0 0 0;" target="_blank">AdSense</a>?</strong>
            <a href="https://adinserter.pro/adsense-ads#connect-your-site" style="text-decoration: none; box-shadow: 0 0 0;" target="_blank">Connect your site</a> - Advanced
            <a href="https://adinserter.pro/adsense-ads#ad-code" style="text-decoration: none; box-shadow: 0 0 0;" target="_blank">AdSense code</a>:
            <a href="https://adinserter.pro/adsense-ads#in-feed-ads" style="text-decoration: none; box-shadow: 0 0 0;" target="_blank">In-feed ads</a>,
            <a href="https://adinserter.pro/adsense-ads#auto-ads" style="text-decoration: none; box-shadow: 0 0 0;" target="_blank">Auto ads</a>,
            <a href="https://adinserter.pro/adsense-ads#amp" style="text-decoration: none; box-shadow: 0 0 0;" target="_blank">AMP ads</a>, optional
            <a href="https://adinserter.pro/adsense-ads#integration" style="text-decoration: none; box-shadow: 0 0 0;" target="_blank">AdSense Integration</a>
          </div>
          <hr />
          <div>Ads are not showing? Check <a href="https://adinserter.pro/adsense-ads#ads-not-displayed" style="text-decoration: none; box-shadow: 0 0 0;" target="_blank">troubleshooting guide</a> to find out how to diagnose and fix the problem.</div>
          <div>If you need any kind of help or support, please do not hesitate to open a thread on the <a href="https://wordpress.org/support/plugin/ad-inserter/" style="text-decoration: none; box-shadow: 0 0 0;" target="_blank">support forum</a>.</div>
        </div>
        <div style="clear: both;"></div>
      </div>

<?php
}

function sidebar_pro () {
  $version = rand (0, 1);
?>

      <div class="ai-form rounded no-select feature-list" style="background: #fff;">
        <div style="float: right;" >
          <div>
<?php switch ($version) {
        case 0: ?>
            <a href="http://adinserter.pro/" class="clear-link" title="Automate ad placement on posts and pages" target="_blank"><img id="ai-pro-1" src="<?php echo AD_INSERTER_PLUGIN_IMAGES_URL; ?>icon-256x256.jpg" style="margin-top: 10px;" /></a>
<?php   break; case 1: ?>
            <a href='http://bit.ly/2oF81Oh' class="clear-link" title="Looking for AdSense alternative?" target="_blank"><img id="ai-media-2" src="<?php echo AD_INSERTER_PLUGIN_IMAGES_URL; ?>contextual-2.jpg" style="margin-top: 10px;" /></a>
<?php   break;
      } ?>
          </div>
          <div>
<?php switch ($version) {
        case 0: ?>
            <a href="https://adinserter.pro/tracking" class="clear-link" title="A/B testing - Track ad impressions and clicks" target="_blank"><img id="ai-pro-2" src="<?php echo AD_INSERTER_PLUGIN_IMAGES_URL; ?>ai-charts-250.png" style="margin-top: 10px;" /></a>
<?php   break; case 1: ?>
            <a href="http://adinserter.pro/" class="clear-link" title="Automate ad placement on posts and pages" target="_blank"><img id="ai-pro-1" src="<?php echo AD_INSERTER_PLUGIN_IMAGES_URL; ?>icon-256x256.jpg" style="margin-top: 10px;" /></a>
<?php   break;
      } ?>
          </div>
          <div>
<?php switch ($version) {
        case 0: ?>
            <a href='http://bit.ly/2oF81Oh' class="clear-link" title="Looking for AdSense alternative?" target="_blank"><img id="ai-media-2" src="<?php echo AD_INSERTER_PLUGIN_IMAGES_URL; ?>contextual-2.jpg" style="margin-top: 10px;" /></a>
<?php   break; case 1: ?>
            <a href="https://adinserter.pro/lists#geo-targeting" class="clear-link" title="Geotargeting - black/white-list countries" target="_blank"><img id="ai-pro-3" src="<?php echo AD_INSERTER_PLUGIN_IMAGES_URL; ?>ai-countries-250.png" style="margin-top: 10px;" /></a>
<?php   break;
      } ?>
          </div>
        </div>

        <h3 style="text-align: justify;">Looking for Pro Ad Management plugin?</h3>
        <h4 style="text-align: justify;">To Optimally Monetize your WordPress website?</h4>

        <ul>
          <li>64 code (ad) blocks</li>
          <li><a href="https://adinserter.pro/adsense-ads#integration" class="simple-link" target="_blank">AdSense Integration</a></li>
          <li>Syntax highlighting <a href="https://adinserter.pro/code-editing" class="simple-link" target="_blank">editor</a></li>
          <li><a href="http://adinserter.pro/documentation#code-preview" class="simple-link" target="_blank">Code preview</a> with visual CSS editor</li>
          <li>Simple user interface - all settings on a single page</li>
          <li><a href="http://adinserter.pro/documentation#automatic-insertion" class="simple-link" target="_blank">Automatic insertion</a> before or after post / content / <a href="http://adinserter.pro/documentation#paragraphs" class="simple-link" target="_blank">paragraph</a> / excerpt</li>
          <li><a href="http://adinserter.pro/documentation#automatic-insertion" class="simple-link" target="_blank">Automatic insertion</a> between posts on blog pages</li>
          <li><a href="http://adinserter.pro/documentation#automatic-insertion" class="simple-link" target="_blank">Automatic insertion</a> before, between and after comments</li>
          <li><a href="http://adinserter.pro/documentation#automatic-insertion" class="simple-link" target="_blank">Automatic insertion</a> below <code>&lt;body&gt;</code> or above <code>&lt;/body&gt;</code> tags</li>
          <li>Automatic insertion at <a href="https://adinserter.pro/documentation#custom-hooks" class="simple-link" target="_blank">custom hook positions</a></li>
          <li>Automatic insertion before or after any HTML element on page</li>
          <li><a href="https://adinserter.pro/exceptions" class="simple-link" target="_blank">Insertion exceptions</a> for individual posts and pages</li>
          <li><a href="http://adinserter.pro/documentation#manual-insertion" class="simple-link" target="_blank">Manual insertion</a>: widgets, shortcodes, PHP function call</li>
          <li><a href="https://adinserter.pro/alignments-and-styles" class="simple-link" target="_blank">Sticky positions</a> (left, top, right, bottom - ads stay fixed when the page scrolls)</li>
          <li><a href="http://adinserter.pro/documentation#manual-insertion" class="simple-link" target="_blank">Sticky (fixed) widgets</a> (sidebar does not move when the page scrolls)</li>
          <li>Block <a href="https://adinserter.pro/alignments-and-styles" class="simple-link" target="_blank">alignment and style</a> customizations</li>
          <li><a href="http://adinserter.pro/documentation#paragraphs" class="simple-link" target="_blank">Clearance</a> options to avoid insertion near images or headers (AdSense TOS)</li>
          <li>Options to <a href="http://adinserter.pro/documentation#misc" class="simple-link" target="_blank">disable insertion</a> on Ajax calls, 404 error pages or in RSS feeds</li>
          <li><a href="https://adinserter.pro/code-editing#ad-rotation" class="simple-link" target="_blank">Ad rotation</a> (works also with caching)</li>
          <li>Ad impression and click <a href="https://adinserter.pro/tracking" class="simple-link" target="_blank">tracking</a> (works also with Javascript ads like AdSense)</li>
          <li>Support for <a href="https://adinserter.pro/tracking#ab-testing" class="simple-link" target="_blank">A/B testing</a></li>
          <li>Support for ads on <a href="http://adinserter.pro/settings#amp" class="simple-link" target="_blank">AMP pages</a></li>
          <li>Support for contextual <a href="https://adinserter.pro/settings#amazon" class="simple-link" target="_blank">Amazon Native Shopping Ads</a> (responsive)</li>
          <li>Custom CSS class name for wrapping divs to avoid ad blockers</li>
          <li>PHP code processing</li>
          <li><a href="https://adinserter.pro/code-editing#banners" class="simple-link" target="_blank">Banner</a> code generator</li>
          <li>Support for <a href="http://adinserter.pro/documentation#header-footer" class="simple-link" target="_blank">header and footer</a> code</li>
          <li>Support for Google Analytics, Piwik or any other web analytics code</li>
          <li>Desktop, tablet and phone server-side <a href="http://adinserter.pro/documentation#devices" class="simple-link" target="_blank">device detection</a></li>
          <li>Client-side <a href="http://adinserter.pro/documentation#devices" class="simple-link" target="_blank">mobile device detection</a> (works with caching, 6 custom viewports)</li>
          <li><a href="https://adinserter.pro/ad-blocking-detection" class="simple-link" target="_blank">Ad blocking detection</a> - popup message, ad replacement, content protection</li>
          <li><a href="https://adinserter.pro/tracking#ad-blocking-statistics" class="simple-link" target="_blank">Ad blocking statistics</a></li>
          <li><a href="http://adinserter.pro/lists" class="simple-link" target="_blank">Black/White-list </a>categories, tags, taxonomies, users, post IDs, urls, referers</li>
          <li><a href="http://adinserter.pro/lists#geo-targeting" class="simple-link" target="_blank">Black/White-list </a>IP addresses or countries (works also with caching)</li>
          <li><a href="http://adinserter.pro/documentation#multisite" class="simple-link" target="_blank">Multisite options</a> to limit settings on the sites</li>
          <li><a href="https://adinserter.pro/code-editing#export-import" class="simple-link" target="_blank">Import/Export</a> block or plugin settings</li>
          <li><a href="http://adinserter.pro/documentation#scheduling" class="simple-link" target="_blank">Scheduling</a> with fallback option</li>
          <li>Country-level <a href="http://adinserter.pro/lists#geo-targeting" class="simple-link" target="_blank">GEO targeting</a> (works also with caching)</li>
          <li>Simple troubleshooting with many <a href="http://adinserter.pro/documentation#debugging" class="simple-link" target="_blank">debugging functions</a></li>
          <li><a href="http://adinserter.pro/documentation#visualization" class="simple-link" target="_blank">Visualization</a> of inserted code blocks or ads for easier placement</li>
          <li><a href="http://adinserter.pro/documentation#visualization" class="simple-link" target="_blank">Visualization</a> of available positions for automatic ad insertion</li>
          <li><a href="http://adinserter.pro/documentation#visualization" class="simple-link" target="_blank">Visualization</a> of HTML tags for easier ad placement between paragraphs</li>
          <li><a href="https://adinserter.pro/code-editing#clipboard" class="simple-link" target="_blank">Clipboard support</a> to easily copy code blocks or settings</li>
          <li>Support via email</li>
        </ul>

        <p style="text-align: justify;">Ad Inserter Pro is a complete all-in-one ad management plugin for WordPress website with many advertising features to automatically insert adverts on posts and pages.
           With Ad Inserter Pro you also get <strong>one year of free updates and support via email</strong>. If you find Ad Inserter useful and need more code blocks, GEO targeting,
           impression and click tracking, ad blocking detection actions or multisite support then you can simply upgrade to <a href="http://adinserter.pro/" style="text-decoration: none;" target="_blank">Ad Inserter Pro</a> (existing settings will be preserved).</p>
      </div>

<?php
}

function sidebar_pro_small () { ?>

      <div class="ai-form header rounded" style="padding-bottom: 0;">
        <div style="float: left;">
          <a href="https://adinserter.pro/" class="simple-link" target="_blank"><img src="<?php echo AD_INSERTER_PLUGIN_IMAGES_URL; ?>icon-256x256.jpg" style="width: 100px;" /></a>
        </div>
        <div class="feature-list" style="float: right;">
          <h3 style="text-align: center; margin: 0;">Looking for <a href="https://adinserter.pro/" class="simple-link" target="_blank">Pro Ad Management plugin</a>?</h3>
          <hr style="margin-bottom: 0;" />

          <div style="float: right; margin-left: 15px;">
            <ul>
              <li>Ads between posts</li>
              <li>Ads between comments</li>
              <li>Support via email</li>
            </ul>
          </div>

          <div style="float: right; margin-left: 15px;">
            <ul>
              <li><a href="https://adinserter.pro/alignments-and-styles" class="simple-link" target="_blank">Sticky positions</a></li>
              <li><a href="http://adinserter.pro/lists" class="simple-link" target="_blank">Limit insertions</a></li>
              <li><a href="http://adinserter.pro/documentation#paragraphs" class="simple-link" target="_blank">Clearance</a> options</li>
            </ul>
          </div>

          <div style="float: right; margin-left: 15px;">
            <ul>
              <li>Ad rotation</li>
              <li><a href="https://adinserter.pro/tracking#ab-testing" class="simple-link" target="_blank">A/B testing</a></li>
              <li><a href="https://adinserter.pro/tracking" class="simple-link" target="_blank">Ad tracking</a></li>
            </ul>
          </div>

          <div style="float: right; margin-left: 15px;">
            <ul>
              <li>Support for <a href="http://adinserter.pro/settings#amp" class="simple-link" target="_blank">AMP pages</a></li>
              <li><a href="https://adinserter.pro/ad-blocking-detection" class="simple-link" target="_blank">Ad blocking detection</a></li>
              <li><a href="http://adinserter.pro/documentation#devices" class="simple-link" target="_blank">Mobile device detection</a></li>

            </ul>
          </div>

          <div style="float: right; margin-left: 15px;">
            <ul>
              <li>64 code blocks</li>
              <li><a href="http://adinserter.pro/lists#geo-targeting" class="simple-link" target="_blank">GEO targeting</a></li>
              <li><a href="http://adinserter.pro/documentation#scheduling" class="simple-link" target="_blank">Scheduling</a></li>
            </ul>
          </div>

          <div style="clear: both;"></div>
        </div>
        <div style="clear: both;"></div>
      </div>

<?php
}

function ai_block_code_demo ($block_class_name, $block_class, $block_number_class, $inline_styles) {
  global $block_object;
  $default = $block_object [0];

  $block_class_name = sanitize_html_class ($block_class_name);

  $classes = array ();
  if ($block_class_name != '' && $block_class) $classes []= $block_class_name;
  if (defined ('AI_NORMAL_HEADER_STYLES') && AI_NORMAL_HEADER_STYLES && !$inline_styles) $classes []= $default->generate_alignment_class ($block_class_name);
  if ($block_class_name != '' && $block_number_class) $classes []= $block_class_name . '-n';

  $class = count ($classes) ? ' class="' . implode (' ', $classes) . '"' : '';
  $style = $inline_styles || !defined ('AI_NORMAL_HEADER_STYLES') ? ' style="' . AI_ALIGNMENT_CSS_DEFAULT . '"' : '';

  echo "&lt;div$class$style&gt;";
}
