<?php

namespace YayMail\Helper;

defined( 'ABSPATH' ) || exit;
class PluginSupported {
	public static function listAddonSupported() {
		$arrAddonSupported = array(
			'WC_Subscription'                              => array(
				'plugin_name'   => 'WooCommerce Subscriptions',
				'template_name' => array(
					'cancelled_subscription',
					'expired_subscription',
					'suspended_subscription',
					'customer_completed_renewal_order',
					'customer_completed_switch_order',
					'customer_on_hold_renewal_order',
					'customer_renewal_invoice',
					'new_renewal_order',
					'new_switch_order',
					'customer_processing_renewal_order',
					'customer_payment_retry',
					'payment_retry',
					'_enr_customer_auto_renewal_reminder',
					'_enr_customer_expiry_reminder',
					'_enr_customer_manual_renewal_reminder',
					'_enr_customer_processing_shipping_fulfilment_order',
					'_enr_customer_shipping_frequency_notification',
					'_enr_customer_subscription_price_updated',
					'_enr_customer_trial_ending_reminder',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-woocommerce-subscriptions',
			),
			'yith_wishlist_constructor'                    => array(
				'plugin_name'   => 'YITH WooCommerce Wishlist Premium',
				'template_name' => array(
					'estimate_mail',
					'yith_wcwl_back_in_stock',
					'yith_wcwl_on_sale_item',
					'yith_wcwl_promotion_mail',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-yith-woocommerce-wishlist',
			),
			'SUMO_Subscription'                            => array(
				'plugin_name'   => 'SUMO Subscription',
				'template_name' => array(
					'subscription_new_order',
					'subscription_new_order_old_subscribers',
					'subscription_processing_order',
					'subscription_completed_order',
					'subscription_pause_order',
					'subscription_invoice_order_manual',
					'subscription_expiry_reminder',
					'subscription_automatic_charging_reminder',
					'subscription_renewed_order_automatic',
					'auto_to_manual_subscription_renewal',
					'subscription_overdue_order_automatic',
					'subscription_overdue_order_manual',
					'subscription_suspended_order_automatic',
					'subscription_suspended_order_manual',
					'subscription_preapproval_access_revoked',
					'subscription_turnoff_automatic_payments_success',
					'subscription_pending_authorization',
					'subscription_cancel_order',
					'subscription_cancel_request_submitted',
					'subscription_cancel_request_revoked',
					'subscription_expired_order',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-sumo-subscription',
			),

			'YITH_Subscription'                            => array(
				'plugin_name'   => 'YITH WooCommerce Subscription Premium',
				'template_name' => array(
					'ywsbs_subscription_admin_mail',
					'ywsbs_customer_subscription_cancelled',
					'ywsbs_customer_subscription_suspended',
					'ywsbs_customer_subscription_expired',
					'ywsbs_customer_subscription_before_expired',
					'ywsbs_customer_subscription_paused',
					'ywsbs_customer_subscription_resumed',
					'ywsbs_customer_subscription_request_payment',
					'ywsbs_customer_subscription_renew_reminder',
					'ywsbs_customer_subscription_payment_done',
					'ywsbs_customer_subscription_payment_failed',
					'ywsbs_customer_subscription_delivery_schedules',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-yith-woocommerce-subscription',
			),
			'woo-b2b'                                      => array(
				'plugin_name'   => 'WooCommerce B2B',
				'template_name' => array(
					'wcb2b_customer_onquote_order',
					'wcb2b_customer_quoted_order',
					'wcb2b_customer_status_notification',
					'wcb2b_new_quote',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-woocommerce-b2b',
			),
			'YITH_Vendors'                                 => array(
				'plugin_name'   => 'YITH WooCommerce Multi Vendor Premium',
				'template_name' => array(
					'cancelled_order_to_vendor',
					'commissions_paid',
					'commissions_unpaid',
					'new_order_to_vendor',
					'new_vendor_registration',
					'product_set_in_pending_review',
					'vendor_commissions_bulk_action',
					'vendor_commissions_paid',
					'vendor_new_account',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-yith-woocommerce-multi-vendor',
			),
			'Germanized_Pro'                               => array(
				'plugin_name'   => 'Germanized for WooCommerce',
				'template_name' => array(
					'sab_cancellation_invoice',
					'sab_document',
					'sab_document_admin',
					'sab_simple_invoice',
					'sab_packing_slip',
					'customer_paid_for_order',
					'customer_cancelled_order',
					'customer_order_confirmation',
					'customer_revocation',
					'customer_new_account_activation',
					'customer_shipment',
					'customer_partial_shipment',
					'customer_return_shipment',
					'customer_return_shipment_delivered',
					'new_return_shipment_request',
					'customer_trusted_shops',
					'customer_sepa_direct_debit_mandate',
					'customer_guest_return_shipment_request',
					'oss_delivery_threshold_email_notification',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-woocommerce-germanized',
			),
			'WC_Bookings'                                  => array(
				'plugin_name'   => 'WooCommerce Bookings',
				'template_name' => array(
					'admin_booking_cancelled',
					'booking_cancelled',
					'booking_confirmed',
					'booking_notification',
					'booking_pending_confirmation',
					'booking_reminder',
					'new_booking',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-woocommerce-bookings',
			),
			'WooCommerce_Waitlist'                         => array(
				'plugin_name'   => 'WooCommerce Waitlist',
				'template_name' => array(
					'woocommerce_waitlist_joined_email',
					'woocommerce_waitlist_left_email',
					'woocommerce_waitlist_mailout',
					'woocommerce_waitlist_signup_email',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-woocommerce-waitlist',
			),
			'WooCommerce_Quotes'                           => array(
				'plugin_name'   => 'Quotes for WooCommerce',
				'template_name' => array(
					'qwc_req_new_quote',
					'qwc_request_sent',
					'qwc_send_quote',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-quotes-for-woocommerce',
			),
			'YITH_Pre_Order'                               => array(
				'plugin_name'   => 'YITH Pre-Order for WooCommerce Premium ',
				'template_name' => array(
					'yith_ywpo_date_end',
					'yith_ywpo_sale_date_changed',
					'yith_ywpo_is_for_sale',
					'yith_ywpo_out_of_stock',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-yith-woocommerce-pre-order',
			),
			'WooCommerce_Appointments'                     => array(
				'plugin_name'   => 'WooCommerce Appointments',
				'template_name' => array(
					'admin_appointment_cancelled',
					'admin_new_appointment',
					'appointment_cancelled',
					'appointment_confirmed',
					'appointment_follow_up',
					'appointment_reminder',
					'admin_appointment_rescheduled',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-woocommerce-appointments',
			),
			'SG_Order_Approval'                            => array(
				'plugin_name'   => 'Sg Order Approval for Woocommerce',
				'template_name' => array(
					'wc_admin_order_new',
					'wc_customer_order_new',
					'wc_customer_order_approved',
					'wc_customer_order_rejected',
					'sgitsoa_wc_admin_order_new',
					'sgitsoa_wc_customer_order_new',
					'sgitsoa_wc_customer_order_approved',
					'sgitsoa_wc_customer_order_rejected',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-sg-woocommerce-order-approval',
			),
			'Follow_Up_Emails'                             => array(
				'plugin_name'   => 'Follow Up Emails for WooCommerce ',
				'template_name' => apply_filters( 'YaymailCreateListFollowUpNames', array() ),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-woocommerce-follow-up-emails',
			),
			'Order_Delivery_Date'                          => array(
				'plugin_name'   => 'Order Delivery Date for WooCommerce',
				'template_name' => array(
					'orddd_lite_update_date',
					'orddd_delivery_reminder',
					'orddd_delivery_reminder_customer',
					'orddd_update_date',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-woocommerce-order-delivery-date',
			),
			'WC_Email_Cancelled_Customer_Order'            => array(
				'plugin_name'   => 'Order Cancellation Email to Customer',
				'template_name' => array(
					'wc_customer_cancelled_order',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-order-cancellation-email-to-customer',
			),
			'WC_Smart_Coupons'                             => array(
				'plugin_name'   => 'WooCommerce Smart Coupons',
				'template_name' => array(
					'wc_sc_combined_email_coupon',
					'wc_sc_acknowledgement_email',
					'wc_sc_email_coupon',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-smart-coupons',
			),
			'Dokan'                                        => array(
				'plugin_name'   => 'Dokan',
				'template_name' => array(
					'dokan_new_seller',
					'dokan_email_vendor_disable',
					'dokan_email_vendor_enable',
					'dokan_contact_seller',
					'new_product',
					'new_product_pending',
					'pending_product_published',
					'updated_product_pending',
					'dokan_vendor_new_order',
					'dokan_vendor_completed_order',
					'dokan_vendor_withdraw_request',
					'dokan_vendor_withdraw_cancelled',
					'dokan_vendor_withdraw_approved',
					'dokan_refund_request',
					'dokan_vendor_refund',
					'dokan_announcement',
					'dokan_staff_new_order',
					'Dokan_Email_Wholesale_Register',
					'dokan_email_shipping_status_tracking',
					'dokan_email_subscription_invoice',
					'updates_for_store_followers',
					'vendor_new_store_follower',
					'dokan_product_enquiry_email',
					'dokan_report_abuse_admin_email',
					'Dokan_Send_Coupon_Email',
					'Dokan_Rma_Send_Warranty_Request',
					'dokan_new_support_ticket',
					'dokan_reply_to_store_support_ticket',
					'dokan_reply_to_user_support_ticket',
					'dokan_vendor_refund_canceled',
					'Dokan_Email_Booking_New',
					'Dokan_Email_Booking_Cancelled_NEW',
					'DokanNewSupportTicketForAdmin',
					'DokanReplyToAdminSupportTicket_vendor_customer',
					'reverse_withdrawal_invoice',
					'dokan_staff_password_update',
					'dokan_new_store_review',
					'Dokan_Subscription_Cancelled',
					'dokan_update_admin_order_delivery_time',
					'dokan_update_vendor_order_delivery_time',
					'dokan_request_new_quote',
					'dokan_request_update_quote',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-dokan',
			),
			'Woocommerce_German_Market'                    => array(
				'plugin_name'   => 'Woocommerce_German_Market',
				'template_name' => array(
					'wgm_confirm_order_email',
					'wgm_double_opt_in_customer_registration',
					'wgm_sepa',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-german-market',
			),
			'B2B_Wholesale_Suite'                          => array(
				'plugin_name'   => 'B2B & Wholesale Suite',
				'template_name' => array(
					'b2bwhs_new_customer_email',
					'b2bwhs_new_customer_requires_approval_email',
					'b2bwhs_new_message_email',
					'b2bwhs_new_quote_email',
					'b2bwhs_your_account_approved_email',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-b2b-wholesale-suite',
			),
			'WooCommerce_Deposits'                         => array(
				'plugin_name'   => 'WooCommerce Deposits',
				'template_name' => array(
					'customer_deposit_partially_paid',
					'customer_partially_paid',
					'customer_second_payment_reminder',
					'full_payment',
					'partial_payment',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-woocommerce-deposits',
			),
			'YITH_Booking'                                 => array(
				'plugin_name'   => 'YITH Booking and Appointment for WooCommerce Premium',
				'template_name' => array(
					'yith_wcbk_admin_new_booking',
					'yith_wcbk_booking_status',
					'yith_wcbk_customer_booking_note',
					'yith_wcbk_customer_cancelled_booking',
					'yith_wcbk_customer_completed_booking',
					'yith_wcbk_customer_confirmed_booking',
					'yith_wcbk_customer_new_booking',
					'yith_wcbk_customer_paid_booking',
					'yith_wcbk_customer_unconfirmed_booking',
					'yith_wcbk_booking_status_vendor',
					'yith_wcbk_vendor_new_booking',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-yith-booking-and-appointment-for-woocommerce',
			),
			'WooCommerce_Points_Rewards'                   => array(
				'plugin_name'   => 'Points and Rewards for WooCommerce',
				'template_name' => array(
					'mwb_wpr_email_notification',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-points-and-rewards-for-woocommerce',
			),
			'PW_WooCommerce_Gift_Cards'                    => array(
				'plugin_name'   => 'PW WooCommerce Gift Cards',
				'template_name' => array(
					'pwgc_email',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-pw-woocommerce-gift-cards',
			),
			'YITH_WooCommerce_Gift_Cards_Premium'          => array(
				'plugin_name'   => 'YITH WooCommerce Gift Cards Premium',
				'template_name' => array(
					'ywgc-email-delivered-gift-card',
					'ywgc-email-send-gift-card',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-yith-woocommerce-gift-cards',
			),
			'YITH_WooCommerce_Membership_Premium'          => array(
				'plugin_name'   => 'YITH WooCommerce Membership Premium',
				'template_name' => array(
					'membership_cancelled',
					'membership_expired',
					'membership_expiring',
					'membership_welcome',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-yith-woocommerce-membership',
			),
			'WooCommerce_Order_Delivery'                   => array(
				'plugin_name'   => 'WooCommerce Order Delivery',
				'template_name' => array(
					'subscription_delivery_note',
					'order_delivery_note',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-order-delivery-for-woocommerce',
			),
			'WooCommerce_Simple_Auction'                   => array(
				'plugin_name'   => 'WooCommerce Simple Auction',
				'template_name' => array(
					'auction_buy_now',
					'auction_closing_soon',
					'auction_fail',
					'auction_finished',
					'auction_relist',
					'auction_relist_user',
					'auction_win',
					'bid_note',
					'customer_bid_note',
					'outbid_note',
					'remind_to_pay',
					'Reserve_fail',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-woocommerce-simple-auction',
			),
			'vendors_marketplace'                          => array(
				'plugin_name'   => 'Vendors marketplace',
				'template_name' => array(
					'admin_notify_approved',
					'admin_notify_application',
					'admin_notify_product',
					'admin_notify_shipped',
					'customer_notify_shipped',
					'vendor_notify_application',
					'vendor_notify_approved',
					'vendor_notify_cancelled_order',
					'vendor_notify_denied',
					'vendor_notify_order',
					'vendor_application',
					'admin_new_vendor_product',
					'vendor_notify_shipped',
					'vendor_new_order',
					'customer-mark-received',
					'store_contact',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-wcvendors',
			),
			'WooCommerce_Pre_Orders'                       => array(
				'plugin_name'   => 'WooCommerce Pre-Orders',
				'template_name' => array(
					'wc_pre_orders_new_pre_order',
					'wc_pre_orders_pre_order_available',
					'wc_pre_orders_pre_order_cancelled',
					'wc_pre_orders_pre_order_date_changed',
					'wc_pre_orders_pre_ordered',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-woocommerce-pre-order',
			),
			'WooCommerce_Split_Orders'                     => array(
				'plugin_name'   => 'WooCommerce Split Orders',
				'template_name' => array(
					'customer_order_split',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-woocommerce-split-order',
			),
			'WP_Crowdfunding'                              => array(
				'plugin_name'   => 'WP Crowdfunding',
				'template_name' => array(
					'wp_crowdfunding_campaign_accept',
					'wp_crowdfunding_submit_campaign',
					'wp_crowdfunding_campaign_update_email',
					'wp_crowdfunding_new_backed',
					'wp_crowdfunding_new_user',
					'wp_crowdfunding_target_reached_email',
					'wp_crowdfunding_withdraw_request',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-wp-crowdfunding',
			),
			'WC_PIP_Loader'                                => array(
				'plugin_name'   => 'Woocommerce Print Invoices & Packing lists',
				'template_name' => array(
					'pip_email_invoice',
					'pip_email_packing_list',
					'pip_email_pick_list',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-woocommerce-print-invoices-packing-lists',
			),
			'LMFWC'                                        => array(
				'plugin_name'   => 'License Manager for WooCommerce',
				'template_name' => array(
					'lmfwc_email_customer_deliver_license_keys',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-license-manager',
			),
			'WooCommerce_Account_Funds'                    => array(
				'plugin_name'   => 'WooCommerce Account Funds',
				'template_name' => array(
					'wc_account_funds_increase',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-account-funds',
			),
			'WooCommerce_Gift_Cards'                       => array(
				'plugin_name'   => 'WooCommerce Gift Cards',
				'template_name' => array(
					'gift_card_received',
					'gift_card_send_to_buyer',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-woocommerce-gift-cards',
			),
			'AutomateWoo'                                  => array(
				'plugin_name'   => 'AutomateWoo',
				'template_name' => apply_filters( 'YaymailCreateListAutomateWooNames', array() ),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-automatewoo',
			),
			'WooCommerce_Stripe_Gateway'                   => array(
				'plugin_name'   => 'WooCommerce Stripe Gateway',
				'template_name' => array(
					'failed_authentication_requested',
					'failed_preorder_sca_authentication',
					'failed_renewal_authentication',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-woocommerce-stripe-payment-gateway',
			),
			'YITH_WCSTRIPE'                                => array(
				'plugin_name'   => 'YITH Woocommerce Stripe',
				'template_name' => array(
					'renew_needs_action',
					'expiring_card',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-yith-woocommerce-stripe',
			),
			'WooCommerce_Multivendor_Marketplace'          => array(
				'plugin_name'   => 'WooCommerce Multivendor Marketplace',
				'template_name' => array(
					'new-enquiry',
					'store-new-order',
					'email-verification',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-wcfm-marketplace',
			),
			'WooCommerce_Memberships'                      => array(
				'plugin_name'   => 'WooCommerce Memberships',
				'template_name' => array(
					'WC_Memberships_User_Membership_Activated_Email',
					'WC_Memberships_User_Membership_Ended_Email',
					'WC_Memberships_User_Membership_Ending_Soon_Email',
					'WC_Memberships_User_Membership_Note_Email',
					'WC_Memberships_User_Membership_Renewal_Reminder_Email',
					'wc_memberships_for_teams_team_invitation',
					'wc_memberships_for_teams_team_membership_ended',
					'wc_memberships_for_teams_team_membership_ending_soon',
					'wc_memberships_for_teams_team_membership_renewal_reminder',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-woocommerce-memberships',
			),
			'TrackShip_for_WooCommerce'                    => array(
				'plugin_name'   => 'TrackShip for WooCommerce',
				'template_name' => apply_filters( 'YaymailCreateListTrackShipWooNames', array() ),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-trackship-for-woocommerce',
			),
			'AliDropship_Woo_Plugin'                       => array(
				'plugin_name'   => 'AliDropship Woo Plugin',
				'template_name' => array(
					'adsw_order_shipped_notification',
					'adsw_order_tracking_changed_notification',
					'adsw_update_notification',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-alidropship-woo-plugin',
			),
			'YITH_WooCommerce_Review_For_Discounts_Premium' => array(
				'plugin_name'   => 'YITH WooCommerce Review For Discounts Premium',
				'template_name' => array(
					'yith-review-for-discounts',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-yith-review-for-discounts',
			),
			'SUMO_Payment_Plans'                           => array(
				'plugin_name'   => 'SUMO Payment Plans',
				'template_name' => array(
					'_sumo_pp_deposit_balance_payment_auto_charge_reminder',
					'_sumo_pp_deposit_balance_payment_completed',
					'_sumo_pp_deposit_balance_payment_invoice',
					'_sumo_pp_deposit_balance_payment_overdue',
					'_sumo_pp_payment_awaiting_cancel',
					'_sumo_pp_payment_cancelled',
					'_sumo_pp_payment_pending_auth',
					'_sumo_pp_payment_plan_auto_charge_reminder',
					'_sumo_pp_payment_plan_completed',
					'_sumo_pp_payment_plan_invoice',
					'_sumo_pp_payment_plan_overdue',
					'_sumo_pp_payment_plan_success',
					'_sumo_pp_payment_schedule',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-sumo-payment-plans',
			),
			'TeraWallet'                                   => array(
				'plugin_name'   => 'TeraWallet',
				'template_name' => array(
					'new_wallet_transaction',
					'low_wallet_balance',
					'woo_wallet_withdraw_approved',
					'woo_wallet_withdraw_rejected',
					'woo_wallet_withdrawal_request',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-terawallet',
			),
			'CustomFieldsforWooCommerce'                   => array(
				'plugin_name'   => 'Custom Fields for WooCommerce by Addify',
				'template_name' => array(
					'af_email_admin_register_new_user',
					'af_email_approve_user_account',
					'af_email_declined_user_account',
					'af_email_register_new_account',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-custom-fields-by-addify',
			),
			'WooCommerceMultiLocationInventory'            => array(
				'plugin_name'   => 'WooCommerce MultiLocation Inventory & Order Routing',
				'template_name' => array(
					'wh_new_order',
					'wh_reassign',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-woocommerce-multi-warehouse-order-routing',
			),
			'MultivendorMarketplaceSolutionWooCommerce'    => array(
				'plugin_name'   => 'Multivendor Marketplace Solution for WooCommerce - WC Marketplace',
				'template_name' => array(
					'admin_added_new_product_to_vendor',
					'admin_change_order_status',
					'admin_new_question',
					'admin_new_vendor',
					'admin_widthdrawal_request',
					'approved_vendor_new_account',
					'customer_answer',
					'notify_shipped',
					'rejected_vendor_new_account',
					'wcmp_send_report_abuse',
					'suspend_vendor_new_account',
					'vendor_commissions_transaction',
					'vendor_contact_widget_email',
					'vendor_direct_bank',
					'vendor_followed',
					'vendor_new_account',
					'vendor_new_announcement',
					'admin_new_vendor_coupon',
					'vendor_new_order',
					'admin_new_vendor_product',
					'vendor_new_question',
					'vendor_orders_stats_report',
					'admin_vendor_product_rejected',
					'review_vendor_alert',
					'customer_order_refund_request',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-wc-marketplace',
			),
			'AffiliateForWooCommerce'                      => array(
				'plugin_name'   => 'Affiliate For WooCommerce',
				'template_name' => array(
					'afwc_affiliate_pending_request',
					'afwc_commission_paid',
					'afwc_new_conversion',
					'afwc_new_registration',
					'afwc_welcome',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-affiliate-for-woocommerce',
			),
			'WooCommerceProductVendors'                    => array(
				'plugin_name'   => 'WooCommerce Product Vendors',
				'template_name' => array(
					'vendor_approval',
					'cancelled_order_email_to_vendor',
					'order_email_to_vendor',
					'order_fulfill_status_to_admin',
					'order_note_to_customer',
					'product_added_notice',
					'vendor_registration_to_admin',
					'vendor_registration_to_vendor',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-woocommerce-product-vendors',
			),
			'WooCommerceBackInStockNotifications'          => array(
				'plugin_name'   => 'WooCommerce Back In Stock Notifications',
				'template_name' => array(
					'bis_notification_confirm',
					'bis_notification_received',
					'bis_notification_verify',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-back-in-stock-notifications',
			),
			'WooCommerceReturnandWarrrantyPro'             => array(
				'plugin_name'   => 'WooCommerce Return and Warrranty Pro',
				'template_name' => array(
					'WCRW_Send_Coupon_Email',
					'WCRW_Send_Message_Email',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-woocommerce-return-and-warranty',
			),
			'B2BKing'                                      => array(
				'plugin_name'   => 'B2BKing',
				'template_name' => array(
					'b2bking_new_customer_email',
					'b2bking_new_customer_requires_approval_email',
					'b2bking_new_message_email',
					'b2bking_new_offer_email',
					'b2bking_your_account_approved_email',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-b2bking',
			),
			'Domina_Shipping'                              => array(
				'plugin_name'   => 'Domina Shipping',
				'template_name' => array(
					'Domina_Email_Tracking',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-domina-shipping',
			),
			'YITH_WooCommerce_Delivery_Date_Premium'       => array(
				'plugin_name'   => 'YITH WooCommerce Delivery Date Premium',
				'template_name' => array(
					'yith_advise_user_delivery_email',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-yith-woocommerce-delivery-date',
			),
			'YITH_Advanced_Refund_System_for_WooCommerce_Premium' => array(
				'plugin_name'   => 'YITH Advanced Refund System for WooCommerce Premium',
				'template_name' => array(
					'yith_ywcars_approved_user_email',
					'yith_ywcars_coupon_user_email',
					'yith_ywcars_new_message_admin_email',
					'yith_ywcars_new_message_user_email',
					'yith_ywcars_new_request_admin_email',
					'yith_ywcars_new_request_user_email',
					'yith_ywcars_on_hold_user_email',
					'yith_ywcars_processing_user_email',
					'yith_ywcars_rejected_user_email',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-yith-advanced-refund-system',
			),
			'YITH_WooCommerce_Affiliates_Premium'          => array(
				'plugin_name'   => 'YITH WooCommerce Affiliates Premium',
				'template_name' => array(
					'new_affiliate',
					'payment_sent',
					'pending_commission',
					'affiliate_ban',
					'affiliate_new_coupon',
					'customer_payment_sent',
					'customer_pending_commission',
					'affiliate_status_changed',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-yith-woocommerce-affiliates',
			),
			'YITH_Auctions_for_WooCommerce_Premium'        => array(
				'plugin_name'   => 'YITH Auctions for WooCommerce Premium',
				'template_name' => array(
					'yith_wcact_email_auction_no_winner',
					'yith_wcact_email_auction_rescheduled_admin',
					'yith_wcact_email_auction_winner',
					'yith_wcact_email_auction_winner_reminder',
					'yith_wcact_email_better_bid',
					'yith_wcact_email_closed_buy_now',
					'yith_wcact_email_auction_delete_bid',
					'yith_wcact_email_delete_bid_admin',
					'yith_wcact_email_end_auction',
					'yith_wcact_email_new_bid',
					'yith_wcact_email_not_reached_reserve_price',
					'yith_wcact_email_not_reached_reserve_price_max_bidder',
					'yith_wcact_email_successfully_bid',
					'yith_wcact_email_successfully_bid_admin',
					'yith_wcact_email_successfully_follow',
					'yith_wcact_email_winner_admin',
					'yith_wcact_email_without_bid',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-yith-auctions-for-woocommerce',
			),
			'RMA_Return_Refund_Exchange_for_WooCommerce_Pro' => array(
				'plugin_name'   => 'RMA Return Refund & Exchange for WooCommerce Pro',
				'template_name' => array(
					'wps_rma_cancel_request_email',
					'wps_rma_exchange_request_accept_email',
					'wps_rma_exchange_request_cancel_email',
					'wps_rma_exchange_request_email',
					'wps_rma_order_messages_email',
					'wps_rma_refund_email',
					'wps_rma_refund_request_accept_email',
					'wps_rma_refund_request_cancel_email',
					'wps_rma_refund_request_email',
					'wps_rma_returnship_email',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-rma-return-refund-and-exchange-for-woocommerce',
			),
			'YITH_WooCommerce_Points_and_Rewards_Premium'  => array(
				'plugin_name'   => 'YITH WooCommerce Points and Rewards Premium',
				'template_name' => array(
					'ywpar_expiration',
					'ywpar_update_points',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-yith-points-and-rewards',
			),
			'WooCommerce_PDF_Product_Vouchers'             => array(
				'plugin_name'   => 'WooCommerce PDF Product Vouchers',
				'template_name' => array(
					'wc_pdf_product_vouchers_voucher_purchaser',
					'wc_pdf_product_vouchers_voucher_recipient',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-woocommerce-pdf-product-vouchers',
			),
			'YITH_WooCommerce_Request_A_Quote_Premium'     => array(
				'plugin_name'   => 'YITH WooCommerce Request A Quote Premium',
				'template_name' => array(
					'ywraq_quote_status',
					'ywraq_email',
					'ywraq_email_customer',
					'ywraq_send_quote',
					'ywraq_send_quote_reminder',
					'ywraq_send_quote_reminder_accept',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-yith-woocommerce-request-a-quote',
			),
			'Woo_Sell_Services'                            => array(
				'plugin_name'   => 'Woo Sell Services',
				'template_name' => array(
					'order_accepted',
					'order_conversation_ready',
					'order_ready',
					'order_rejected',
					'submit_requirement',
					'requirements_received',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-woo-sell-services',
			),
			'YITH_WC_Recover_Abandoned_Cart'               => array(
				'plugin_name'   => 'YITH WooCommerce Recover Abandoned Cart Premium',
				'template_name' => array(
					'ywrac_email',
					'ywrac_email_recovered_cart',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-yith-woocommerce-recover-abandoned-cart',
			),
			'YITH_WooCommerce_Coupon_Email_System_Premium' => array(
				'plugin_name'   => 'YITH WooCommerce Coupon Email System Premium',
				'template_name' => array(
					'yith-coupon-email-system',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-yith-woocommerce-coupon-email-system',
			),
			'YITH_Easy_Login_Register'                     => array(
				'plugin_name'   => 'YITH Easy Login & Register Popup For WooCommerce',
				'template_name' => array(
					'yith_welrp_customer_authentication_code',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-yith-easy-login-register-popup-for-woo',
			),
			'colissimo_shipping_woo'                       => array(
				'plugin_name'   => 'Colissimo shipping methods for WooCommerce',
				'template_name' => array(
					'lpc_inward_label_generation',
					'lpc_outward_label_generation',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-addon-for-colissimo-shipping-methods',
			),
			'Parcel_Panel_Order_Tracking'                  => array(
				'plugin_name'   => 'Parcel Panel Order Tracking for WooCommerce',
				'template_name' => array(
					'customer_pp_delivered_shipment',
					'customer_pp_exception_shipment',
					'customer_pp_failed_attempt_shipment',
					'customer_pp_in_transit_shipment',
					'customer_pp_out_for_delivery_shipment',
					'customer_pp_partial_shipped_order',
				),
				'link_upgrade'  => 'https://yaycommerce.com/yaymail-addons/yaymail-premium-addon-for-parcel-panel-order-tracking/',
			),
		);
		return $arrAddonSupported;
	}
	public static function listPluginForProSupported() {
		$list_plugin_for_pro = array();
		if ( class_exists( 'WC_Shipment_Tracking_Actions' ) || class_exists( 'WC_Advanced_Shipment_Tracking_Actions' ) || class_exists( 'AST_PRO_Install' ) ) {
			$list_plugin_for_pro[] = 'WC_Shipment_Tracking';
		}
		if ( class_exists( 'AST_PRO_Install' ) ) {
			$list_plugin_for_pro[] = 'WC_Shipment_Tracking_AST_PRO';
		}
		if ( class_exists( 'PH_Shipment_Tracking_API_Manager' ) ) {
			$list_plugin_for_pro[] = 'PH_Shipment_Tracking_API_Manager';
		}

		if ( class_exists( 'TrackingMore' ) ) {
			$list_plugin_for_pro[] = 'TrackingMore';
		}

		if ( class_exists( 'WC_Connect_Loader' ) ) {
			$list_plugin_for_pro[] = 'WooCommerce_Shipping_Tax';
		}

		if ( is_plugin_active( 'wc-chitchats-shipping-pro/wc-chitchats-shipping-pro.php' ) ) {
			$list_plugin_for_pro[] = 'Chitchats_Shipping_Shipments';
		}
		if ( class_exists( 'FooEvents' ) ) {
			$list_plugin_for_pro[] = 'FooEvents';
		}
		if ( class_exists( 'WC_Software' ) ) {
			$list_plugin_for_pro[] = 'WC_Software';
		}
		if ( class_exists( 'WC_Admin_Custom_Order_Fields' ) ) {
			$list_plugin_for_pro[] = 'WC_Admin_Custom_Order_Fields';
		}
		if ( class_exists( 'EventON' ) ) {
			$list_plugin_for_pro[] = 'EventON';
		}
		if ( function_exists( 'woocontracts_maile_ekle' ) ) {
			$list_plugin_for_pro[] = 'WC_Email_Sozlesmeler';
		}
		if ( class_exists( 'Woocommerce_German_Market' ) ) {
			$list_plugin_for_pro[] = 'Woocommerce_German_Market';
		}
		if ( class_exists( 'YITH_WooCommerce_Order_Tracking_Premium' ) ) {
			$list_plugin_for_pro[] = 'YITH_WooCommerce_Order_Tracking_Premium';
		}
		if ( class_exists( 'Woocommerce_Local_Pickup' ) ) {
			$list_plugin_for_pro[] = 'WooCommerceAdvancedLocalPickup';
		}
		return $list_plugin_for_pro;
	}
}
