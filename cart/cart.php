<?php

//echo "some test code here tetstttt";

$promocode_amt = 0;
$discount_amt_label = "";
if($order_data['promocode_id']>0 && $order_data['promocode_amt']>0) {
	$promocode_amt = $order_data['promocode_amt'];
	$discount_amt_label = "";
	if($order_data['discount_type']=="percentage")
		$discount_amt_label = "(".$order_data['discount']."%)";

	$total_of_order = $sum_of_orders+$order_data['promocode_amt'];
	$is_promocode_exist = true;
} else {
	$total_of_order = $sum_of_orders;
}

if(!empty($guest_user_data) && $guest_user_id > 0) {
	$user_data = $guest_user_data;
	$user_id = $guest_user_id;
}

$open_shipping_popup = 0;
if(isset($_SESSION['open_shipping_popup'])) {
	$open_shipping_popup = $_SESSION['open_shipping_popup'];
	unset($_SESSION['open_shipping_popup']);
} ?>
<style>
    .pac-container.pac-logo {
    z-index: 9999999999 !important;
}
</style>
<form action="controllers/cart/cart.php" method="post" id="revieworder_form">
  <section class="pb-0">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
          <div class="block heading page-heading text-center">
            <h3>Order details:</h3>
          </div>
          <div class="block order-details cart clearfix">

						<div class="table-div">
							<div class="table-row head d-none d-md-flex clearfix">
								<div class="table-cell sl">No</div>
								<div class="table-cell description">Name & Description</div>
								<div class="table-cell price text-center">Price</div>
								<div class="table-cell actions text-center">Actions</div>
							</div>
							<?php
							$tid=1;
							foreach($order_item_list as $order_item_list_data) {
							$model_data = get_single_model_data($order_item_list_data['model_id']);
							$mdl_details_url = SITE_URL.$model_details_page_slug.$model_data['sef_url'];

							//path of this function (get_order_item) admin/include/functions.php
							$order_item_data = get_order_item($order_item_list_data['id'],'rev_ord_list'); ?>
							<div class="table-row d-md-flex clearfix">
								<div class="table-cell sl"><?=$tid?></div>
								<div class="table-cell description item-description-<?=$order_item_list_data['id']?>">
									<div class="row">
										<div class="col-2 col-md-2 d-flex align-items-center">
											<?php
											if($order_item_list_data['device_icon']) {
												echo '<img src="'.SITE_URL.'images/device/'.$order_item_list_data['device_icon'].'"/>';
											} elseif($order_item_list_data['cat_cart_image']) {
												echo '<img src="'.SITE_URL.'images/categories/'.$order_item_list_data['cat_cart_image'].'"/>';
											} ?>
										</div>
										<div class="col-8 col-md-8">
											<h6><?=$order_item_list_data['model_title']?></h6>
											<!-- <a class="d-block d-md-none d-lg-none device-info" data-id="<?=$order_item_list_data['id']?>" href="javascript:void(0)">more info</a> -->
											<?=$order_item_data['device_type']?>
										</div>
										<div  class="col-2 col-md-2">
											<div class="input-group mt-4">
												<form method="post" action="<?=SITE_URL?>controllers/cart/cart.php" id="form_<?php echo $order_item_list_data['id']; ?>">   
	                                                <input type="hidden" name="order_id" value="<?php echo $order_item_list_data['id']; ?>" min="1">    
	                                                  <input type="number" class="form-control mb-3" name="quantity" placeholder="Quantity" value="<?php echo $order_item_list_data['quantity']; ?>">
	                                                  <button class="btn btn-primary rounded-pill btn-sm" style="font-size: 0.875rem !important;" type="submit">Update Cart</button>
	                                                
	                                            </form>
	                                        </div>
										</div>
									</div>		
								</div>
								<div class="table-cell price d-flex align-items-center justify-content-center">
									<span class="price-item">
										<span>
											<?=amount_fomat($order_item_list_data['price'] * $order_item_list_data['quantity'])?>
										</span>
									</span>
								</div>
								<div class="table-cell actions">
									<div class="clearfix">
										<a href="<?=$mdl_details_url?>?item_id=<?=$order_item_list_data['id']?>"><img src="<?=SITE_URL?>images/cart/edit.png" alt="Edit"></a>
					  					<a href="<?=SITE_URL?>controllers/cart/cart.php?rorder_id=<?=$order_item_list_data['id']?>" onclick="return confirm('Are you sure you want to remove this item?');"><img src="<?=SITE_URL?>images/icons/close-circle.png" alt="Remove"></a>
									</div>
								</div>
							</div>
							<?php
							$tid++;
							$item_price_array[] = amount_fomat($order_item_list_data['price'] * $order_item_list_data['quantity']);
							} ?>
							<div class="table-row no-bg d-md-flex border-top clearfix">
								<div class="table-cell w-50 cart-total-cell">
									<h5 class="title">Expected payments:</h5>
									<?php
									$expected_payments = '';
									if(count($item_price_array)==1) {
										$expected_payments = '<span>'.amount_fomat($sum_of_orders).'</span>';
									} else {
										$expected_payments = implode(" + ",$item_price_array).' = <span>'.amount_fomat($sum_of_orders).'</span>';
									}
									// <a href="javascript:void(0);" id="promocode_removed">X</a>
									echo $expected_payments; ?> <span id="showhide_promocode_row" <?php if($promocode_amt<=0) {echo 'style="display:none;"';}?>> + <span id="promocode_amt_label"><?=$discount_amt_label?></span> <span id="promocode_amt"><?=amount_fomat($promocode_amt)?></span>&nbsp;(Coupon)</span>
									</p>
									<div class="row">
										<div class="col-md-6">
											<?php
											$bonus_data = get_bonus_data_info_by_user($user_id);
											$bonus_percentage = $bonus_data['bonus_data']['percentage'];
											if($user_id>0 && $bonus_percentage>0) {
												$bonus_amount = ($sum_of_orders * $bonus_percentage / 100); ?>
												<p class="bonus"><img src="<?=SITE_URL?>images/icons/gift.png" alt="gift"> Bonus: <?=$bonus_percentage?>% = <?=amount_fomat($bonus_amount)?></p>
												<input type="hidden" name="bonus_percentage" id="bonus_percentage" value="<?=$bonus_percentage?>"/>
												<input type="hidden" name="bonus_amount" id="bonus_amount" value="<?=$bonus_amount?>"/>
											<?php
											} ?>
										</div>
										<div class="col-md-6">
											<?php if($general_setting_data['promocode_section']=='1') { ?>
											<h5 class="price-coupon">
												<div>
													<input type="text" name="promo_code" id="promo_code" class="form-control promo_code" placeholder="Coupon: $10" autocomplete="nope" value="<?=$order_data['promocode']?>" <?php if($order_data['promocode']!=""){echo 'readonly="readonly"';}?> required>
													<button class="btn btn-link close-icon" type="reset"><img src="<?=SITE_URL?>images/icons/close-circle.png" alt=""></button>
													<a href="javascript:void(0);" name="apl_promo_code" id="apl_promo_code" class="apl_promo_code" onclick="getPromoCode();" <?php if($order_data['promocode']!=""){echo 'style="display:none;"';}?>><img class="status" src="<?=SITE_URL?>images/icons/tick.png" alt="tick"></a><span id="apl_promo_spining_icon"></span>
													<img class="coupon-icon" src="<?=SITE_URL?>images/cart/coupon.png" alt="coupon">
													<a class="promocode_removed" href="javascript:void(0);" id="promocode_removed" <?php if($promocode_amt<=0) {echo 'style="display:none;"';}?>>X</a>
												</div>
											</h5>
											<span class="showhide_promocode_msg" style="display:none;"><span class="promocode_msg"></span></span>
											<input type="hidden" name="promocode_id" id="promocode_id" value="<?=$order_data['promocode_id']?>"/>
											<input type="hidden" name="promocode_value" id="promocode_value" value="<?=$order_data['promocode']?>"/>
											<?php
											} ?>
											<p class="note">*We occasionally offer promo codes in our email blasts or Facebook page</p>
										</div>
									</div>
								</div>
								<div class="table-cell w-50 text-right d-flex align-items-center justify-content-end">
									<p>
										<button type="button" class="btn btn-primary btn-lg rounded-pill get-paid" data-toggle="modal" <?php if($user_id>0){echo 'data-target="#ShippingFields"';}else{echo 'data-target="#SignInRegistration"';}?>>Get Paid</button>
										<a href="<?=SITE_URL?>sell" class="btn btn-lg btn-outline-dark rounded-pill">Add Device</a>
									</p>
								</div>
							</div>
						</div>
			
            <?php /*?><table class="d-block cart-table-mobile d-md-none table table-borderless parent">
              <tr>
                <td class="total-cell"></td>
                <td class="cart-total-cell" colspan="2">
                  <h5 class="title">Expected payments:</h5>
                  <p>
				  	<?=implode(" + ",$item_price_array)?> = <span><?=amount_fomat($sum_of_orders)?></span>
				  </p>
				  <?php
				  if($user_id>0) { ?>
                  <p class="bonus"><img src="<?=SITE_URL?>images/icons/gift.png" alt=""> Bonus: 1% = $4.5</p>
				  <?php
				  } ?>
                  <h5 class="price-coupon">
                    <div>
                      <input type="text" class="form-control" placeholder="Coupon: $10" required>
                      <button class="btn btn-link close-icon" type="reset"><img src="<?=SITE_URL?>images/icons/close-circle.png" alt=""></button>
                      <img class="status" src="<?=SITE_URL?>images/icons/tick.png" alt="">
                      <img class="coupon-icon" src="<?=SITE_URL?>images/cart/coupon.png" alt="">
                    </div>
                  </h5>
                  <p class="note">*We occasionally offer promo codes in our email blasts or Facebook page</p>				
                </td>
                <td class="paid-cell" colspan="2">
                  <button class="btn btn-primary btn-lg rounded-pill" data-toggle="modal" <?php if($user_id>0){echo 'data-target="#ShippingFields"';}else{echo 'data-target="#SignInRegistration"';}?>>Get Paid</button>
                </td>
              </tr>
            </table><?php */?>
			
          </div>
        </div>
      </div>
    </div>
  </section>
<input id="order_id" name="order_id" value="<?=$order_id?>" type="hidden">
</form>

  <div class="modal fade" id="ShippingFields" tabindex="-1" role="dialog" aria-labelledby="ShippingFields" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title shipping_payment_label">Shipping Address</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <img src="<?=SITE_URL?>images/payment/close.png" alt="">
          </button>
        </div>
        <div class="modal-body pt-3 text-center shipping_form_section">
			<?php
			$shipping_first_name = $order_data['shipping_first_name'];
			$shipping_last_name = $order_data['shipping_last_name'];
			$shipping_company_name = $order_data['shipping_company_name'];
			if($user_data['first_name']) {
				$shipping_first_name = $user_data['first_name'];
			}
			if($user_data['last_name']) {
				$shipping_last_name = $user_data['last_name'];
			}
			if($user_data['company_name']) {
				$shipping_company_name = $user_data['company_name'];
			}

			$shipping_address = $order_data['shipping_address1'];
			$shipping_address2 = $order_data['shipping_address2'];
			$shipping_city = $order_data['shipping_city'];
			$shipping_state = $order_data['shipping_country'];
			$shipping_postcode = $order_data['shipping_postcode'];
			$shipping_phone = $order_data['shipping_phone'];
			$shipping_country_code = $order_data['shipping_country_code'];
			if($user_data['use_shipping_adddress_prefilled'] == '1' || $guest_user_id > 0) {
				if($user_data['address']) {
					$shipping_address = $user_data['address'];
				}
				if($user_data['address2']) {
					$shipping_address2 = $user_data['address2'];
				}
				if($user_data['city']) {
					$shipping_city = $user_data['city'];
				}
				if($user_data['state']) {
					$shipping_state = $user_data['state'];
				}
				if($user_data['postcode']) {
					$shipping_postcode = $user_data['postcode'];
				}
				if($user_data['phone']) {
					$shipping_phone = $user_data['phone'];
				}
				if($user_data['country_code']) {
					$shipping_country_code = $user_data['country_code'];
				}
			} ?>
          <form action="" class="sign-in needs-validation" id="shipping_form" novalidate>
            <div class="form-row">
              <div class="form-group col-md-6 with-icon">
                <img src="<?=SITE_URL?>images/icons/user-gray.png" alt="">
                <input type="text" class="form-control" name="shipping_first_name" id="shipping_first_name" placeholder="First Name" value="<?=$shipping_first_name?>" autocomplete="nope">
				<div id="shipping_first_name_error_msg" class="invalid-feedback m_validations_showhide" style="display:none;"></div>
              </div>
              <div class="form-group mt-0 col-md-6 with-icon">
                 <img src="<?=SITE_URL?>images/icons/user-gray.png" alt=""> 
				 <input type="text" class="form-control" name="shipping_last_name" id="shipping_last_name" value="<?=$shipping_last_name?>" autocomplete="nope" placeholder="Last Name" required>
				<div id="shipping_last_name_error_msg" class="invalid-feedback m_validations_showhide" style="display:none;"></div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group mt-3  col-md-6 with-icon">
                <img src="<?=SITE_URL?>images/icons/place-marker.png" alt="">
				<input type="text" class="form-control" name="shipping_address" id="shipping_address" value="<?=$shipping_address?>" autocomplete="nope" placeholder="Street address" required>
				<div id="shipping_address_error_msg" class="invalid-feedback m_validations_showhide" style="display:none;"></div>
              </div>
              <div class="form-group mt-3 col-md-6 with-icon">
                <img src="<?=SITE_URL?>images/icons/place-marker.png" alt="">
				<input type="text" class="form-control" name="shipping_address2" id="shipping_address2"  value="<?=$shipping_address2?>" autocomplete="nope" placeholder="Street address 2 (optional)">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group mt-3 col-md-6 with-icon">
                <img src="<?=SITE_URL?>images/icons/people.png" alt="">
				<input type="text" class="form-control" name="shipping_company_name" id="shipping_company_name"  value="<?=$shipping_company_name?>" autocomplete="nope" placeholder="Company (optional)">
              </div>
              <div class="form-group mt-3 col-md-6 with-icon">
                <img src="<?=SITE_URL?>images/icons/home.png" alt="">
				<input type="text" class="form-control" name="shipping_city" id="shipping_city" value="<?=$shipping_city?>" autocomplete="nope" placeholder="City" required>
				<div id="shipping_city_error_msg" class="invalid-feedback m_validations_showhide" style="display:none;"></div>
              </div>
            </div>
            <div class="form-row">
               <div class="form-group mt-3 col-md-6 with-icon">
                 <img src="<?=SITE_URL?>images/icons/state.png" alt="">
				 <input type="text" class="form-control" name="shipping_state" id="shipping_state" value="<?=$shipping_state?>" autocomplete="nope" placeholder="State" required>
				 <div id="shipping_state_error_msg" class="invalid-feedback m_validations_showhide" style="display:none;"></div>
               </div>
              <div class="form-group mt-3 col-md-6 with-icon">
                <img src="<?=SITE_URL?>images/icons/envelop.png" alt="">
				<input type="text" class="form-control" name="shipping_postcode" id="shipping_postcode" value="<?=$shipping_postcode?>" autocomplete="nope" placeholder="Zip code">
				<div id="shipping_postcode_error_msg" class="invalid-feedback m_validations_showhide" style="display:none;"></div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group mt-3 col-md-6 with-icon telephone-form">
                <img src="<?=SITE_URL?>images/icons/phone_dial.png" alt="">
				<input type="tel" id="shipping_phone" name="shipping_phone" class="form-control">
				<input type="hidden" name="shipping_phone_c_code" id="shipping_phone_c_code" value="<?=$shipping_country_code?>"/>
				<div id="shipping_phone_error_msg" class="invalid-feedback m_validations_showhide" style="display:none;"></div>
              </div>
            </div>
			<?php
			if($guest_user_id<=0) { ?>
            <div class="form-row">
              <div class="form-group mt-3 col-md-6">
                <div class="custom-control custom-checkbox">
				  <input type="checkbox" class="custom-control-input" name="save_as_primary_address" id="save_as_primary_address" value="1"/>
                  <label class="custom-control-label" for="save_as_primary_address">Save as my primary address</label>
                </div>
              </div>
            </div>
			<?php
			} ?>
            <div class="form-group double-btn pt-5 text-center">
              <button type="button" class="btn btn-primary btn-lg rounded-pill ml-lg-3 shipping_submit_btn">Continue</button>
            </div>
          </form>
        </div>
		
		<div class="modal-body text-center payment_form_section" style="display:none;">
          <ul class="nav nav-tabs" id="myTab" role="tablist">
		  	<?php
			$paypal_address = '';
			$payment_method_details = json_decode($user_data['payment_method_details'],true);
			$paypal_address = $payment_method_details['data']['paypal']['paypal_address'];
			/*if($user_data['use_payment_method_prefilled'] == '1') {
				$my_payment_option = $payment_method_details['my_payment_option'];
				if($my_payment_option) {
					$default_payment_option = $my_payment_option;
				}
				
				$paypal_address = $payment_method_details['data']['paypal']['paypal_address'];
			}*/

			if($choosed_payment_option['paypal']=="paypal") { ?>
			<li class="nav-item <?php if($default_payment_option=="paypal"){echo 'active';}?>">
              <a class="nav-link active" id="paypal-tab" data-toggle="tab" href="#paypal" role="tab" aria-controls="paypal" aria-selected="true">
				<p><img src="<?=SITE_URL?>images/payment/paypal.png" alt="paypal"></p>
				<!-- <i class="fas fa-check active-check"></i> -->
				<i class="fas fa-check-circle active-check"></i>
              </a>
            </li>
			<?php
			}
			if($choosed_payment_option['cheque']=="cheque") { ?>
            <li class="nav-item">
              <a class="nav-link <?php if($default_payment_option=="cheque"){echo 'active';}?>" id="cheque-tab" data-toggle="tab" href="#cheque" role="tab" aria-controls="cheque" aria-selected="false">
                <p>
                  <img src="<?=SITE_URL?>images/payment/bank.png" alt="bank">
                  <span class="name">Bank Check</span>
                  <!--<span class="status">(not available at the moment)</span>-->
                </p>
              </a>
            </li>
			<?php
			} if($choosed_payment_option['zelle']=="zelle") { ?>
				<li class="nav-item">
				  <a class="nav-link <?php if($default_payment_option=="zelle"){echo 'active';}?>" id="zelle-tab" data-toggle="tab" href="#zelle" role="tab" aria-controls="zelle" aria-selected="false">
					<p>
					  <img src="https://image.pngaaa.com/684/2183684-middle.png" alt="zelle"  height="85">
					  <span class="name">Zelle</span>
					  <!--<span class="status">(not available at the moment)</span>-->
					</p>
				  </a>
				</li>
				<?php
				} if($choosed_payment_option['cashapp']=="cashapp") { ?>
					<li class="nav-item">
					  <a class="nav-link <?php if($default_payment_option=="cashapp"){echo 'active';}?>" id="cashapp-tab" data-toggle="tab" href="#cashapp" role="tab" aria-controls="cashapp" aria-selected="false">
						<p>
						  <img src="https://1000logos.net/wp-content/uploads/2021/01/Cash-App-logo.png" alt="cashapp" height="85">
						  <span class="name">Cash App</span>
						  <!--<span class="status">(not available at the moment)</span>-->
						</p>
					  </a>
					</li>
					<?php
					} ?>
          </ul>
          <div class="tab-content" id="myTabContent">
		    <?php
			if($choosed_payment_option['paypal']=="paypal") { ?>
			<div class="tab-pane fade <?php if($default_payment_option=="paypal"){echo 'show active';}?>" id="paypal" role="tabpanel" aria-labelledby="paypal-tab">
              <form action="<?=SITE_URL?>controllers/cart/confirm.php" method="post" onSubmit="return confirm_sale_validation(this);">
                <div class="form-group">
					<input type="text" class="form-control" id="paypal_address" name="paypal_address" value="<?=$paypal_address?>" autocomplete="nope" placeholder="yourpaypal@adress.com">
					<div id="paypal_address_error_msg" class="invalid-feedback text-center m_validations_showhide" style="display:none;"></div>
					<div id="exist_paypal_address_msg" class="invalid-feedback text-center" style="display:none;"></div>
                </div>
                <div class="form-group">
					<input type="text" class="form-control" id="confirm_paypal_address" name="confirm_paypal_address" value="<?=$paypal_address?>" autocomplete="nope" placeholder="Repeat yourpaypal@adress.com">
					<div id="confirm_paypal_address_error_msg" class="invalid-feedback text-center m_validations_showhide" style="display:none;"></div>
                </div>
				<button type="button" class="btn btn-lg btn-outline-dark rounded-pill mr-lg-3 bk_shipping_form">Back</button>
                <button type="submit" class="btn btn-primary btn-lg rounded-pill confirm_sale_btn">Place Order 
									<span id="place_order_spining_icon"></span>
									<!-- <div class="spining-full-wrap"><div class="spining-icon"><i class="fa fa-spinner fa-spin"></i></div></div> -->
								</button>
				
				<input type="hidden" name="confirm_sale" value="yes"/>
				<input class="r_payment_method" name="payment_method" value="<?=$default_payment_option?>" type="hidden">
				<input type="hidden" name="num_of_item" id="num_of_item" value="<?=count($order_item_ids);?>"/>
              </form>
            </div>
			<?php
			}
			if($choosed_payment_option['cheque']=="cheque") { ?>
            <div class="tab-pane fade <?php if($default_payment_option=="cheque"){echo 'show active';}?>" id="cheque" role="tabpanel" aria-labelledby="cheque-tab">
              <form action="<?=SITE_URL?>controllers/cart/confirm.php" method="post" onSubmit="return confirm_sale_validation(this);">
				<button type="button" class="btn btn-lg btn-outline-dark rounded-pill mr-lg-3 bk_shipping_form">Back</button>
                <button type="submit" class="btn btn-primary btn-lg rounded-pill confirm_sale_btn">Place Order 
									<span id="place_order_spining_icon"></span>
									<!-- <div class="spining-full-wrap"><div class="spining-icon"><i class="fa fa-spinner fa-spin"></i></div></div> -->
								</button>
				
				<input type="hidden" name="confirm_sale" value="yes"/>
				<input class="r_payment_method" name="payment_method" value="<?=$default_payment_option?>" type="hidden">
				<input type="hidden" name="num_of_item" id="num_of_item" value="<?=count($order_item_ids);?>"/>
              </form>
            </div>
			<?php
			} if($choosed_payment_option['zelle']=="zelle") { ?>
				<div class="tab-pane fade <?php if($default_payment_option=="zelle"){echo 'show active';}?>" id="zelle" role="tabpanel" aria-labelledby="zelle-tab">
				  <form action="<?=SITE_URL?>controllers/cart/confirm.php" method="post" onSubmit="return confirm_sale_validation(this);">
					<div class="form-group">
						<input type="text" class="form-control" id="zelle_account_name" name="zelle_account_name" value="<?=$zelle_account_name?>" autocomplete="nope" placeholder="Account Name">
						<div id="zelle_account_name_error_msg" class="invalid-feedback text-center m_validations_showhide" style="display:none;"></div>
						<div id="exist_zelle_account_name_msg" class="invalid-feedback text-center" style="display:none;"></div>
					</div>
					<div class="form-group">
						<input type="text" class="form-control" id="zelle_phone" name="zelle_phone" value="<?=$zelle_phone?>" autocomplete="nope" placeholder="Phone Number">
						<div id="zelle_phone_error_msg" class="invalid-feedback text-center m_validations_showhide" style="display:none;"></div>
					</div>
					<button type="button" class="btn btn-lg btn-outline-dark rounded-pill mr-lg-3 bk_shipping_form">Back</button>
					<button type="submit" class="btn btn-primary btn-lg rounded-pill confirm_sale_btn">Place Order 
										<span id="place_order_spining_icon"></span>
									</button>
					
					<input type="hidden" name="confirm_sale" value="yes"/>
					<input class="r_payment_method" name="payment_method" value="<?=$default_payment_option?>" type="hidden">
					<input type="hidden" name="num_of_item" id="num_of_item" value="<?=count($order_item_ids);?>"/>
				  </form>
				</div>
				<?php
				} if($choosed_payment_option['cashapp']=="cashapp") { ?>
					<div class="tab-pane fade <?php if($default_payment_option=="cashapp"){echo 'show active';}?>" id="cashapp" role="tabpanel" aria-labelledby="cashapp-tab">
					  <form action="<?=SITE_URL?>controllers/cart/confirm.php" method="post" onSubmit="return confirm_sale_validation(this);">
					  <div class="form-group">
						<input type="text" class="form-control" id="cashapp_username" name="cashapp_username" value="<?=$cashapp_username?>" autocomplete="nope" placeholder="Username">
						<div id="username_error_msg" class="invalid-feedback text-center m_validations_showhide" style="display:none;"></div>
						<div id="exist_username_msg" class="invalid-feedback text-center" style="display:none;"></div>
					</div>
					<div class="form-group">
						<input type="text" class="form-control" id="cashapp_phone" name="cashapp_phone" value="<?=$cashapp_phone?>" autocomplete="nope" placeholder="Phone Number">
						<div id="cashapp_phone_error_msg" class="invalid-feedback text-center m_validations_showhide" style="display:none;"></div>
					</div>
						<button type="button" class="btn btn-lg btn-outline-dark rounded-pill mr-lg-3 bk_shipping_form">Back</button>
						<button type="submit" class="btn btn-primary btn-lg rounded-pill confirm_sale_btn">Place Order 
											<span id="place_order_spining_icon"></span>
											<!-- <div class="spining-full-wrap"><div class="spining-icon"><i class="fa fa-spinner fa-spin"></i></div></div> -->
										</button>
						
						<input type="hidden" name="confirm_sale" value="yes"/>
						<input class="r_payment_method" name="payment_method" value="<?=$default_payment_option?>" type="hidden">
						<input type="hidden" name="num_of_item" id="num_of_item" value="<?=count($order_item_ids);?>"/>
					  </form>
					</div>
					<?php
					}?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <input type="hidden"  id="shipping_country">
<script>
function getPromoCode()
{
	var promo_code = document.getElementById('promo_code').value.trim();
	if(promo_code=="") {
		jQuery("#promo_code").focus();
		return false;
	}

	post_data = "promo_code="+promo_code+"&amt=<?=$sum_of_orders?>&order_id=<?=$order_id?>&token=<?=unique_id()?>";
	jQuery(document).ready(function($) {
		jQuery("#apl_promo_spining_icon").html('<i class="fa fa-spinner fa-spin" style="font-size:16px;"></i>');
		$.ajax({
			type: "POST",
			url:"<?=SITE_URL?>ajax/promocode_verify.php",
			data:post_data,
			success:function(data) {
				$("#apl_promo_spining_icon").html('');
				if(data!="") {
					var resp_data = JSON.parse(data);
					if(resp_data.msg!="" && resp_data.mode == "expired") {
						$("#promo_code").val('');
						$("#showhide_promocode_row").hide();
						$("#promocode_id").val('');
						$("#promocode_value").val('');
						
						$(".showhide_promocode_msg").show();
						$(".promocode_msg").html('<div class="alert alert-warning alert-dismissable d-inline-block">'+resp_data.msg+'</div>');
					} else {
						$("#promocode_removed").show();
						$(".showhide_promocode_msg").hide();
						$(".promocode_msg").html('');
						$("#showhide_promocode_row").show();
						if(resp_data.coupon_type=='percentage') {
							$("#promocode_amt_label").html("("+resp_data.percentage_amt+"%)");
							$("#promocode_amt").html(resp_data.discount_of_amt);
						} else {
							$("#promocode_amt_label").html("");
							$("#promocode_amt").html(resp_data.discount_of_amt);
						}
						$("#promocode_id").val(resp_data.promocode_id);
						$("#promocode_value").val(resp_data.promocode_value);
						
						$("#apl_promo_code").attr("disabled", true);
						$("#promo_code").attr("readonly", true);
						$("#apl_promo_code").hide();
						check_update_cart();
					}
				}
			}
		});
	});
}

function check_shipping_form() {
	jQuery(".m_validations_showhide").hide();
	if(document.getElementById("shipping_first_name").value.trim()=="") {
		jQuery("#shipping_first_name_error_msg").show().text('Enter shipping first name');
		return false;
	} else if(document.getElementById("shipping_last_name").value.trim()=="") {
		jQuery("#shipping_last_name_error_msg").show().text('Enter shipping last name');
		return false;
	} else if(document.getElementById("shipping_address").value.trim()=="") {
		jQuery("#shipping_address_error_msg").show().text('Enter shipping address');
		return false;
	} else if(document.getElementById("shipping_city").value.trim()=="") {
		jQuery("#shipping_city_error_msg").show().text('Enter shipping city');
		return false;
	} else if(document.getElementById("shipping_state").value.trim()=="") {
		jQuery("#shipping_state_error_msg").show().text('Enter shipping state');
		return false;
	} else if(document.getElementById("shipping_postcode").value.trim()=="") {
		jQuery("#shipping_postcode_error_msg").show().text('Enter shipping zip code');
		return false;
	} else if(document.getElementById("shipping_phone").value.trim()=="") {
		jQuery("#shipping_phone_error_msg").show().text('Enter shipping phone');
		return false;
	}

	var telInput = jQuery("#shipping_phone");
	jQuery("#shipping_phone_c_code").val(telInput.intlTelInput("getSelectedCountryData").dialCode);
	if(!telInput.intlTelInput("isValidNumber")) {
		jQuery("#shipping_phone_error_msg").show().text('Area Code + Phone like: +19045551212');
		return false;
	}
}

function check_form() {
	jQuery(".m_validations_showhide").hide();				
	var payment_method = jQuery(".r_payment_method").val();
	<?php
	if($choosed_payment_option['paypal']=="paypal") { ?>
	if(payment_method=="paypal") {
		if(document.getElementById("paypal_address").value.trim()=="") {
			jQuery("#paypal_address_error_msg").show().text('Enter paypal address');
			return false;
		} else if(document.getElementById("confirm_paypal_address").value.trim()=="") {
			jQuery("#confirm_paypal_address_error_msg").show().text('Enter confirm paypal address');
			return false;
		} else if(document.getElementById("paypal_address").value.trim()!=document.getElementById("confirm_paypal_address").value.trim()) {
			jQuery("#paypal_address_error_msg").show().text('Does not match paypal address and confirm paypal address');
			return false;
		}
	}
	<?php
	} ?>
	<?php
	if($choosed_payment_option['zelle']=="zelle") { ?>
	if(payment_method=="zelle") {
		if(document.getElementById("zelle_account_name").value.trim()=="") {
			jQuery("#zelle_account_name_error_msg").show().text('Enter zelle Account name');
			return false;
		} else if(document.getElementById("zelle_phone").value.trim()=="") {
			jQuery("#zelle_phone_error_msg").show().text('Enter zelle phone number');
			return false;
		}
	}
	<?php
	} ?>
	<?php
	if($choosed_payment_option['cashapp']=="cashapp") { ?>
	if(payment_method=="cashapp") {
		if(document.getElementById("cashapp_username").value.trim()=="") {
			jQuery("#username_error_msg").show().text('Enter cashapp Username');
			return false;
		} else if(document.getElementById("cashapp_phone").value.trim()=="") {
			jQuery("#cashapp_phone_error_msg").show().text('Enter cashapp phone number');
			return false;
		}
	}
	<?php
	} ?>
}

function confirm_sale_validation() {
	var ok = check_form();
	if(ok == false) {
		return false;
	} else {
		jQuery("#place_order_spining_icon").html('<div class="spining-full-wrap"><div class="spining-icon"><i class="fa fa-spinner fa-spin"></i></div></div>');
		jQuery("#place_order_spining_icon").show();
		jQuery(".confirm_sale_btn").attr("disabled", true);

		/*var ok = confirm("Are you sure you want to submit order?");
		if(ok == false) {
			return false;
		}*/
	}
}

(function( $ ) {
	$(function() {

		var telInput2 = $("#shipping_phone");
		telInput2.intlTelInput({
		  //initialCountry: "auto",
		  geoIpLookup: function(callback) {
			$.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
			  var countryCode = (resp && resp.country) ? resp.country : "";
			  callback(countryCode);
			});
		  },
		  utilsScript: "<?=SITE_URL?>js/intlTelInput-utils.js"
		});
		
		$("#shipping_phone").intlTelInput("setNumber", "<?=($shipping_phone?'+'.$shipping_country_code.$shipping_phone:'')?>");

		$("#shipping_form").on('blur keyup change paste', 'input, select, textarea', function(event) {
			check_shipping_form();
		});
		$(".shipping_submit_btn").click(function() {
			var ok = check_shipping_form();
			if(ok == false) {
				return false;
			}

			$(".shipping_form_section").hide();
			$(".payment_form_section").show();
			$(".shipping_payment_label").html('Payment method');
			
			$.ajax({
				type: 'POST',
				url: '<?=SITE_URL?>ajax/order_shipping_method.php',
				data: $('#shipping_form').serialize(),
				success: function(data) {
					if(data!="") {
						var resp_data = JSON.parse(data);
						//console.log(resp_data);
					}
				}
			});
			return false;
		});

		$(".bk_shipping_form").click(function() {
			$(".shipping_form_section").show();
			$(".payment_form_section").hide();
			$(".shipping_payment_label").html('Shipping Address');
		});
		
		$("#paypal-tab").click(function() {
			$(".r_payment_method").val('paypal');
		});
		$("#cheque-tab").click(function() {
			$(".r_payment_method").val('cheque');
		});
		$("#zelle-tab").click(function() {
			$(".r_payment_method").val('zelle');
		});
		$("#cashapp-tab").click(function() {
			$(".r_payment_method").val('cashapp');
		});
		// $("#promocode_removed").hide();
		$("#promocode_removed").click(function() {
			$("#promo_code").val('');
			$("#showhide_promocode_row").hide();
			$("#promocode_id").val('');
			$("#promocode_value").val('');
			$("#apl_promo_code").attr("disabled", false);
			$("#promo_code").attr("readonly", false);
			$("#apl_promo_code").show();
			check_update_cart();
			$(this).hide();
		});

		$("#promo_code").on('keyup',function() {
			var promo_code = document.getElementById('promo_code').value.trim();
			if(promo_code!="") {
				$(".showhide_promocode_msg").hide();
				$(".promocode_msg").html('');
			}
		});
		
		<?php
		if($guest_user_id > 0) { ?>
		$("#paypal_address").on('keyup',function() {
			var paypal_address = $(this).val();
			$.ajax({
				type: 'POST',
				url: '<?=SITE_URL?>ajax/check_paypal_address.php',
				data: {email:paypal_address},
				success: function(data) {
					if(data!="") {
						var resp_data = JSON.parse(data);
						if(resp_data.msg!="" && resp_data.exist == true) {
							$("#exist_paypal_address_msg").show();
							$("#exist_paypal_address_msg").html(resp_data.msg);
							$(".confirm_sale_btn").attr("disabled", true);
						} else {
							$("#exist_paypal_address_msg").hide();
							$("#exist_paypal_address_msg").html('');
							$(".confirm_sale_btn").attr("disabled", false);
						}
					}
				}
			});
		});
		$(document).on('click', '.paypal_address_login', function() {
			$("#ShippingFields").modal('hide');
			$("#SignInRegistration").modal();
		});
		<?php
		}

		if($open_shipping_popup) {
			echo '$("#ShippingFields").modal();';
		} ?>
	});
})(jQuery);

function check_update_cart() {
	jQuery(document).ready(function($){
		$.ajax({
			type: 'POST',
			url: '<?=SITE_URL?>ajax/upt_promo_bonus.php',
			data: $('#revieworder_form').serialize(),
			success: function(data) {
				if(data!="") {
					var resp_data = JSON.parse(data);
					console.log(resp_data);
					return false;
				}
			}
		});
	});
}
check_update_cart();
</script>
<script>
      // This sample uses the Places Autocomplete widget to:
      // 1. Help the user select a place
      // 2. Retrieve the address components associated with that place
      // 3. Populate the form fields with those address components.
      // This sample requires the Places library, Maps JavaScript API.
      // Include the libraries=places parameter when you first load the API.
      // For example: <script
      // src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
      let autocomplete;
      let address1Field;
      let address2Field;
      let postalField;

      function initAutocomplete() {
        address1Field = document.querySelector("#shipping_address");
        address2Field = document.querySelector("#shipping_address2");
        postalField = document.querySelector("#shipping_postcode");
        // Create the autocomplete object, restricting the search predictions to
        // addresses in the US and Canada.
        autocomplete = new google.maps.places.Autocomplete(address1Field, {
          componentRestrictions: { country: ["us", "ca"] },
          fields: ["address_components", "geometry"],
          types: ["address"],
        });
        address1Field.focus();
        // When the user selects an address from the drop-down, populate the
        // address fields in the form.
        autocomplete.addListener("place_changed", fillInAddress);
      }

      function fillInAddress() {
        // Get the place details from the autocomplete object.
        const place = autocomplete.getPlace();
        let address1 = "";
        let postcode = "";

        // Get each component of the address from the place details,
        // and then fill-in the corresponding field on the form.
        // place.address_components are google.maps.GeocoderAddressComponent objects
        // which are documented at http://goo.gle/3l5i5Mr
        for (const component of place.address_components) {
          const componentType = component.types[0];

          switch (componentType) {
            case "street_number": {
              address1 = `${component.long_name} ${address1}`;
              break;
            }

            case "route": {
              address1 += component.short_name;
              break;
            }

            case "postal_code": {
              postcode = `${component.long_name}${postcode}`;
              break;
            }

            case "postal_code_suffix": {
              postcode = `${postcode}-${component.long_name}`;
              break;
            }
            case "locality":
              document.querySelector("#shipping_city").value = component.long_name;
              break;

            case "administrative_area_level_1": {
              document.querySelector("#shipping_state").value = component.short_name;
              break;
            }
            case "country":
              document.querySelector("#shipping_country").value = component.long_name;
              break;
          }
        }
        address1Field.value = address1;
        postalField.value = postcode;
        // After filling the form with address components from the Autocomplete
        // prediction, set cursor focus on the second address line to encourage
        // entry of subpremise information such as apartment, unit, or floor number.
        address2Field.focus();
      }
    </script>
    <!--<script-->
    <!--  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFvkfXaHULvziFdHwe0wsX2ij5RqbQH8I&callback=initAutocomplete&libraries=places"-->
    <!--  async-->
    <!--</script>-->
       <script
      src="https://maps.googleapis.com/maps/api/js?key&callback=initAutocomplete&libraries=places"
      async
    ></script>
<?php $_SESSION['cart_shipp_popup'] = 1; if($user_id>0){ if(!empty($_SESSION['cart_shipp_popup'])) {  ?>
      	<script>
      	$(document).ready(function(){
      	    jQuery('#ShippingFields').modal('show');
      	});
      	</script>
    <?php }  } ?>