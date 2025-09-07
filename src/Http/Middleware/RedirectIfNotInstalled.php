<?php
namespace Softmax\Installer\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Softmax\Installer\Facades\Installer;

class RedirectIfNotInstalled
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Allow access to installer routes
        if ($request->is('softmax-installer*') || 
            $request->is('vendor/*') || 
            $request->is('storage/*') || 
            $request->is('build/*') ||
            $request->is('assets/*')) {
            return $next($request);
        }

        // Check if installation is completed
        if (!Installer::isInstalled()) {
            return redirect()->route('softmax.installer.start');
        }

        // Verify encryption key matches
        if (!Installer::verifyEncryptionKey()) {
            return redirect()->route('softmax.installer.start');
        }

        return $next($request);
    }
}
