<?php
/**
 * Hauptlogik des Activity Log Add-ons (v2.1.4)
 * Pfad: activity-log.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 2. AJAX HANDLER
 */
add_action('wp_ajax_wm_al_get_table', function() {
    check_ajax_referer('wm_al_ajax_nonce', 'nonce');
    // Schutz gegen zu frühen Aufruf: Prüfen ob User-Funktionen da sind
    if ( ! function_exists('current_user_can') || ! current_user_can('manage_options') ) {
        wp_send_json_error( 'Verweigert' );
    }
    $page = isset($_POST['paged']) ? max(1, intval($_POST['paged'])) : 1;
    wp_send_json_success( wm_al_render_log_table($page) );
});

/**
 * 3. RENDER-LOGIK
 */
if ( ! function_exists( 'wm_al_render_log_table' ) ) {
    function wm_al_render_log_table($current_page = 1) {
        // SICHERHEITS-CHECK (Behebt Fatal Error)
        if ( ! function_exists('current_user_can') ) return '';
        if ( ! current_user_can('manage_options') ) return '';
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'wm_activity_log_extended';
        
        // Prüfen ob Tabelle existiert (verhindert SQL-Errors bei Aktivierung)
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            return '<p>Datenbank wird vorbereitet...</p>';
        }

        $per_page   = 20;
        $offset     = ( (int) $current_page - 1 ) * $per_page;

        $total_items = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
        $results     = $wpdb->get_results( $wpdb->prepare(
            "SELECT *, TIMESTAMPDIFF(SECOND, time, last_seen) as duration_sec FROM $table_name ORDER BY time DESC LIMIT %d OFFSET %d",
            $per_page, $offset
        ) );

        if ( ! $results ) return '<p>' . esc_html__( 'Keine Daten vorhanden.', 'whoismember' ) . '</p>';
        
        $whois_base = untrailingslashit( get_option( 'wm_whois_base_url', 'https://whois.com' ) ) . '/whois/';

        ob_start();
        ?>
        <table style="width:100%; border-collapse: collapse; font-family: sans-serif; font-size: 12px; background: #fff; border: 1px solid #dee2e6;">
            <thead>
                <tr style="background: #f1f1f1; text-align: left;">
                    <th style="padding: 10px; border: 1px solid #dee2e6;">Zeit / Dauer</th>
                    <th style="padding: 10px; border: 1px solid #dee2e6;">Besucher / IP</th>
                    <th style="padding: 10px; border: 1px solid #dee2e6;">Aktivität (URL)</th>
                    <th style="padding: 10px; border: 1px solid #dee2e6;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $results as $row ) : 
                    $duration = ( $row->duration_sec > 0 ) ? (int) $row->duration_sec . 's' : '-';
                    $status_color = ($row->status_code == 404) ? '#c62828' : '#28a745';
                ?>
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 8px; border: 1px solid #dee2e6;"><strong><?php echo esc_html($row->time); ?></strong><br>(<?php echo esc_html($duration); ?>)</td>
                    <td style="padding: 8px; border: 1px solid #dee2e6;"><strong><?php echo esc_html($row->origin_info); ?></strong><br><code><?php echo esc_html($row->user_ip); ?></code></td>
                    <td style="padding: 8px; border: 1px solid #dee2e6; max-width: 200px; word-break: break-all;"><?php echo esc_html($row->request_uri); ?></td>
                    <td style="padding: 8px; border: 1px solid #dee2e6; text-align:center;"><span style="background:<?php echo $status_color; ?>; color:#fff; padding:2px 6px; border-radius:3px; font-weight:bold;"><?php echo esc_html($row->status_code); ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="wm-al-pagination" style="margin-top: 15px; display: flex; gap: 5px; flex-wrap: wrap;">
            <?php
            $total_pages = ceil( $total_items / $per_page );
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = ($i === (int)$current_page) ? 'background:#2271b1; color:#fff;' : 'background:#eee;';
                echo '<button class="wm-al-page-btn" data-page="'.(int)$i.'" style="cursor:pointer; border:1px solid #ccc; padding:4px 8px; border-radius:3px; font-size:11px; '.$active.'">'.(int)$i.'</button>';
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
}

/**
 * 4. SHORTCODE
 */
add_shortcode( 'activity-log', function() {
    // Sicherstellen, dass das User-System geladen ist
    if ( ! function_exists('current_user_can') ) return '';
    if ( ! current_user_can( 'manage_options' ) ) return '';
    
    $nonce = wp_create_nonce('wm_al_ajax_nonce');
    ob_start();
    ?>
    <div class="wm-log-inline-display" style="margin-top:20px; padding:20px; background:#fff; border:1px solid #2271b1; border-radius:5px; box-shadow:0 2px 10px rgba(0,0,0,0.1); font-family: sans-serif;">
        <h3 style="margin-top:0; color:#2271b1;">Administratives Activity-Log</h3>
        <div id="wm-al-container" style="min-height: 100px;">
            <p class="wm-al-loader">⏳ Lade Protokoll...</p>
        </div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        function wm_al_load(page = 1) {
            $('#wm-al-container').css('opacity', '0.5');
            $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                action: 'wm_al_get_table', nonce: '<?php echo $nonce; ?>', paged: page
            }, function(response) {
                if(response.success) { $('#wm-al-container').html(response.data).css('opacity', '1'); }
            });
        }
        wm_al_load();
        $(document).off('click', '.wm-al-page-btn').on('click', '.wm-al-page-btn', function(e) {
            e.preventDefault();
            wm_al_load($(this).data('page'));
        });
    });
    </script>
    <?php
    return ob_get_clean();
});
