<?php
/*
Plugin Name: Add to cart label based on product category
Plugin URI: http://desenvolvedorwordpress.com/
Description: Insert a brand new field to change the add-to-cart button text.
Version: 1.0
Author: Eric Gruby
Author URI:  http://desenvolvedorwordpress.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wporg
Domain Path: /languages
*/
 function ___register_add_to_cart_text() {
     register_meta( 'product_cat', '__add_to_cart_text', '___sanitize_add_to_cart_text' );
 }
 function ___sanitize_add_to_cart_text ( $value ) {
     return sanitize_text_field ($value);
 }
 function ___get_add_to_cart_text( $term_id ) {
   $value = get_term_meta( $term_id, '__add_to_cart_text', true );
   $value = ___sanitize_add_to_cart_text( $value );
   return $value;
 }
 function ___add_form_field_add_to_cart_text() { ?>
     <?php wp_nonce_field( basename( __FILE__ ), 'add_to_cart_text_nonce' ); ?>
     <div class="form-field term-meta-text-wrap">
         <label for="term-meta-text"><?php _e( 'Custom Add to Cart Text', 'add-to-cart-label-plugin' ); ?></label>
         <input type="text" name="add_to_cart_text" id="term-meta-text" value="" class="term-meta-text-field" />
     </div>
 <?php }
 function ___edit_form_field_add_to_cart_text( $term ) {
     $value  = ___get_add_to_cart_text( $term->term_id );
     if ( ! $value )
         $value = ""; ?>

     <tr class="form-field term-meta-text-wrap">
         <th scope="row"><label for="term-meta-text"><?php _e( 'Custom Add to Cart Text', 'add-to-cart-label-plugin' ); ?></label></th>
         <td>
             <?php wp_nonce_field( basename( __FILE__ ), 'add_to_cart_text_nonce' ); ?>
             <input type="text" name="add_to_cart_text" id="term-meta-text" value="<?php echo esc_attr( $value ); ?>" class="term-meta-text-field"  />
         </td>
     </tr>
 <?php }
 function ___save_add_to_cart_text( $term_id ) {
     if ( ! isset( $_POST['add_to_cart_text_nonce'] ) || ! wp_verify_nonce( $_POST['add_to_cart_text_nonce'], basename( __FILE__ ) ) )
         return;
     $old_value  = ___get_add_to_cart_text( $term_id );
     $new_value = isset( $_POST['add_to_cart_text'] ) ? ___sanitize_add_to_cart_text ( $_POST['add_to_cart_text'] ) : '';
     if ( $old_value && '' === $new_value )
         delete_term_meta( $term_id, '__add_to_cart_text' );
     else if ( $old_value !== $new_value )
         update_term_meta( $term_id, '__add_to_cart_text', $new_value );
 }
 function eric_add_to_cart_text($default){
    global $product;
    $cats = $product->category_ids;
    $rand_texts = array();
    foreach($cats as $cat){
      $cat_text = get_term_meta( $cat, '__add_to_cart_text', true );
      if($cat_text != ""){
        $rand_texts[] = $cat_text;
      }
    }
    if(count($rand_texts) < 1){
      return __($default, 'add-to-cart-label-plugin');
    }else{
      $rand_key = array_rand($rand_texts, 1);
      $text = $rand_texts[$rand_key];
      return __($text, 'add-to-cart-label-plugin');
    }
 }
 add_action( 'init', '___register_add_to_cart_text' );
 add_action( 'product_cat_add_form_fields', '___add_form_field_add_to_cart_text' );
 add_action( 'product_cat_edit_form_fields', '___edit_form_field_add_to_cart_text' );
 add_action( 'edit_product_cat',   '___save_add_to_cart_text' );
 add_action( 'create_product_cat', '___save_add_to_cart_text' );
 add_filter('woocommerce_product_add_to_cart_text', 'eric_add_to_cart_text');
 add_filter('woocommerce_product_single_add_to_cart_text', 'eric_add_to_cart_text');
?>
