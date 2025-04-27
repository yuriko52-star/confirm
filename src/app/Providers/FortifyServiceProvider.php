<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;


use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\LoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // fortifyのカスタマイズや設定を行う（ユーザ登録、ログイン、アクセス制限）
        Fortify::createUsersUsing(CreateNewUser::class);
        // 新しいユーザーを作成する際の処理（バリデーションやデーターベースへの保存）
        Fortify::registerView(function () {
            return view('auth.register');
        });
        Fortify::loginView(function() {
            return view('auth.login');
        });

        RateLimiter::for('login', function (Request $request) {
           $email = (string) $request->email;

            return Limit::perMinute(10)->by
            ($email . $request->ip());
        });
        // ログインのリクエスト頻度を制限する設定。一分間に最大10回のログイン試行を許可。制限は、ユーザーのメールアドレスとIPアドレスを組み合わせたキーに基づく。同一メールアドレスまたは同一IPアドレスからの過剰な試行を防ぐ仕組み。

        $this->app->bind(FortifyLoginRequest::class, LoginRequest::class);
        // Fortifyのデフォルトの FortifyLoginRequest クラスを、カスタムの LoginRequest クラスに置き換える。これにより、ログイン処理に必要なデータバリデーションや認証ロジックを独自に拡張できる。
    }
}
