<?php
//api模块is8n 中文语言包 字母排序
return [
    //设备
    'equipment_error_notification'=>'Equipment Temperature Alarm',
    'equipment_temp_max'=>'[UTC]{:time} the temperature {:temp} , upper temperature limit {:max_temp}',
    'equipment_temp_min'=>'[UTC]{:time} the temperature {:temp} , lower temperature limit  {:min_temp}',

    "err_email"=>"please input correct email",
    "err_status_0"=>'已被禁用',
    "err_status_-1"=>'已被删除',
    "err_login_"=>"您的帐号于{:time}在未知设备上登录了，如果这不是你的操作，你的密码已经泄漏。",
    "err_login_android"=>"您的帐号于{:time}在一台android手机上登录了，如果这不是你的操作，你的密码已经泄漏。",
    "err_login_ios"=>"您的帐号于{:time}在一台iphone手机设备上登录了，如果这不是你的操作，你的密码已经泄漏。",
    "err_login_pc"=>"您的帐号于{:time}在一台电脑上登录了，如果这不是你的操作，你的密码已经泄漏。",
    "err_login_weixin"=>"您的帐号于{:time}通过微信登录了，如果这不是你的操作，你的密码已经泄漏。",
    "err_login_web"=>"您的帐号于{:time}在一台手机浏览器上登录了，如果这不是你的操作，你的密码已经泄漏。",

    //腾讯云返回错误
    "tip_sms_success"=>"短信已发送成功，请注意查看",
    "err_qcloud_unknown"=>"短信发送未知错误",
    "err_qcloud_1001"=>"AppKey错误",
    "err_qcloud_1002"=>"短信/语音内容中含有脏字",
    "err_qcloud_1003"=>"未填AppKey",
    "err_qcloud_1004"=>"REST API请求参数有误",
    "err_qcloud_1006"=>"没有权限",
    "err_qcloud_1007"=>"其他错误",
    "err_qcloud_1008"=>"下发短信超时",
    "err_qcloud_1009"=>"用户IP不在白名单中",
    "err_qcloud_1011"=>"REST API命令字错误 ",
    "err_qcloud_1012"=>"短信内容格式错误",
    "err_qcloud_1013"=>"请30秒后再试",
    "err_qcloud_1014"=>"模版未审批",
    "err_qcloud_1015"=>"黑名单手机",
    "err_qcloud_1016"=>"错误的手机号格式",
    "err_qcloud_1017"=>"短信内容过长",
    "err_qcloud_1018"=>"语音验证码格式错误",
    "err_qcloud_1019"=>"sdkappid不存在",

    "err_system"=>"System Error",
    'success'=>'Successful Operation',
    'fail'=>'Operation Failed',
    "err_lang_not_support"=>"Language Does Not Support",
    "err_not_find"=>"Data Does Not Exist",
    "exceed_limit"=>"More than limit，up to {:limit}.",
    "err_return_is_not_null"=>"in _ return _ is _ not _.",
    "err_data_query"=>"Data Query Error",
    "page_index_need"=>"The paging number parameter is missing",
    "page_size_need"=>"Paging page size parameter missing",
    "tip_filter_by"=>"Press",
    //index模块
    //快递web controller
    "err_auth_fail"=>"You have no right to access this page. Please close the page and try again.",
    "auth_psw_need"=>"Verify password missing",
    "err_no_order"=>"No such order",
    "err_order_not_ship"=>"Orders not shipped",
    "err_no_express_info"=>"No such express information",
    //订单domain
    //订单action
    "err_address_id"=>"Wrong receiving address ID",
    "err_sku_ids"=>"Wrong product specifications ID",
    "err_cart_id"=>"Wrong shopping item ID",
    "err_quantity_lack"=>"Commodity {:name} inventory shortage",
    //订单logic
    "err_order_id"=>"Order ID error",
    "err_order_code"=>"Order number error",
    "err_cant_cancel_payed_order"=>"Can not cancel the order has been paid",
    "err_order_status"=>"Wrong order status",
    "err_order_not_updated"=>"Order not updated",
    "err_order_status_add"=>"Order status record insertion failed",
    "err_cart_item_count_invalid"=>"Commodity [{:name}] did not reach the minimum number ({:min})",
    "err_add_order_info_fail"=>"Failed to save order information",
    "err_get_pay_info"=>"Failed to pay for information",
    "err_repay_param"=>"Failed to restart payment",
    "err_pay_status"=>"Wrong payment status",
    //addressDomain
    "err_address_not_exits"=>'Address does not exist',
    "address_id_need"=>"Receiving address ID deletion",
    "err_country_id_need"=>"Lack of national ID",
    "err_country_id_error"=>"Illegal state ID",
    //FileController
    "err_file_not_accept_type"=>'Unsupported upload file type',
    "err_file_user_id_not_exist"=>'User ID does not exist',
    //categoryomain
    "err_category_cate_id_need"=>'Category ID deletion',
    //goodsfeedbackdomain
    "err_goods_feedback_img_ids_need"=>"Picture ID missing",
    //product_domain
    "err_product_has_expired"=>"The goods have been off the shelf",
    "err_product_shelf_off"=>"The goods have been off the shelf",
    //product_logic
    "err_product_status_expired"=>"The product information is not valid",
    "err_product_status_shelf_off"=>"The goods have been off the shelf",
    "err_product_status_outsold"=>"The goods have been sold out",
    "err_product_status_delete"=>"The merchandise has been deleted",
    //购物车domain
    "err_cart_count_need"=>"Missing quantity parameter",
    "err_cart_invalid_sku_pkid"=>"Wrong product specifications ID",
    "err_cart_min_buy_limit"=>"Minimum purchase amount {:limit}",
    "err_cart_max_buy_limit"=>"Purchase quantity {:limit}",
    "err_cart_quantity"=>"Inventory shortage, the remaining inventory {:quantity}",
    //
    "tip_cart_status_1"=>"Normal",
    "tip_cart_status_2"=>"The goods have been off the shelf",
    "tip_cart_status_3"=>"The product information is not valid",
    "tip_cart_status_4"=>"Inventory of the goods is tight",
    //favoritesDomain
    "err_type"=>"Wrong collection type parameters",

    // src / security code
    "country_tel_number_need"=>"The national mobile phone number code missing",
    "err_invalid_code"=>  "Verification code is not valid!",
    "err_code_used"=>  "Verification code has been used!",
    "err_code_expired"=>  "Verification code has expired!",

    "invalid_id"=>"Wrong or illegal ID",
    "type_need"=>"Type type parameter missing",
    "count_need"=>"Count quantity parameter missing",
    "uid_need"=>'User ID deletion',
    "id_need"=>'ID parameter deletion',
    'username_need'=>'User name missing',
    'password_need'=>'Codon deletion',
    'mobile_need'=>'Cell phone number missing',
    'email_need'=>'Mailbox deletion',
    'reg_from_need'=>'Lack of registration information',
    'code_need'=>'Verification code deletion',
    'code_type_need'=>'Verification code usage type deletion',
    'delay_do_msg_send'=> "Please try to send text messages after {:param} seconds!",


    'tip_mobile_registered'=>'The phone number has been registered',
    'tip_mobile_unregistered'=>'The phone number is not yet registered',
    'tip_update_api_version'=>'Please update the interface，the latest version:{:version}',
    'lack_parameter'=>'Missing {:param} parameter',
    'invalid_parameter'=>'{:param} parameter is invalid or illegal',
    'invalid_request'=>'The request is not valid. Please try again.',
    'err_sign'=>'Signature error',
    'err_404'=>'Requested resource does not exist',
    "err_not_support_file_ext"=> "Upload type not supported!",
    'err_delete'=>'Delete failed',
    "err_not_users_data"=>'Does not belong to the user\'s data， can not be operated',

    //src/domain模块
    'err_province_need'=>"Provinces can not be empty",
    'err_city_need'=>"The city can not be empty",
    'err_area_need'=>"The detailed address cannot be empty",
    'err_province_id_need'=>"Province code can not be empty",
    'err_city_id_need'=>"City code can not be empty",
    'err_area_id_need'=>"Region encoding cannot be empty",
    'err_contact_name_need'=>'Contact cannot be empty',
    'err_contact_mobile_need'=>"Contact phone number can not be empty",
    'err_contact_email_need'=>"Zip code can not be empty",

    "err_auto_login_code_need"=>"Automatic login authorization code is invalid",
    'err_incorrect_password'=>'Incorrect password',
    'err_invalid_idcard'=>'Invalid ID number',

    //src/user模块

    //hook模块
    "tip_hook_pay_success"=>'Payment success notification message',
    "tip_hook_pay_success_summary"=>'order {:content} payment success notification message',
    "tip_hook_pay_success_content"=>'Your order {:content} has been paid successfully',

    'tip_username_length'=> 'The length of the user name must be greater than 6 and less than 64',
    'tip_username'=>"The account name must be a combination of letters, numbers, or underscores, and the first must be the letter!",
    'tip_password_length'=> 'The length of the password must be greater than 6 and less than 64',
    'tip_password'=>'Password can only contain numbers，English letters and the following characters \，.?><;:!@#$%^&*()_+-=!',
    'tip_success'=>'Delete success',
    'err_account_unregistered'=>'Account has not been registered',
    'err_login_fail'=>'Login failed',
    "suc_login"=>"Login success",
    'suc_modified'=>'Successful modification',
    'err_modified'=>'Modify failed',
    "err_auth_code"=>"Authorization code is invalid",
    "err_re_login"=>"Please log in again",

    //src/base模块
    'tip_mobile_exist'=>'Cell phone number has been registered',
    'tip_username_exist'=>'User name has been registered',

    //src/service/IntentionalOrderCreateAction
    'err_id'=>"Illegal ID parameters",

    //src/repairerApply
	'err_repairer_has_apply'=>'Already have applied',
	'repairer_apply_success'=> 'apply succeed'

];