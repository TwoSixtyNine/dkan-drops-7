<?php
/**
 * @file
 * Theme functions
 */

require_once dirname(__FILE__) . '/includes/structure.inc';
require_once dirname(__FILE__) . '/includes/comment.inc';
require_once dirname(__FILE__) . '/includes/form.inc';
require_once dirname(__FILE__) . '/includes/menu.inc';
require_once dirname(__FILE__) . '/includes/node.inc';
require_once dirname(__FILE__) . '/includes/panel.inc';
require_once dirname(__FILE__) . '/includes/user.inc';
require_once dirname(__FILE__) . '/includes/view.inc';


/**
 * Theme function for iframe link.
 */
function nuboot_radix_link_iframe_formatter_original($variables) {
  $link_options = $variables['element'];
  $link = l($link_options['title'], $link_options['url'], $link_options);
  return '<i class="fa fa-external-link"></i>  ' . $link;
}

/**
 * Implements theme_breadcrumb().
 */
function nuboot_radix_breadcrumb($variables) {
  if (drupal_is_front_page()) {
    return;
  }
  $breadcrumb = $variables['breadcrumb'];
  $contexts = array();

  if (!empty($breadcrumb)) {
    $output = '<h2 class="element-invisible">' . t('You are here') . '</h2>';

    $crumbs = '<ul class="breadcrumb">';
    if (!drupal_is_front_page()) {
      $crumbs .= '<li class="home-link"><a href="' . url('<front>') . '"><i class="fa fa fa-home"></i><span> Home</span></a></li>';
    }

    // Remove null values.
    $breadcrumb = array_filter($breadcrumb);
    $i = 1;
    foreach ($breadcrumb as $value) {
      if ($i == count($breadcrumb)) {
        $crumbs .= '<li class="active-trail">' . $value . '</li>';
      }
      else {
        $crumbs .= '<li>' . $value . '</li>';
      }
      $i++;
    }
    $crumbs .= '</ul>';
    return $crumbs;
  }
}



/**
 * Returns HTML for an inactive facet item.
 *
 *   An associative array containing the keys 'text', 'path', 'options', and
 *   'count'. See the l() and theme_facetapi_count() functions for information
 *   about these variables.
 *
 * @ingroup themeable
 */
function nuboot_radix_facetapi_link_inactive($variables) {
  // Builds accessible markup.
  // @see http://drupal.org/node/1316580
  $accessible_vars = array(
    'text' => $variables['text'],
    'active' => FALSE,
  );

  $accessible_markup = theme('facetapi_accessible_markup', $accessible_vars);

  // Sanitizes the link text if necessary.
  $sanitize = empty($variables['options']['html']);
  $variables['text'] = ($sanitize) ? check_plain($variables['text']) : $variables['text'];

  // Adds count to link if one was passed.
  if (isset($variables['count'])) {
    $variables['text'] .= ' ' . theme('facetapi_count', $variables);
  }

  // Resets link text, sets to options to HTML since we already sanitized the
  // link text and are providing additional markup for accessibility.
  $variables['text'] .= $accessible_markup;
  $variables['options']['html'] = TRUE;
  return theme_link($variables);
}

/**
 * Returns HTML for an inactive facet item.
 *
 *   An associative array containing the keys 'text', 'path', and 'options'. See
 *   the l() function for information about these variables.
 *
 * @see l()
 *
 * @ingroup themeable
 */
function nuboot_radix_facetapi_link_active($variables) {
  // Sanitizes the link text if necessary.
  $sanitize = empty($variables['options']['html']);
  $link_text = ($sanitize) ? check_plain($variables['text']) : $variables['text'];

  // Theme function variables fro accessible markup.
  // @see http://drupal.org/node/1316580
  $accessible_vars = array(
    'text' => $variables['text'],
    'active' => TRUE,
  );

  $accessible_markup = theme('facetapi_accessible_markup', $accessible_vars);
  $variables['text'] .= $accessible_markup;
  $variables['options']['html'] = TRUE;
  return theme_link($variables);
}

/**
 * Theme social icons.
 */
function nuboot_radix_sitewide_social_block() {
  $path = isset($_GET['q']) ? $_GET['q'] : '<front>';
  $link = url($path, array('absolute' => TRUE));

  $output = array(
    '#theme' => 'item_list',
    '#items' => array(
      'googleplus' => array(
        'data' => l('<i class="fa fa-lg fa-google-plus-square"></i> ' . t('Google+'),
        'https://plus.google.com/share', array(
          'query' => array(
            'url' => $link,
          ),
          'attributes' => array(
            'target' => '_blank',
          ),
          'html' => TRUE,
        )),
        'class' => array('nav-item'),
      ),
      'twitter' => array(
        'data' => l('<i class="fa fa-lg fa-twitter-square"></i> ' . t('Twitter'),
        'https://twitter.com/share', array(
          'query' => array(
            'url' => $link,
          ),
          'attributes' => array(
            'target' => '_blank',
          ),
          'html' => TRUE,
        )),
        'class' => array('nav-item'),
      ),
      'facebook' => array(
        'data' => l('<i class="fa fa-lg fa-facebook-square"></i> ' . t('Facebook'),
        'https://www.facebook.com/sharer.php', array(
          'query' => array(
            'u' => $link,
          ),
          'attributes' => array(
            'target' => '_blank',
          ),
          'html' => TRUE,
        )),
        'class' => array('nav-item'),
      ),
    ),
    '#attributes' => array(
      'class' => array('nav', 'nav-simple', 'social-links'),
    ),
  );

  return $output;
}

/**
 * Overrides theme_file_widget().
 *
 * https://drupal.org/files/issues/bootstrap-undefined-index-2177089-1.patch
 */
function nuboot_radix_file_widget($variables) {
  $element = $variables['element'];
  $output = '';

  $hidden_elements = array();
  foreach (element_children($element) as $child) {
    if (isset($element[$child]['#type']) && $element[$child]['#type'] === 'hidden') {
      $hidden_elements[$child] = $element[$child];
      unset($element[$child]);
    }
  }

  $element['upload_button']['#prefix'] = '<span class="input-group-btn">';
  $element['upload_button']['#suffix'] = '</span>';

  // The "form-managed-file" class is required for proper Ajax functionality.
  $output .= '<div class="file-widget form-managed-file clearfix input-group">';
  if (!empty($element['fid']['#value'])) {
    // Add the file size after the file name.
    $element['filename']['#markup'] .= ' <span class="file-size">(' . format_size($element['#file']->filesize) . ')</span> ';
  }
  $output .= drupal_render_children($element);
  $output .= '</div>';
  $output .= render($hidden_elements);
  return $output;
}

/**
 * Overrides theme_user_picture().
 */
function nuboot_radix_user_picture(&$variables) {
  $variables['user_picture'] = '';

  if (variable_get('user_pictures', 0)) {
    // Load the full user object since it is not provided with nodes, comments,
    // or views displays.
    $account = module_exists('gravatar') ? _gravatar_load_account($variables['account']) : $variables['account'];
    $filepath = module_exists('gravatar') ? _gravatar_get_account_user_picture($account) : '';

    if (!empty($filepath)) {
      $alt = t("@user's picture", array('@user' => format_username($account)));
      if (module_exists('image') && file_valid_uri($filepath) && $style = variable_get('user_picture_style', '')) {
        $variables['user_picture'] = theme('image_style', array('style_name' => $style, 'path' => $filepath, 'alt' => $alt, 'title' => $alt));
      }
      else {
        $variables['user_picture'] = theme('image', array('path' => $filepath, 'alt' => $alt, 'title' => $alt));
      }
      if ($account->uid && user_access('access user profiles')) {
        // Create link to the user's profile.
        $attributes = array('title' => t('View user profile.'));
        $variables['user_picture'] = l($variables['user_picture'], 'user/' . $account->uid, array('attributes' => $attributes, 'html' => TRUE));
      }
      elseif (!empty($account->homepage)) {
        // If user is anonymous, create link to the commenter's homepage.
        $attributes = array(
          'title' => t('View user website.'),
          'rel' => 'external nofollow',
        );
        $variables['user_picture'] = l($variables['user_picture'], $account->homepage, array('attributes' => $attributes, 'html' => TRUE));
      }
    }
  }
}
