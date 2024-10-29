<?php
/**
 * Plugin Name: Arya Switch Theme
 * Plugin URI: https://github.com/aryathemes/arya-switch-theme
 * Description: Allows users to choose and preview all WordPress themes installed without activation or deactivation for demonstration purposes.
 * Author: Arya Themes
 * Author URI: https://github.com/aryathemes
 * Version: 1.0.0
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package   Arya\SwitchTheme
 * @author    Luis A. Ochoa
 * @copyright 2019 Luis A. Ochoa
 * @license   GPL-2.0-or-later
 */

defined( 'ABSPATH' ) || exit;

/**
 * Allows to choose and preview a theme without activation.
 *
 * @since 1.0.0
 */
function arya_switch_theme()
{
    if ( ! isset( $_GET['theme'] ) || empty( $_GET['theme'] ) ) {
        return;
    }

    $template = sanitize_title( $_GET['theme'] );

    $theme = wp_get_theme( $template );

    if ( ! $theme->exists() || ! empty( $theme->get( 'Template' ) ) ) {
        return;
    }

    if ( ! isset( $_GET['child'] ) || empty( $_GET['child'] ) ) {
        $stylesheet = $template;
    } else {
        $stylesheet = sanitize_title( $_GET['child'] );

        $child = wp_get_theme( $stylesheet );

        if ( ! $child->exists() || $template !== $child->get( 'Template' ) ) {
            $stylesheet = $template;
        }
    }

    add_filter( 'template', function() use ( $template ): string {
        return $template;
    }, 10 );

    add_filter( 'stylesheet', function() use ( $stylesheet ): string {
        return $stylesheet;
    }, 10 );
}
add_action( 'setup_theme', 'arya_switch_theme' );

/**
 * Filters the permalinks.
 *
 * @since 1.0.0
 */
function arya_query_vars_link( $permalink ): string
{
    global $wp_query;

    $theme = isset( $wp_query->query_vars['theme'] ) ? $wp_query->query_vars['theme']: '';

    $child = isset( $wp_query->query_vars['child'] ) ? $wp_query->query_vars['child']: '';

    $query = [];

    if ( ! empty( $theme ) ) {
        $query[ 'theme' ] = $theme;
    }

    if ( ! empty( $child ) ) {
        $query[ 'child' ] = $child;
    }

    $permalink = add_query_arg( $query, $permalink );

    return esc_url( $permalink );
}

$filters = [
    'home_url',
    'site_url',
    'network_site_url',
    'network_home_url',
    'post_link',
    'page_link',
    'attachment_link',
    'category_link',
    'tag_link',
    'term_link',
    'post_type_link',
    'year_link',
    'month_link',
    'day_link',
    'search_link'
];

foreach( $filters as $filter ) {
    add_filter( $filter, 'arya_query_vars_link' );
}

/**
 * Filters the query variables 'theme' and 'child'.
 *
 * @since 1.0.0
 */
function arya_query_vars( $query_vars ): array
{
    $query_vars[] = 'theme';
    $query_vars[] = 'child';

    return $query_vars;
}
add_filter( 'query_vars', 'arya_query_vars' );
