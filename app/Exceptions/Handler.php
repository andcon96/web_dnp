<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Session;
use Artisan;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
        'Symfony\Component\HttpKernel\Exception\HttpException'
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    protected function prepareException(Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        } elseif ($e instanceof AuthorizationException) {
            $e = new AccessDeniedHttpException($e->getMessage(), $e);
        } elseif ($e instanceof TokenMismatchException) {
              return redirect()->route('login');
              dd('a');
        }   

        return $e;
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        //return parent::render($request, $exception);
        //return redirect()->action('/');
        if ($exception instanceof AuthenticationException) {
            session(['url.now' => url()->current()]);
            dd('b');
            return redirect('/');
        }

        if ($exception instanceof TokenMismatchException){
            // Redirect to a form. Here is an example of how I handle mine
            Artisan::call('cache:clear');
            Artisan::call('config:cache');
            return redirect('http://danapaint.sytes.net:8181/home');
            // return redirect($request->fullUrl())->with('error',"Oops! Something has happened. Please refresh the browser.");
        }

        return parent::render($request, $exception);
    }
}
