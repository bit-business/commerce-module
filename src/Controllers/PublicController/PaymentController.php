<?php

namespace NadzorServera\Skijasi\Module\Commerce\Controllers\PublicController;

use Exception;
use Illuminate\Http\Request;
use NadzorServera\Skijasi\Controllers\Controller;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Module\Commerce\Abstracts\SkijasiPayment as AbstractsSkijasiPayment;
use NadzorServera\Skijasi\Module\Commerce\Interfaces\SkijasiPayment;
use NadzorServera\Skijasi\Module\Commerce\Models\Payment;
use NadzorServera\Skijasi\Module\Commerce\Models\PaymentOption;

class PaymentController extends Controller
{
    protected $slugs = [];

    public function __construct()
    {
        $payments_module_list = config('skijasi-commerce.payments');

        foreach ($payments_module_list as $module) {
            $module_class = new $module();
            if ($module_class instanceof SkijasiPayment && $module_class instanceof AbstractsSkijasiPayment) {
                foreach ($module_class->getProtectedPaymentSlug() as $key => $slug) {
                    $this->slugs[] = $slug;
                }
            } else {
                throw new Exception('Class in skijasi commerce payment config must be instance of SkijasiPayment abstract & interface');
            }
        }
    }

    public function browse()
    {
        try {
            $payments = Payment::with(['options' => function ($query) {
                return $query->where('is_active', 1);
            }])->where('is_active', 1)->get();

            $data['payments'] = $payments->toArray();

            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function read(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Module\Commerce\Models\PaymentOption,id',
            ]);

            $data['payment_option'] = PaymentOption::where('id', $request->id)->first();

            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }
}
