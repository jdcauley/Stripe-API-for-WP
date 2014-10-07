<?php
  /*
    Plugin Name: Stripe for Devs
  */

require_once('stripe/lib/Stripe.php');

Stripe::setApiKey("");


/* Call Stripe.js */
function stripe_scripts() {
	wp_enqueue_script( 'stripe', 'https://js.stripe.com/v2/', false, null, false);
}
add_action( 'wp_enqueue_scripts', 'stripe_scripts' );

function stripe_create_customer($token, $email, $tier){
	$customer = Stripe_Customer::create(array(
		"description" => "",
		"card" => $token, // obtained with Stripe.js
		"email" => $email,
		// "plan" => $tier
	));
  if($customer->id){
    return $customer;
  } else {
    return null;
  }
}

function stripe_delete_customer($customer){
  $cu = Stripe_Customer::retrieve($customer);
	$is_deleted = $cu->delete();

  return $is_deleted;
}

function stripe_create_card($customer, $token){
	$cu = Stripe_Customer::retrieve($customer);
	$cu->card = $token; // obtained with Stripe.js
	$updated_customer = $cu->save();

	return $updated_customer;
}

function stripe_default_card($customer){
	$cards = $customer->cards->data;
	$default_card = $customer->default_card;
	$the_card = array();

	for($i = 0; $i < count($cards); ++$i) {
		if($cards[$i]['id'] === $default_card){
			$the_card = $cards[$i];
		}
	}
	return $the_card;
}
