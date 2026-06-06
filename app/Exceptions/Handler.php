<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // SymfonyのNotFoundHttpExceptionをキャッチする形にします
        $this->renderable(function (NotFoundHttpException $e, $request) {

            // 先ほどのエラーメッセージ「No query results for model [App\Models\Contact]」が含まれているかチェック
            if (str_contains($e->getMessage(), 'App\\Models\\Contact')) {
                return response()->json([
                    'error' => 'お問い合わせが見つかりませんでした',
                ], 404);
            }

            // それ以外の普通の404（存在しないURLが叩かれた等）はLaravelの標準処理に任せる
        });
    }
}
