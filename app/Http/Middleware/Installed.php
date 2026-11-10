<?php

namespace App\Http\Middleware;

use Closure;

class Installed
{
    /**
     * Only allow Access if the application IS installed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!file_exists(base_path("INSTALLED")) /* [multimod] chemin absolu */)
        {
            abort(403,'Application not installed');
            die();
        }

        return $next($request);
    }
}
