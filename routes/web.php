<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/test','PhotoController@create');
Route::get('/test','PhotoController@index');

Auth::routes();

//Auth Fix
Auth::routes();

Route::get('/checkauth','CheckAuthController@getCheck');
Route::get('/logout',function(){
	Auth::logout();
	return redirect('/login');
});
//RouteFix
Route::get('/home',function(){
	return redirect('/');
});

//Brand Glb
Route::get('/','BrandController@getDashboard');

//Admin Global
Route::get('/admin','AdminController@getDashboard');

//[Admin] POS
Route::get('/admin/pos','AdminController@getPOS');
Route::post('/admin/main','AdminController@getMainPOS');
Route::post('/admin/pos/makeorder','AdminController@POSmakeOrder');
Route::get('/admin/pos/slip/{id}','AdminController@getPrintSlip');
Route::post('/admin/pos/searchorder','AdminController@searchItem');
Route::post('/admin/pos/getbarcode','AdminController@getBarcode');

//[Admin] Order
Route::get('/admin/order','AdminController@getChooseOrder');
Route::post('/admin/order','AdminController@getAllOrder');
Route::get('/admin/order/get/{id}','AdminController@getOrder');
Route::post('/admin/order/void','AdminController@voidOrder');
Route::get('/admin/brandsales','AdminController@getBrandSales');
Route::post('/admin/brandsales','AdminController@getBrandSales');

//[Admin] Stock Report
Route::get('/admin/stock/report','AdminController@getStockReport');
Route::post('/admin/stock/report','AdminController@getStockBrand');
Route::get('/admin/stock/adjust','AdminController@getStockAdjust');
Route::post('/admin/stock/adjust','AdminController@adjustStock');
Route::post('/admin/stock/productcheck','AdminController@getProductStock');
Route::get('/admin/purchase/recieve/{id}','AdminController@recieveProduct');


//[Admin] Stock Transfer Report
Route::get('/admin/stock/transfer','AdminController@getStockTransfer');
Route::get('/admin/stock/create','AdminController@getStockCreate');
Route::get('/admin/stock/transfer/get/{id}','AdminController@getEditTransfer');
Route::post('/admin/stock/transfer','AdminController@transferStock');
Route::get('/admin/stock/transfer/submit/{id}','AdminController@submitTransfer');
Route::get('/admin/stock/transfer/cancel/{id}','AdminController@cancelTransfer');
Route::post('/admin/stock/transfer/update','AdminController@updateTransfer');
Route::get('//admin/stock/transfer/print/{id}','AdminController@printTransfer');

//Brand Stock Report
Route::get('/stock','BrandController@getStock');
//[Admin]Brand Manage
Route::get('/admin/brand','AdminController@getBrand');
Route::get('/admin/brand/add','AdminController@getAddBrand');
Route::post('/admin/brand/add','AdminController@addBrand');
Route::get('/admin/brand/get/{id}','AdminController@getEditBrand');
Route::post('/admin/brand/update','AdminController@updateBrand');
Route::get('/admin/brand/suspend/{id}','AdminController@suspendBrand');
Route::get('/admin/brand/unsuspend/{id}','AdminController@unsuspendBrand');

//[Admin]Admin Manage
Route::get('/admin/admin','AdminController@getAdmin');
Route::get('/admin/admin/add','AdminController@getAddAdmin');
Route::post('/admin/admin/add','AdminController@addAdmin');
Route::get('/admin/admin/get/{id}','AdminController@getEditAdmin');
Route::post('/admin/admin/update','AdminController@updateAdmin');
Route::get('/admin/admin/suspend/{id}','AdminController@suspendAdmin');
Route::get('/admin/admin/unsuspend/{id}','AdminController@unsuspendAdmin');

//[Admin]Member Manage
Route::get('/admin/member','AdminController@getMember');
Route::get('/admin/member/add','AdminController@getAddMember');
Route::post('/admin/member/add','AdminController@addMember');
Route::get('/admin/member/get/{id}','AdminController@getEditMember');
Route::post('/admin/member/update','AdminController@updateMember');
Route::get('/admin/member/suspend/{id}','AdminController@suspendMember');
Route::get('/admin/member/unsuspend/{id}','AdminController@unsuspendMember');
Route::get('/admin/member/order/{id}','AdminController@getMemberOrder');

//[Admin]Branch Manage
Route::get('/admin/branch','AdminController@getAllBranch');
Route::get('/admin/branch/get/{id}','AdminController@getBranch');
Route::post('/admin/branch/assign','AdminController@assigntoBranch');
Route::post('/admin/branch/remove','AdminController@removefromBranch');

//[Brand]Product Manage
Route::get('/products/add','BrandController@getAddProduct');
Route::post('/products/add','BrandController@addProduct');
Route::get('/products/suspend/{id}','BrandController@suspendProduct');
Route::get('/products/unsuspend/{id}','BrandController@unsuspendProduct');
Route::get('/products/barcodeprint','BrandController@getPrintBarcode');
Route::post('/products/barcodeprint','BrandController@printBarcodeCustom');
Route::get('/product/move/{id}','BrandController@getProductMove');
Route::get('/products/promotionprint','AdminController@getPrintPromotion');
Route::post('/products/promotionprint','AdminController@printPromotion');

//[Brand]Purchase Manage
Route::get('/purchase/add','BrandController@getAddPurchase');
Route::post('/purchase/add','BrandController@addPurchase');
Route::post('/purchase/update','BrandController@updatePurchase');
Route::get('/purchase/cancel/{id}','BrandController@cancelPurchase');


//[Admin+Brand]Global Route
Route::get('/purchase','BrandController@getPurchase');
Route::get('/purchase/get/{id}','BrandController@showPurchase');
Route::get('/products','BrandController@getProduct');
Route::post('/products','BrandController@getProduct');
Route::get('/products/get/{id}','BrandController@getEditProduct');
Route::post('/products/update','BrandController@updateProduct');
Route::get('/purchase/barcode/{id}','BrandController@printBarcode');
Route::get('/purchase/print/{id}','BrandController@printPO');
Route::get('/purchase/printpo/{id}','BrandController@printPONew');

Route::get('/report','BrandController@getChooseReport');
Route::post('/report','BrandController@getReport');

Route::get('/admin/report','AdminController@getChooseReport');
Route::post('/admin/report','AdminController@getReport');

//[Admin+Brand]Help
Route::get('/help/{id}','GlobalController@getHelp');
Route::get('/admin/help','AdminController@getAddHelp');
Route::post('/admin/help','AdminController@addHelp');
Route::post('/admin/uphelp','AdminController@updateHelp');
Route::get('/admin/help/{id}','AdminController@getUpdateHelp');


Route::post('/admin/stock/store','AdminController@apiGetproduct');
Route::post('/admin/stock/poscheck','AdminController@posCheckStock');

Route::post('/stock/store','BrandController@apiGetproduct');

// //Promotion
// Route::get('/admin/promotions','PromotionController@getManagePromotion');
// Route::post('/admin/promotions','PromotionController@managePromotion');


// //Promotion Discountprice
// Route::get('/admin/promotions/discountprice','PromotionController@getDiscountprice');
// Route::post('/admin/promotions/discountprice','PromotionController@addDiscountprice');
// Route::post('/admin/promotions/discountprice/apigetproduct','PromotionController@apiGetproductDiscountprice');
// Route::get('/admin/promotions/discountprice/{id}','PromotionController@deleteDiscountprice');
// Route::get('/admin/promotions/discountprice/print/{id}','PromotionController@printDiscountprice');

//Promotion Discountprice New
Route::get('/admin/promotions','PromotionController@getAllPromotion');
Route::post('/admin/promotions/specific','PromotionController@getPromotionSpecific');
Route::get('/admin/promotions/print/{id}','PromotionController@printDiscountprice');
Route::get('/admin/promotions/print/group/{id}','PromotionController@printDiscountpriceGroup');
Route::post('/admin/promotions/dateprint','PromotionController@printDiscountpriceGroupDate');
Route::get('/admin/promotions/create','PromotionController@getAddPromotion');
Route::post('/admin/promotions/create','PromotionController@addPromotion');
Route::post('/admin/promotions/update','PromotionController@updatePromotion');
Route::post('/admin/promotions/addproduct','PromotionController@addProducttoPromotion');
Route::get('/admin/promotions/get/{id}','PromotionController@getPromotion');
Route::get('/admin/promotions/delete/{promotion_id}/{product_id}','PromotionController@removeProductfromPromotion');
Route::get('/admin/promotions/deletepromotion/{id}','PromotionController@removePromotion');
Route::post('/admin/promotions/updateprice','PromotionController@updatePrice');
Route::post('/admin/promotions/checkpromotion','PromotionController@posCheckPromotion');

//POSStart
Route::get('/admin/posstart','AdminController@indexPOSStart');
Route::get('/admin/posstart/get/{id}','AdminController@getPosstart');
Route::post('/admin/posstart/add','AdminController@addPosstart');
Route::post('/admin/posstart/update','AdminController@editPosstart');

//Promotion Notification
Route::get('/admin/promonotification','AdminController@getAllPromoNotification');
Route::post('/admin/promotionnotification/specific','AdminController@getPromoNotificationSpecific');
Route::get('/admin/promotionnotification/print/{id}','AdminController@printPromoNotification');
Route::post('/admin/promotionnotification/groupprint','AdminController@printPromoNotificationGroupPrint');
Route::get('/admin/promonotification/create','AdminController@getAddPromoNotification');
Route::post('/admin/promonotification/create','AdminController@addPromoNotification');
Route::post('/admin/promonotification/update','AdminController@updatePromoNotification');
Route::post('/admin/promonotification/addproduct','AdminController@addProducttoPromotion');
Route::get('/admin/promonotification/get/{id}','AdminController@getPromoNotification');
Route::get('/admin/promonotification/delete/{promotion_id}/{product_id}','AdminController@removeProductfromPromotion');
Route::get('/admin/promonotification/deletepromotion/{id}','AdminController@removePromotionNotification');
Route::post('/admin/promonotification/checkpromotion','AdminController@checkNotiPromotion');
Route::get('/admin/promonotification/getpospromotion/{branch_id}','AdminController@getPosPromotion');
Route::post('/admin/promonotification/discountprice/apigetproduct','PromotionController@apiGetproductNotification');



Route::get('/parsebranch','AdminController@parseBranch');

Route::get('/tmpbranch','AdminController@getTmpBranch');