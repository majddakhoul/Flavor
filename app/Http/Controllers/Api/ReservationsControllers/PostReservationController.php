<?php

namespace App\Http\Controllers\Api\ReservationsControllers;

use App\Mail\ReservationCodeMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReservationsRequests\postReservationRequest;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PostReservationController extends Controller
{
    public function add_reservation(postReservationRequest $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validated();
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'status' => 401,
                    'message' => 'Unauthenticated.',
                    'data' => null,
                ], 401);
            }

            // معالجة حالة الزبائن
            if ($user->user_type === 'Customer') {
                $customer = $user->customer;

                if (!$customer) {
                    return response()->json([
                        'success' => false,
                        'status' => 403,
                        'message' => 'Customer profile not found',
                        'data' => null,
                    ], 403);
                }

                // التحقق من الحظر
                if ($customer->ban) {
                    // حساب تاريخ انتهاء الحظر (7 أيام من تاريخ الحظر)
                    $banEndDate = Carbon::parse($customer->ban_date)->addDays(7);

                    if (now()->lt($banEndDate)) {
                        return response()->json([
                            'success' => false,
                            'status' => 403,
                            'message' => 'Your account is banned until ' . $banEndDate->format('Y-m-d'),
                            'data' => null,
                        ], 403);
                    } else {
                        // إلغاء الحظر وحذف الحجوزات الملغية
                        $customer->unbanAndClearCancellations();
                    }
                }

                // التحقق من الحجوزات الملغية
                $cancelledCount = $customer->cancelledReservationsCount();

                // تسجيل عدد الحجوزات الملغية للتحقق
                Log::info("Customer {$customer->id} has {$cancelledCount} cancelled reservations");

                if ($cancelledCount >= 5) {
                    // حظر الزبون وتسجيل العملية
                    $customer->ban();
                    DB::commit();
                    Log::warning("Customer {$customer->id} banned due to {$cancelledCount} cancelled reservations");

                    return response()->json([
                        'success' => false,
                        'status' => 403,
                        'message' => 'Your account has been banned due to 5 cancelled reservations',
                        'data' => null,
                    ], 403);
                }
            }

            // حفظ تاريخ الحجز من start_time
            $validatedData['date'] = Carbon::parse($request->start_time)->format('Y-m-d');

            $validatedData['reservation_code'] = Str::random(5);
            $validatedData['status'] = $request->status ?? 'Pending';
            $tableIds = $validatedData['table_ids'];
            unset($validatedData['table_ids']);

            // تحديد نوع الحجز والمستخدم المرتبط
            if ($user->user_type === 'Employee' || $user->user_type === 'Manager') {
                $validatedData['type'] = 'Local';
                $validatedData['employee_id'] = $user->employee?->id;
            } elseif ($user->user_type === 'Customer') {
                $validatedData['type'] = 'Application';
                $validatedData['customer_id'] = $user->customer?->id;
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'Unauthorized user type.',
                    'data' => null,
                ], 403);
            }

            // إنشاء الحجز
            $reservation = Reservation::create($validatedData);

            if ($reservation && !empty($tableIds)) {
                $tables = Table::whereIn('id', $tableIds)->get();
                $tableNumbers = $tables->pluck('table_number')->toArray();
                $existingTableIds = $tables->pluck('id')->toArray();

                // التحقق من وجود جميع الطاولات
                if (count($tableIds) !== count($existingTableIds)) {
                    throw new \Exception('Some tables not found');
                }

                // ربط الطاولات بالحجز
                foreach ($tableIds as $tableId) {
                    $reservation->tables()->attach($tableId, [
                        'start_time' => $request->start_time,
                        'end_time' => $request->end_time,
                    ]);
                }

                // إرسال البريد للزبون
                if ($user->user_type === 'Customer') {
                    Mail::to($user->email)->send(
                        new ReservationCodeMail($reservation, $tableNumbers)
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 201,
                'message' => 'The reservation has been saved successfully.',
                'data' => [
                    'reservation' => $reservation,
                    'table_numbers' => $tableNumbers ?? []
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reservation creation failed: ' . $e->getMessage() . "\n" . $e->getTraceAsString());

            $message = 'Reservation creation failed';
            if ($e->getMessage() === 'Some tables not found') {
                $message = 'Some tables not found';
            } elseif (strpos($e->getMessage(), 'conflict') !== false) {
                $message = 'Time slot conflict with existing reservation';
            }

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => $message,
                'data' => null,
            ], 500);
        }
    }
}
