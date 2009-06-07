<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?><?php if ($options['kitten'] == false) : ?>
 <?php _e ('You can help support the author by sending a donation.  Every file that is downloaded without support will cause a <strong>kitten to cry</strong>.', 'drain-hole'); ?>
 <?php _e ('All it takes is <strong>$16 to keep the kittens happy</strong> and act as an incentive for me to carry on writing other free software.', 'drain-hole'); ?>
</p>

<p><img class="kitten" src="<?php echo $this->url () ?>/images/kitten.jpg" width="150" height="204" alt="Kitten"/></p>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="admin@urbangiraffe.com">
<input type="hidden" name="item_name" value="Drain Hole">
<input type="hidden" name="amount" value="16.00">
<input type="hidden" name="buyer_credit_promo_code" value="">
<input type="hidden" name="buyer_credit_product_category" value="">
<input type="hidden" name="buyer_credit_shipping_method" value="">
<input type="hidden" name="buyer_credit_user_address_change" value="">
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="return" value="http://urbangiraffe.com/plugins/drain-hole/">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="tax" value="0">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="bn" value="PP-DonationsBF">
<input type="image" class="kitten" style="border: none" src="https://www.paypal.com/en_US/i/btn/x-click-butcc-donate.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
</form>
<?php else : ?>
	<p><?php _e ('Your help stopping kittens from crying is appreciated!', 'drain-hole'); ?></p>
	
	<p><?php _e ('If you \'mistakenly\' said you supported the author without actually doing so then not only are the kittens still crying, but their stomachs ache and they all feel miserable.', 'drain-hole'); ?></p>
<?php endif; ?>