<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequestLogger
{
    private array $excludes = [
        '_debugbar',
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('logging.request.enable')) {
            if ($this->isWrite($request)) {
                $this->write($request);
            }
        }
        return $next($request);
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function isWrite(Request $request): bool
    {
        return !in_array($request->path(), $this->excludes, true);
    }

    /**
     * @param Request $request
     */
    private function write(Request $request): void
    {
        Log::debug($request->method(), ['url' => $request->fullUrl(), 'request' => $request->all()]);
    }

}
