<?php
namespace Desymphony\Helpers;

use WP_Error;
use WP_Image_Editor;

if ( ! defined('ABSPATH') ) {
    exit;
}

/**
 * A flexible image handler that uses DS_Image_Profiles for config.
 */
class DS_Media_Handler {

    /**
     * Process a base64-cropped image using a named "profile" from DS_Image_Profiles.
     *
     * @param array $args {
     *   @type string profile_key  The key from DS_Image_Profiles (e.g. "product", "profile")
     *   @type string image_data   The base64 image data
     *   @type array  placeholders Additional placeholders like ['id' => 123, 'slug' => 'my-banner']
     * }
     *
     * @return array|WP_Error A map: [ 'outputs' => [ ['name'=>'...', 'url'=>'...'], ... ] ]
     */
    public function process_image_upload( array $args ) {
        $profileKey   = $args['profile_key']   ?? '';
        $image_data   = $args['image_data']    ?? '';
        $placeholders = $args['placeholders']  ?? [];
    
        if ( ! $profileKey ) {
            return new \WP_Error(
                'missing_profile',
                __( 'No profileKey specified.', 'wv-addon' )
            );
        }
        if ( ! $image_data ) {
            return new \WP_Error(
                'missing_image_data',
                __( 'No image_data provided.', 'wv-addon' )
            );
        }
    
        // 1) Load the profile definition
        $profile = DS_Image_Profiles::get_profile( $profileKey );
        if ( ! $profile ) {
            return new \WP_Error(
                'invalid_profile',
                sprintf(
                    __( 'Profile "%s" not found.', 'wv-addon' ),
                    $profileKey
                )
            );
        }
    
        // 2) Decode base64 => real temp file in /uploads/wv/temp/
        $decoded = $this->decode_base64_image( $image_data );
        if ( is_wp_error( $decoded ) ) {
            return $decoded;
        }
        $temp_file = $decoded['tmp_name'];  // e.g. /.../wp-content/uploads/wv/temp/wv_ABCdEF.tmp
        $extension = $decoded['extension'] ?: 'jpg';
        if ( ! in_array( $extension, [ 'jpg', 'jpeg', 'png', 'webp' ], true ) ) {
            $extension = 'jpg'; // fallback
        }
    
        // 3) "upload_dir" filter => store final images in /uploads/ + your subdir
        $upload_filter = function( $dirs ) use ( $profile, $placeholders ) {
            $sub = trim( $profile['subdir'] ?? 'wv', '/' );
    
            // Replace placeholders
            foreach ($placeholders as $k => $v) {
                $sub = str_replace('{{'.$k.'}}', $v, $sub);
            }
    
            $dirs['subdir'] = '/' . $sub;
            $dirs['path']   = $dirs['basedir'] . $dirs['subdir'];
            $dirs['url']    = $dirs['baseurl'] . $dirs['subdir'];
            return $dirs;
        };
        
        add_filter( 'upload_dir', $upload_filter );
    
        // 4) Create a “base” file for repeated usage (resizing, cropping, etc.)
        $baseName = 'addon-base.' . $extension;
        $up = wp_upload_bits( $baseName, null, file_get_contents( $temp_file ) );
        if ( ! empty( $up['error'] ) ) {
            remove_filter( 'upload_dir', $upload_filter );
            // Also remove the temp file
            @unlink( $temp_file );
            return new \WP_Error( 'upload_error', $up['error'] );
        }
        $tmp_path = $up['file']; // absolute path in /uploads/... subdir
    
        // We’ll collect final URLs here
        $results = [ 'outputs' => [] ];
    
        // 5) For each “output size” in the profile, do a resize
        $outputs = $profile['outputs'] ?? [];
        if ( empty( $outputs ) ) {
            @unlink( $tmp_path );
            remove_filter( 'upload_dir', $upload_filter );
            @unlink( $temp_file ); // remove the base64 temp
            return new \WP_Error(
                'no_outputs',
                sprintf( __( 'Profile "%s" has no outputs configured.', 'wv-addon' ), $profileKey )
            );
        }
    
        foreach ( $outputs as $outSpec ) {
            // Example: 'filename' => 'product-image-{{id}}.jpg'
            $filename = $outSpec['filename'] ?? 'final.jpg';
    
            // Replace placeholders like {{id}}, {{slug}}
            foreach ( $placeholders as $k => $v ) {
                $filename = str_replace( '{{'.$k.'}}', $v, $filename );
            }
    
            $width   = (int)($outSpec['width']   ?? 0);
            $height  = (int)($outSpec['height']  ?? 0);
            $quality = (int)($outSpec['quality'] ?? 85);
    
            // Resize & save
            $res = $this->create_named_resized( $tmp_path, $width, $height, $filename, $quality );
            if ( is_wp_error($res) ) {
                @unlink( $tmp_path );
                remove_filter( 'upload_dir', $upload_filter );
                @unlink( $temp_file );
                return $res;
            }
    
            // Optionally append ?ver=timestamp
            $finalUrl = $res . '?ver=' . time();
    
            $results['outputs'][] = [
                'name' => $filename,
                'url'  => $finalUrl,
            ];
        }
    
        // 6) Cleanup
        @unlink( $tmp_path );                 // remove the “addon-base.jpg”
        remove_filter( 'upload_dir', $upload_filter );
        @unlink( $temp_file );               // remove the original .tmp base64 file
    
        // For convenience, set 'final' => first output
        if ( ! empty( $results['outputs'] ) ) {
            $results['final'] = $results['outputs'][0]['url'];
        }
    
        return $results;
    }
    

    /**
     * Decode base64 => real temp file
     */
    private function decode_base64_image( string $base64_str ) {
        // Validate base64 format
        if ( ! preg_match( '/^data:image\/(\w+);base64,/', $base64_str, $matches ) ) {
            return new \WP_Error( 'invalid_data_url', __( 'Invalid base64 image data.', 'wv-addon' ) );
        }
    
        // Identify file extension from the data URL
        $ext = strtolower( $matches[1] );
    
        // Decode the actual binary data
        $data = base64_decode( substr( $base64_str, strpos($base64_str, ',') + 1 ) );
        if ( ! $data ) {
            return new \WP_Error( 'decode_fail', __( 'Could not decode base64 data.', 'wv-addon' ) );
        }
    
        // Ensure a custom temp folder exists within wp-content/uploads
        $upload_dir  = wp_upload_dir();
        $temp_folder = $upload_dir['basedir'] . '/wv/temp';
        if ( ! file_exists( $temp_folder ) ) {
            wp_mkdir_p( $temp_folder ); // create it if needed
        }
    
        // Create a unique temp file in that folder
        // 'wv_' is just a prefix; use anything you like
        $tmp_name = tempnam( $temp_folder, 'wv_' );
        if ( ! $tmp_name ) {
            return new \WP_Error( 'tmpfile_fail', __( 'Could not create temp file.', 'wv-addon' ) );
        }
    
        // Write the decoded image data into that temp file
        file_put_contents( $tmp_name, $data );
    
        return [
            'tmp_name'  => $tmp_name,
            'extension' => ( $ext === 'jpeg' ? 'jpg' : $ext ),
        ];
    }
    

    /**
     * create_named_resized => produce final file & URL
     */
    private function create_named_resized( string $original_path, int $width, int $height, string $final_filename, int $quality ) {
        $info       = wp_upload_dir();
        $final_path = $info['path'] . '/' . $final_filename;
        $final_url  = $info['url']  . '/' . $final_filename;

        $editor = wp_get_image_editor( $original_path );
        if ( is_wp_error($editor) ) {
            return $editor;
        }

        // If height=0 => scale by width only
        if ( $height > 0 ) {
            $editor->resize( $width, $height, true );
        } else {
            $size   = $editor->get_size();
            $ratio  = $width / $size['width'];
            $newH   = (int) round( $size['height'] * $ratio );
            $editor->resize( $width, $newH, false );
        }

        $saved = $editor->save( $final_path, 'image/jpeg', $quality );
        if ( is_wp_error($saved) ) {
            return $saved;
        }

        return $final_url;
    }
}
