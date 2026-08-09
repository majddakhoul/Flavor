<?php
use App\Http\Controllers\Api\OrdersControllers\CompleteOrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return response()->json([
        'data' => null,
        'success' => false,
        'message' => 'User not authenticated. Check that your are logged in the app or not',
        'status'=>403
    ],403);
})->name('login');

//Locations=======================================================================================================================

use App\Http\Controllers\Api\LocationsControllers\PostOneLocationController;
use App\Http\Controllers\Api\LocationsControllers\GetAllLocationsController;
use App\Http\Controllers\Api\LocationsControllers\DeleteOneLocationController;
use App\Http\Controllers\Api\LocationsControllers\UpdateOneLocationController;
use App\Http\Controllers\Api\LocationsControllers\GetOneLocationInfoController;

Route::middleware(['auth:sanctum', 'is.manager', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::post('/post_one_Location', [PostOneLocationController::class, 'post_one_Location']);
    Route::put('/update_one_location', [UpdateOneLocationController::class, 'update_one_location']);
    Route::delete('/delete_one_location/{Location_id}', [DeleteOneLocationController::class, 'delete_one_location']);
});

Route::middleware(['auth:sanctum', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::get('/get_one_location_info/{Location_id}', [GetOneLocationInfoController::class, 'get_one_location_info']);
    Route::get('/get_all_locations', [GetAllLocationsController::class, 'get_all_locations']);
});

//=====================================================================================================================================================================================

//Users===========================================================================================================================================

use App\Http\Controllers\Api\UsersControllers\AuthControllers\ResetPasswordController;
use App\Http\Controllers\Api\UsersControllers\AuthControllers\LoginAllUsersController;
use App\Http\Controllers\Api\UsersControllers\AuthControllers\ForgetPasswordController;
use App\Http\Controllers\Api\UsersControllers\CrudControllers\DeleteAccountForUserController;
use App\Http\Controllers\Api\UsersControllers\CrudControllers\UpdateAccountForUserController;
use App\Http\Controllers\Api\UsersControllers\AuthControllers\PostCodeToVerifyEmailController;
use App\Http\Controllers\Api\UsersControllers\CrudControllers\ShowProfileForOneUserController;
use App\Http\Controllers\Api\UsersControllers\AuthControllers\ResendCodeToVerifyEmailController;
use App\Http\Controllers\Api\UsersControllers\AuthControllers\RegisterCustomerOrManagerController;
use App\Http\Controllers\Api\UsersControllers\AuthControllers\LogoutAllUsersController;

Route::post('/password_rest', [ResetPasswordController::class, 'password_rest']);
Route::post('/forget_password', [ForgetPasswordController::class, 'forget_password']);

Route::middleware(['api-session'])->group(function () {
    Route::post('/register_customer_or_manager', [RegisterCustomerOrManagerController::class, 'register_customer_or_manager']);
});

Route::middleware(['api-session'])->group(function () {
    Route::post('/login_all_users', [LoginAllUsersController::class, 'login_all_users']);
});

Route::middleware(['api-session','emailVerified','auth:sanctum'])->group(function () {
    Route::post('/logout_all_users', [LogoutAllUsersController::class, 'logout_all_users']);
});

Route::middleware(['auth:sanctum', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::delete('/delete_account_for_user', [DeleteAccountForUserController::class, 'delete_account_for_user']);
    Route::put('/update_account_for_user', [UpdateAccountForUserController::class, 'update_account_for_user']);
    Route::get('/show_profile_for_one_user', [ShowProfileForOneUserController::class, 'show_profile_for_one_user']);
});

Route::middleware(['auth:sanctum', 'is.loggedin', 'is.customerormanger'])->group(function () {
    Route::post('/post_code_to_verify_email', [PostCodeToVerifyEmailController::class, 'post_code_to_verify_email']);
    Route::post('/resend_email_verification', [ResendCodeToVerifyEmailController::class, 'resend_email_verification']);
});

//Employees============================================================================================================================

use App\Http\Controllers\Api\EmployeesControllers\PostOneEmployeeController;
use App\Http\Controllers\Api\EmployeesControllers\SearchForEmployeesController;
use App\Http\Controllers\Api\EmployeesControllers\ShowOneEmployeeInfoController;
use App\Http\Controllers\Api\EmployeesControllers\ShowAllEmployeesInfoController;
use App\Http\Controllers\Api\EmployeesControllers\GetSumOfBonusEmployeesController;
use App\Http\Controllers\Api\EmployeesControllers\GetUserBelongsToEmployeeController;
use App\Http\Controllers\Api\EmployeesControllers\DeleteEmployeeFromManagerController;
use App\Http\Controllers\Api\EmployeesControllers\GetSumOfAllSalaryEmployeeController;
use App\Http\Controllers\Api\EmployeesControllers\UpdateEmployeeInfoFromManagerController;

Route::middleware(['auth:sanctum', 'is.manager', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::get('/get_sum_of_bonus_employees', [GetSumOfBonusEmployeesController::class, 'get_sum_of_bonus_employees']);
    Route::post('/search_for_employees', [SearchForEmployeesController::class, 'search_for_employees']);
    Route::get('/show_one_employee_info/{Employee_id}', [ShowOneEmployeeInfoController::class, 'show_one_employee_info']);
    Route::post('/post_one_employee', [PostOneEmployeeController::class, 'post_one_employee']);
    Route::get('/show_all_employees_info', [ShowAllEmployeesInfoController::class, 'show_all_employees_info']);
    Route::get('/get_user_belongs_to_employee/{Employee_id}', [GetUserBelongsToEmployeeController::class, 'get_user_belongs_to_employee']);
    Route::put('/update_employee_info_from_manager', [UpdateEmployeeInfoFromManagerController::class, 'update_employee_info_from_manager']);
    Route::post('/search_for_employees', [SearchForEmployeesController::class, 'search_for_employees']);
    Route::delete('/delete_employee_from_manager/{Employee_id}', [DeleteEmployeeFromManagerController::class, 'delete_employee_from_manager']);
    Route::get('/get_sum_of_all_salary_employee', [GetSumOfAllSalaryEmployeeController::class, 'get_sum_of_all_salary_employee']);
});

//=================================================================================================================================================================

//Customers=======================================================================================================================================================

use App\Http\Controllers\Api\CustomersControllers\SearchForCustomerController;
use App\Http\Controllers\Api\CustomersControllers\ShowOneCustomerInfoController;
use App\Http\Controllers\Api\MaintenancesControllers\DeleteMaintenanceController;
use App\Http\Controllers\Api\CustomersControllers\UpdateInfoForCustomerController;
use App\Http\Controllers\Api\CustomersControllers\GetUserBelongsToCustomerController;
use App\Http\Controllers\Api\CustomersControllers\GetAllCustomersForManagerController;

Route::middleware(['auth:sanctum', 'is.manager', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::get('/get_all_customers_for_manager', [GetAllCustomersForManagerController::class, 'get_all_customers_for_manager']);
    Route::get('/show_one_customer_info/{Customer_id}', [ShowOneCustomerInfoController::class, 'show_one_customer_info']);
    Route::get('/get_user_belongs_to_customer/{Customer_id}', [GetUserBelongsToCustomerController::class, 'get_user_belongs_to_customer']);
    Route::post('/search_for_customer', [SearchForCustomerController::class, 'search_for_customer']);
});

Route::middleware(['auth:sanctum', 'is.customer', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::put('/update_info_for_customer', [UpdateInfoForCustomerController::class, 'update_info_for_customer']);
});

//=============================================================================================================================================================

//Maintenances===================================================================================================================================================

use App\Http\Controllers\Api\MaintenancesControllers\PostMaintenancesController;
use App\Http\Controllers\Api\MaintenancesControllers\ShowAllMaintenancesController;
use App\Http\Controllers\Api\MaintenancesControllers\UpdateOneMaintenancesController;
use App\Http\Controllers\Api\MaintenancesControllers\ShowOneMaintenancesInfoController;
use App\Http\Controllers\Api\MaintenancesControllers\GetAllSumOfDiscountAndPriceAndTotalPriceOfAllMaintenancesController;

Route::middleware(['auth:sanctum', 'is.employee', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::get('/get_all_sum_of_discount_and_price_and_total_price_of_all_maintenances', [GetAllSumOfDiscountAndPriceAndTotalPriceOfAllMaintenancesController::class, 'get_all_sum_of_discount_and_price_and_total_price_of_all_maintenances']);
    Route::put('/update_one_maintenances', [UpdateOneMaintenancesController::class, 'update_one_maintenances']);
    Route::delete('/delete_maintenance/{maintenanceId}', [DeleteMaintenanceController::class, 'delete_maintenance']);
    Route::post('/post_maintenances', [PostMaintenancesController::class, 'post_maintenances']);
    Route::get('/show_all_maintenances', [ShowAllMaintenancesController::class, 'show_all_maintenances']);
    Route::get('/show_one_maintenances_info/{MaintenancesId}', [ShowOneMaintenancesInfoController::class, 'show_one_maintenances_info']);
});

//=========================================================================================================================================================

//OfferRatingAndMealRating=====================================================================================================================================

use App\Http\Controllers\Api\MealsRatingsControllers\PostRatingToMealController;
use App\Http\Controllers\Api\OffersRatingsConrtollers\PostRatingToOfferController;
use App\Http\Controllers\Api\MealsRatingsControllers\UpdateRatingToMealController;
use App\Http\Controllers\Api\MealsRatingsControllers\ShowRatingForOneMealController;
use App\Http\Controllers\Api\OffersRatingsConrtollers\UpdateRatingToOfferController;
use App\Http\Controllers\Api\OffersRatingsConrtollers\ShowRatingForOneOfferController;

Route::middleware(['auth:sanctum', 'is.customer', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::post('/post_rating_to_offer', [PostRatingToOfferController::class, 'post_rating_to_offer']);
    Route::put('/update_rating_to_offer', [UpdateRatingToOfferController::class, 'update_rating_to_offer']);
    Route::post('/post_rating_to_meal', [PostRatingToMealController::class, 'post_rating_to_meal']);
    Route::put('/update_rating_to_meal', [UpdateRatingToMealController::class, 'update_rating_to_meal']);
});

Route::middleware(['auth:sanctum', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::get('/show_rating_for_one_meal/{MealId}', [ShowRatingForOneMealController::class, 'show_rating_for_one_meal']);
    Route::get('/show_rating_for_one_offer/{OfferId}', [ShowRatingForOneOfferController::class, 'show_rating_for_one_offer']);
});

//=====================================================================================================================================

//Category=============================================================================================================================

use App\Http\Controllers\Api\CategoriesControllers\PostCategoryController;
use App\Http\Controllers\Api\CategoriesControllers\ShowCategoryController;
use App\Http\Controllers\Api\CategoriesControllers\UpdateCategoryController;
use App\Http\Controllers\Api\CategoriesControllers\DeleteCategoryController;
use App\Http\Controllers\Api\CategoriesControllers\ShowِAllCategoriesController;
use App\Http\Controllers\Api\CategoriesControllers\ShowMenuController;

Route::middleware(['auth:sanctum', 'is.cheformanager', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::put('/update_category', [UpdateCategoryController::class, 'update_category']);
    Route::delete('/delete_category/{CategoryId}', [DeleteCategoryController::class, 'delete_category']);
    Route::post('/post_one_category', [PostCategoryController::class, 'post_category']);
});

Route::middleware(['auth:sanctum', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::get('/show_category/{CategoryId}', [ShowCategoryController::class, 'show_category']);
    Route::get('/showِ_all_categories', [ShowِAllCategoriesController::class, 'showِ_all_categories']);
});

Route::middleware(['auth:sanctum', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::get('/menu', [ShowMenuController::class, 'show_menu']);
});

//===================================================================================================================================

//Piture=============================================================================================================================

use App\Http\Controllers\Api\PicturesConrtollers\PostPictureForMeal;
use App\Http\Controllers\Api\PicturesConrtollers\GetUrlOfPictureMeal;
use App\Http\Controllers\Api\PicturesConrtollers\ReplacePictureForMealController;
use App\Http\Controllers\Api\PicturesConrtollers\DeletePictureForMealController;

Route::middleware(['auth:sanctum', 'is.cheformanager', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::post('/replace_picture_for_meal', [ReplacePictureForMealController::class, 'update']);
    Route::delete('/delete_picture_for_meal/{MealId}', [DeletePictureForMealController::class, 'destroy']);
    Route::post('/post_picture_for_meal', [PostPictureForMeal::class, 'store']);
});

Route::middleware(['auth:sanctum', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::get('/get_url_of_picture_meal/{MealId}', [GetUrlOfPictureMeal::class, 'show']);
});
//===================================================================================================================================

//Offer==========================================================================================================================================

use App\Http\Controllers\Api\OffersControllers\PostOfferController;
use App\Http\Controllers\Api\OffersControllers\ActivateOfferController;
use App\Http\Controllers\Api\OffersControllers\GetAllOffersController;
use App\Http\Controllers\Api\OffersControllers\GetOneOfferController;
use App\Http\Controllers\Api\OffersControllers\PutOfferController;
use App\Http\Controllers\Api\OffersControllers\DeleteOfferController;
use App\Http\Controllers\Api\OffersControllers\SearchOfferController;
use App\Http\Controllers\Api\OffersControllers\AttachMealsToOfferController;

Route::middleware(['auth:sanctum', 'is.cheformanager', 'is.loggedin', 'emailVerified'])->group(function () {

    Route::put('/offers/{offer}', [PutOfferController::class, 'edit_offer']);
    Route::post('/offers', [PostOfferController::class, 'add_offer']);
    Route::patch('/offers/{offer}/deactivate', [ActivateOfferController::class, 'offer_activation']);
    Route::delete('/offers/{offer}', [DeleteOfferController::class, 'delete_offer']);
    Route::post('/offers/{offer_id}/attach', [AttachMealsToOfferController::class, 'attach_meals']);
    Route::delete('/offers/{offer_id}/meals/{meal}', [AttachMealsToOfferController::class, 'detach_meal']);
});

Route::middleware(['auth:sanctum', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::get('/offers', [GetAllOffersController::class, 'get_all']);
    Route::get('/offers/{offer}', [GetOneOfferController::class, 'get_one']);
    Route::post('/offers/search', [SearchOfferController::class, 'search']);
});

//========================================================================================================================================================================================================================================================

//Meal============================================================================================================================================

use App\Http\Controllers\Api\MealsControllers\AttachIngredientsToMealController;
use App\Http\Controllers\api\MealsControllers\deleteMealController;
use App\Http\Controllers\Api\MealsControllers\GetAllMealsController;
use App\Http\Controllers\api\MealsControllers\getOneMealController;
use App\Http\Controllers\Api\MealsControllers\postMealController;
use App\Http\Controllers\api\MealsControllers\putMealController;
use App\Http\Controllers\Api\MealsControllers\SearchMealController;

Route::middleware(['auth:sanctum', 'is.cheformanager', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::post('/meals', [PostMealController::class, 'add_meal']);
    Route::post('/meals/{meal}/attach', [AttachIngredientsToMealController::class, 'attach_ingredients']);
    Route::delete('/meals/{meal_id}/ingredients/{ingredientId}', [AttachIngredientsToMealController::class, 'detach_ingredient']);
    Route::delete('/meals/{meal_id}', [deleteMealController::class, 'delete_meal']);
    Route::put('/meals/{meal_id}', [putMealController::class, 'edit_meal']);
});

Route::middleware(['auth:sanctum', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::get('/meals', [GetAllMealsController::class, 'get_all_meals']);
    Route::get('/meals/{meal_id}', [getOneMealController::class, 'get_one_meal']);
    Route::post('/meals/search', [SearchMealController::class, 'search']);
});

//========================================================================================================================================================================================================================================================================================================================

//Ingredient========================================================================================================================================================================================================================================================================================================================

use App\Http\Controllers\api\IngredientsControllers\deleteIngredientController;
use App\Http\Controllers\api\IngredientsControllers\getAllIngredientsController;
use App\Http\Controllers\api\IngredientsControllers\getOneIngredientController;
use App\Http\Controllers\api\IngredientsControllers\postIngredientController;
use App\Http\Controllers\api\IngredientsControllers\putIngredientController;
use App\Http\Controllers\Api\IngredientsControllers\SearchIngredientController;

Route::middleware(['auth:sanctum', 'is.cheformanager', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::get('/ingredients', [getAllIngredientsController::class, 'get_all_ingredients']);
    Route::get('/ingredients/{ingredient_id}', [getOneIngredientController::class, 'get_one_ingredient']);
    Route::post('/ingredients', [postIngredientController::class, 'add_ingredient']);
    Route::put('/ingredients/{ingredient_id}', [putIngredientController::class, 'edit_ingredient']);
    Route::delete('/ingredients/{ingredient_id}', [deleteIngredientController::class, 'delete_ingredient']);
    Route::post('/ingredients/search', [SearchIngredientController::class, 'search']);
});

//========================================================================================================================================================================================================================================================================================================================

//Reservations================================================================================================================================================

use App\Http\Controllers\Api\ReservationsControllers\deleteReservationController;
use App\Http\Controllers\Api\ReservationsControllers\getAllReservationsController;
use App\Http\Controllers\Api\ReservationsControllers\getOneReservationController;
use App\Http\Controllers\Api\ReservationsControllers\postReservationController;
use App\Http\Controllers\Api\ReservationsControllers\putReservationController;
use App\Http\Controllers\Api\ReservationsControllers\putReservationStatusController;
use App\Http\Controllers\Api\ReservationsControllers\SearchReservationController;
use App\Http\Controllers\Api\ReservationsControllers\putReservationTablesController;

Route::middleware(['auth:sanctum', 'is.loggedin', 'emailVerified' , 'is.waiterormanagerorcustomer'])->group(function () {
    Route::get('/reservations/{reservations_id}', [getOneReservationController::class, 'get_one_reservation']);
    Route::post('/reservations', [postReservationController::class, 'add_reservation']);
    Route::delete('/reservations/{reservation_id}', [deleteReservationController::class, 'delete_reservation']);
    Route::get('/reservations', [getAllReservationsController::class, 'get_all_reservations']);
});

Route::put('/reservations/{reservation_id}', [putReservationController::class, 'edit_reservation']);


Route::middleware('auth:sanctum')->group(function () {
    Route::put('reservations/{reservation_id}/tables', [putReservationTablesController::class, 'updateTables']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('reservations/{reservation_id}/tables', [putReservationTablesController::class, 'detachAllTables']);
});

Route::put('/reservations/status/{reservation_id}', [putReservationStatusController::class, 'edit_status']);
Route::post('/reservations/search', [SearchReservationController::class, 'search']);


//====================================================================================================================================================================================================

//Tables======================================================================================================================================================================================

use App\Http\Controllers\Api\TablesControllers\deleteTableController;
use App\Http\Controllers\Api\TablesControllers\getAllTablesController;
use App\Http\Controllers\Api\TablesControllers\getOneTableController;
use App\Http\Controllers\Api\TablesControllers\postTableController;
use App\Http\Controllers\Api\TablesControllers\putTableController;
use App\Http\Controllers\Api\TablesControllers\SearchTableController;
use App\Http\Controllers\Api\OrdersControllers\TodayOrdersController;

Route::middleware(['auth:sanctum', 'is.waiterormanager', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::post('/tables', [postTableController::class, 'add_table']);
    Route::put('/tables/{table_id}', [putTableController::class, 'edit_table']);
    Route::delete('/tables/{table_id}', [deleteTableController::class, 'delete_table']);
});

Route::middleware(['auth:sanctum', 'is.loggedin', 'emailVerified'])->group(function () {
    Route::get('/tables', [getAllTablesController::class, 'get_all_tables']);
    Route::get('/tables/{table_id}', [getOneTableController::class, 'get_one_table']);
    Route::post('/tables/search', [SearchTableController::class, 'search']);
});

//Orders====================================================================================================================================================================================================
use App\Http\Controllers\Api\OrdersControllers\deleteOrderController;
use App\Http\Controllers\Api\OrdersControllers\getAllOrdersController;
use App\Http\Controllers\Api\OrdersControllers\getAllOrdersWithTrashController;
use App\Http\Controllers\Api\OrdersControllers\getOneOrderController;
use App\Http\Controllers\Api\OrdersControllers\PostOrderController;
use App\Http\Controllers\Api\OrdersControllers\putOrderController;
use App\Http\Controllers\Api\OrdersControllers\softDeleteOrderController;

Route::get('/orders', [getAllOrdersController::class, 'get_all_orders']);
Route::get('/orders/with_trash', [getAllOrdersWithTrashController::class, 'get_all_orders']);
Route::get('/orders/{order}', [getOneOrderController::class, 'get_one_order']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/orders/{order}', [putOrderController::class, 'edit_order']);
});
Route::delete('/orders/{order}', [deleteOrderController::class, 'delete_order']);
Route::delete('/orders/{order}/soft', [softDeleteOrderController::class, 'delete_order']);
Route::get('/orders/today', [TodayOrdersController::class, 'index']);
Route::put('/orders/{order}/complete', [CompleteOrderController::class, 'update']);
Route::middleware('auth:sanctum')->post('/orders', [PostOrderController::class, 'add_order']);

//Cart====================================================================================================================================================================================================

use App\Http\Controllers\Api\CartControllers\CartController;

Route::middleware(['auth:sanctum', 'is.loggedin', 'api-session' ,'emailVerified'])->group(function () {
    Route::get('/get_cart', [CartController::class, 'index']);
    Route::post('/add_item_cart', [CartController::class, 'add']);
    Route::put('/update_item_cart', [CartController::class, 'update']);
    Route::post('/delete_item_cart', [CartController::class, 'remove']);
    Route::post('/purchase_confirmation', [CartController::class, 'checkout']);
    Route::delete('/cancel_order/{OrderId}', [CartController::class, 'cancelOrder']);
    Route::post('/revert_order_to_cart', [CartController::class, 'revertOrderToCart']);
    Route::get('/restore_order/{OrderId}', [CartController::class, 'restoreOrder']);
});

//====================================================================================================================================================================================================
