<?php

namespace Helix\Http;

use Helix\Container\Container;
use Helix\Http\Middleware\MiddlewareInterface;
use Helix\Routing\Router;

class Kernel
{
    private array $middleware = [];

    public function __construct(
        private readonly Container $container,
        private readonly Router $router
    ) {}

    public function pushMiddleware(MiddlewareInterface|string $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    public function handle(Request $request): Response
    {
        try {
            $core = function (Request $request) {
                return $this->router->dispatch($request);
            };

            $pipeline = $this->buildPipeline($core, $this->middleware);

            return $pipeline($request);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    private function buildPipeline(callable $core, array $middleware): callable
    {
        $pipeline = $core;

        foreach (array_reverse($middleware) as $mw) {
            $pipeline = function (Request $request) use ($mw, $pipeline) {
                if (is_string($mw)) {
                    $mw = $this->container->get($mw);
                }
                return $mw->handle($request, $pipeline);
            };
        }

        return $pipeline;
    }

    private function handleException(\Throwable $e): Response
    {
        $status = 500;
        $message = 'Internal Server Error';
        $detail = $e->getMessage();

        if ($e instanceof \Helix\Routing\RouteNotFoundException) {
            $status = 404;
            $message = 'Not Found';
        }

        if ($e instanceof \Helix\Validation\ValidationException) {
            $status = 422;
            $message = 'Validation Failed';
        }

        $config = $this->container->get('config.app');
        $debug = $config['debug'] ?? false;

        $request = $this->container->get(Request::class);

        if ($request->expectsJson()) {
            $data = [
                'error' => true,
                'message' => $message,
                'detail' => $detail,
            ];
            if ($debug) {
                $data['exception'] = [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ];
            }
            return new JsonResponse($data, $status);
        }

        if ($debug) {
            $html = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">'
                . '<meta name="viewport" content="width=device-width,initial-scale=1">'
                . '<title>' . $status . ' — FreightForge</title>'
                . '<style>'
                . '*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}'
                . 'body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#f8fafc;color:#1e293b;min-height:100vh;display:flex;align-items:center;justify-content:center}'
                . '.card{background:#fff;border:1px solid #e2e8f0;border-radius:1rem;padding:3rem;max-width:720px;width:90%;text-align:left}'
                . '.code{font-size:4rem;font-weight:800;color:#f97316;line-height:1;margin-bottom:0.5rem;text-align:center}'
                . 'h1{font-size:1.5rem;font-weight:700;margin-bottom:0.75rem;text-align:center}'
                . 'h2{font-size:1rem;font-weight:600;margin-top:1.5rem;margin-bottom:0.5rem;color:#475569}'
                . 'p{color:#64748b;margin-bottom:1rem;line-height:1.6}'
                . '.meta{font-size:0.85rem;color:#94a3b8;background:#f1f5f9;padding:0.75rem 1rem;border-radius:0.5rem;margin-bottom:1rem;word-break:break-word}'
                . '.trace{font-size:0.8rem;color:#64748b;background:#f8fafc;border:1px solid #e2e8f0;border-radius:0.5rem;padding:1rem;margin-bottom:1.5rem;overflow-x:auto;white-space:pre;font-family:monospace;line-height:1.5}'
                . '.btn{display:inline-block;padding:0.65rem 1.5rem;border-radius:0.5rem;font-size:0.9rem;font-weight:600;text-decoration:none;transition:all 0.2s}'
                . '.btn-primary{background:#f97316;color:#fff}.btn-primary:hover{background:#ea580c}'
                . '</style></head><body><div class="card">'
                . '<div class="code">' . $status . '</div>'
                . '<h1>' . htmlspecialchars(get_class($e)) . '</h1>'
                . '<div class="meta"><strong>Message:</strong> ' . htmlspecialchars($detail) . '</div>'
                . '<div class="meta"><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ' : ' . $e->getLine() . '</div>'
                . '<h2>Stack Trace</h2>'
                . '<div class="trace">' . htmlspecialchars($e->getTraceAsString()) . '</div>'
                . '<a href="/" class="btn btn-primary">Go Home</a>'
                . '</div></body></html>';

            return new Response($html, $status, ['Content-Type' => 'text/html; charset=utf-8']);
        }

        $detailHtml = $status === 404
            ? ''
            : '<div class="detail">' . htmlspecialchars($detail) . '</div>';

        $html = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">'
            . '<meta name="viewport" content="width=device-width,initial-scale=1">'
            . '<title>' . $status . ' — FreightForge</title>'
            . '<style>'
            . '*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}'
            . 'body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#f8fafc;color:#1e293b;min-height:100vh;display:flex;align-items:center;justify-content:center}'
            . '.card{background:#fff;border:1px solid #e2e8f0;border-radius:1rem;padding:3rem;max-width:520px;width:90%;text-align:center}'
            . '.code{font-size:4rem;font-weight:800;color:#f97316;line-height:1;margin-bottom:0.5rem}'
            . 'h1{font-size:1.5rem;font-weight:700;margin-bottom:0.75rem}'
            . 'p{color:#64748b;margin-bottom:1.5rem;line-height:1.6}'
            . '.detail{font-size:0.85rem;color:#94a3b8;background:#f1f5f9;padding:0.75rem 1rem;border-radius:0.5rem;margin-bottom:1.5rem;word-break:break-word}'
            . '.btn{display:inline-block;padding:0.65rem 1.5rem;border-radius:0.5rem;font-size:0.9rem;font-weight:600;text-decoration:none;transition:all 0.2s}'
            . '.btn-primary{background:#f97316;color:#fff}.btn-primary:hover{background:#ea580c}'
            . '</style></head><body><div class="card">'
            . '<div class="code">' . $status . '</div>'
            . '<h1>' . htmlspecialchars($message) . '</h1>'
            . '<p>' . htmlspecialchars($message) . '</p>'
            . $detailHtml
            . '<a href="/" class="btn btn-primary">Go Home</a>'
            . '</div></body></html>';

        return new Response($html, $status, ['Content-Type' => 'text/html; charset=utf-8']);
    }
}
