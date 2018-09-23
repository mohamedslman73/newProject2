<?php

namespace App\Exceptions;

use App\Modules\Api\Merchant\MerchantApiController;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {

        if($exception instanceof TokenMismatchException){
            return response()->view('view.errors.TokenMismatchException', ['exception'=>$exception], 500);
        }elseif($this->isHttpException($exception)){


            switch ($exception->getStatusCode()) {

                case 404:
                case 405:
                case 500:
                    return response()->view('view.errors.404', ['exception'=>$exception], 500);

                    break;
            }

        }
        
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() && (last($exception->guards()) != 'apiMerchant')) {
            return response()->json([
                'status' => false,
                'code' => 102,
                'msg' => __('Unauthenticated'),
                'data' => false,
            ], 401);
        }
        if(last($exception->guards()) == 'staff'){
            return redirect()->guest(route('staff.login'));
        } else if(last($exception->guards()) == 'merchant_staff'){
            return redirect()->guest(route('merchant.login'));
        } else if(last($exception->guards()) == 'apiMerchant'){
            return response()->json([
                'status' => false,
                'msg' => __('Unauthenticated'),
                'code' => 102,
                'data'=> (object)[]
            ],200);
        } else if(last($exception->guards()) == 'api'){
            return response()->json([
                'status' => false,
                'msg' => __('Unauthenticated'),
                'code' => 302,
                'data'=> (object)[]
            ],200);
        } else if (last($exception->guards()) == 'ApiPartner') {
            return response()->json([
                'status' => false,
                'msg' => __('Unauthenticated'),
                'code' => 302,
                'data' => (object)[]
            ], 200);
        }
        else {
            return redirect()->guest(route('login'));
        }
    }
}
