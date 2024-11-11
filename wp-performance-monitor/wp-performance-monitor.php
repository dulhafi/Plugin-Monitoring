<?php
/*
Plugin Name: WP Performance Monitor
Description: Plugin Monitoring performa situs WordPress seperti waktu muat, penggunaan memori, dan query.
Version: 1.0 | Requires PHP 7.2 >
Author: <a href="#">Dulhafi</a> in PDSI
Author URI: 
Tested up PHP : 7.2
Requires PHP: 8.1 
*/


function wp_performance_monitor_dashboard_menu() {
    global $wpdb;
    
    
    $execution_time = timer_stop(0);
    
  
    $memory_usage = memory_get_usage() / 1024 / 1024; // dalam MB
    
    
    // $query_count = count($wpdb->queries);
    $query_count = (is_array($wpdb->queries) || $wpdb->queries instanceof Countable) ? count($wpdb->queries) : 0;

    
   
    // $cpu_usage = sys_getloadavg()[0]; // load CPU 1 menit terakhir
    $cpu_usage = function_exists('sys_getloadavg') ? sys_getloadavg()[0] : 0;

    
    echo "<h3>Informasi Performa Situs</h3>";
    echo "<p><strong>Waktu Muat Halaman:</strong> {$execution_time} detik</p>";
    echo "<p><strong>Penggunaan Memori:</strong> " . number_format($memory_usage, 2) . " MB</p>";
    echo "<p><strong>Jumlah Query:</strong> {$query_count}</p>";
    echo "<p><strong>Rata-Rata Penggunaan CPU:</strong> {$cpu_usage} (load 1 menit)</p>";
}


function wp_performance_monitor_menu() {
    add_menu_page(
        'WP Performance Monitor',
        'Performance Monitor',
        'manage_options',
        'wp-performance-monitor',
        'wp_performance_monitor_dashboard_menu',
        'dashicons-camera'
    );
}

add_action('admin_menu', 'wp_performance_monitor_menu');

// Membuat tabel saat plugin diaktifkan
function wp_performance_monitor_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'performance_monitor';
    
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        execution_time float NOT NULL,
        memory_usage float NOT NULL,
        query_count int NOT NULL,
        cpu_usage float NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'wp_performance_monitor_activate');

?>
