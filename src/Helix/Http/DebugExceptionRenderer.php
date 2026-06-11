<?php

namespace Helix\Http;

class DebugExceptionRenderer
{
    private const CONTEXT_LINES = 10;
    private const PHP_KEYWORDS = [
        '__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch',
        'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else',
        'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile',
        'eval', 'exit', 'extends', 'final', 'finally', 'fn', 'for', 'foreach', 'function', 'global',
        'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof',
        'interface', 'isset', 'list', 'match', 'namespace', 'new', 'or', 'print', 'private',
        'protected', 'public', 'readonly', 'require', 'require_once', 'return', 'static', 'switch',
        'throw', 'trait', 'try', 'unset', 'use', 'var', 'while', 'xor', 'yield', 'true', 'false',
        'null', 'int', 'float', 'bool', 'string', 'void', 'never', 'mixed', 'self', 'parent',
    ];

    public static function render(\Throwable $e, int $status, string $message, ?Request $request = null): string
    {
        $filePath = $e->getFile();
        $lineNumber = $e->getLine();
        $codeSnippet = self::getCodeSnippet($filePath, $lineNumber);
        $traceHtml = self::renderTrace($e);
        $requestHtml = $request ? self::renderRequest($request) : '';

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{$status} — FreightForge</title>
<style>
  *,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
  body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#0f172a;color:#e2e8f0;min-height:100vh}
  .header{background:#1e293b;border-bottom:1px solid #334155;padding:1.5rem 2rem}
  .header .code{font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em}
  .header h1{font-size:1.1rem;font-weight:700;color:#f97316;margin-top:0.25rem;font-family:monospace}
  .header .msg{font-size:0.9rem;color:#94a3b8;margin-top:0.5rem}
  .header .file{font-size:0.8rem;color:#64748b;margin-top:0.25rem}
  .header .file strong{color:#94a3b8}
  .content{display:flex;gap:0;min-height:calc(100vh - 120px)}
  .main{flex:1;padding:1.5rem 2rem;overflow-y:auto}
  .sidebar{width:400px;background:#1e293b;border-left:1px solid #334155;padding:1.5rem;overflow-y:auto;flex-shrink:0;font-size:0.8rem}
  .sidebar h3{font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;margin-bottom:0.75rem;margin-top:1.5rem}
  .sidebar h3:first-child{margin-top:0}
  .sidebar .kv{display:flex;gap:0.5rem;margin-bottom:0.4rem}
  .sidebar .kv .k{color:#64748b;min-width:80px;flex-shrink:0}
  .sidebar .kv .v{color:#e2e8f0;word-break:break-all}
  .code-block{margin-bottom:1.5rem}
  .code-block .file-header{font-size:0.75rem;color:#64748b;background:#1e293b;padding:0.4rem 0.75rem;border:1px solid #334155;border-bottom:none;border-radius:0.375rem 0.375rem 0 0;font-family:monospace}
  .code-block table{border:1px solid #334155;border-radius:0 0 0.375rem 0.375rem;overflow:hidden;width:100%;border-collapse:collapse;font-family:monospace;font-size:0.8rem;line-height:1.6}
  .code-block table td{padding:0 0.75rem;vertical-align:top;white-space:pre}
  .code-block table .num{color:#475569;text-align:right;padding-right:0.75rem;border-right:1px solid #334155;user-select:none;width:1%;min-width:3.5rem}
  .code-block table .line{padding-left:0.75rem}
  .code-block table tr.highlight{background:#7f1d1d}
  .code-block table tr.highlight .num{background:#7f1d1d;color:#fca5a5}
  .code-block table tr.highlight .line{background:#7f1d1d}
  .kw{color:#818cf8}.str{color:#6ee7b7}.cm{color:#64748b}.nu{color:#fcd34d}.fn{color:#34d399}.vr{color:#fca5a5}
  .trace-section h2{font-size:0.85rem;font-weight:600;color:#cbd5e1;margin-bottom:0.75rem}
  .trace-item{padding:0.6rem 0.75rem;border:1px solid #334155;border-radius:0.375rem;margin-bottom:0.5rem;background:#1e293b;font-size:0.8rem;font-family:monospace;line-height:1.5}
  .trace-item .func{color:#818cf8}
  .trace-item .args{color:#64748b;font-size:0.75rem}
  .trace-item .loc{color:#475569;font-size:0.75rem;margin-top:0.15rem}
  .trace-item:first-child{border-color:#7f1d1d}
  .trace-item:first-child .func{color:#fca5a5}
  ::-webkit-scrollbar{width:6px}::-webkit-scrollbar-track{background:transparent}::-webkit-scrollbar-thumb{background:#334155;border-radius:3px}
</style>
</head>
<body>
<div class="header">
  <div class="code">{$status} — " . htmlspecialchars(get_class($e)) . "</div>
  <h1>" . htmlspecialchars($message) . "</h1>
  <div class="msg">in " . htmlspecialchars($filePath) . " : {$lineNumber}</div>
</div>
<div class="content">
  <div class="main">
    <div class="code-block">
      <div class="file-header">{$filePath}</div>
      {$codeSnippet}
    </div>
    <div class="trace-section">
      <h2>Stack Trace</h2>
      {$traceHtml}
    </div>
  </div>
  <div class="sidebar">
    {$requestHtml}
  </div>
</div>
</body>
</html>
HTML;
    }

    private static function getCodeSnippet(string $file, int $line): string
    {
        if (!is_readable($file)) {
            return '<div style="color:#64748b;padding:0.75rem;border:1px solid #334155;border-radius:0.375rem;font-size:0.8rem">File not readable</div>';
        }

        $lines = file($file);
        if ($lines === false) {
            return '<div style="color:#64748b;padding:0.75rem;border:1px solid #334155;border-radius:0.375rem;font-size:0.8rem">Unable to read file</div>';
        }

        $totalLines = count($lines);
        $start = max(0, $line - self::CONTEXT_LINES - 1);
        $end = min($totalLines, $line + self::CONTEXT_LINES);

        $rows = '';
        for ($i = $start; $i < $end; $i++) {
            $lineNum = $i + 1;
            $isHighlight = $lineNum === $line;
            $code = rtrim($lines[$i], "\r\n");
            $highlighted = self::highlight($code);
            $rows .= '<tr class="' . ($isHighlight ? 'highlight' : '') . '">'
                . '<td class="num">' . $lineNum . '</td>'
                . '<td class="line">' . $highlighted . '</td>'
                . '</tr>';
        }

        return '<table>' . $rows . '</table>';
    }

    private static function highlight(string $code): string
    {
        $code = htmlspecialchars($code);

        $code = preg_replace(
            '/(\/\/.*$|\#.*$)/m',
            '<span class="cm">$1</span>',
            $code
        );

        $code = preg_replace(
            '/(&lt;\?php|\?&gt;)/',
            '<span class="kw">$1</span>',
            $code
        );

        $keywords = implode('|', array_map('preg_quote', self::PHP_KEYWORDS));
        $code = preg_replace(
            '/\b(' . $keywords . ')\b/',
            '<span class="kw">$1</span>',
            $code
        );

        $code = preg_replace(
            '/\b([A-Za-z_][A-Za-z0-9_]*)\s*\(/',
            '<span class="fn">$1</span>(',
            $code
        );

        $code = preg_replace(
            '/([\'"])[^\1]*?\1/',
            '<span class="str">$0</span>',
            $code
        );

        $code = preg_replace(
            '/\b(\d+\.?\d*)\b/',
            '<span class="nu">$1</span>',
            $code
        );

        $code = preg_replace(
            '/\$[A-Za-z_][A-Za-z0-9_]*/',
            '<span class="vr">$0</span>',
            $code
        );

        if (strlen($code) === 0) {
            $code = '&nbsp;';
        }

        return $code;
    }

    private static function renderTrace(\Throwable $e): string
    {
        $trace = $e->getTrace();
        $html = '';

        $html .= '<div class="trace-item">'
            . '<div class="func">{main}</div>'
            . '<div class="loc">' . htmlspecialchars($e->getFile()) . ' : ' . $e->getLine() . '</div>'
            . '</div>';

        foreach ($trace as $i => $frame) {
            $file = $frame['file'] ?? '[internal]';
            $line = $frame['line'] ?? 0;
            $function = $frame['function'] ?? '';
            $class = $frame['class'] ?? '';
            $type = $frame['type'] ?? '';

            $args = '';
            if (isset($frame['args']) && count($frame['args']) > 0) {
                $argParts = [];
                foreach ($frame['args'] as $arg) {
                    if (is_string($arg)) {
                        $argParts[] = '"' . htmlspecialchars(substr($arg, 0, 50)) . '"';
                    } elseif (is_numeric($arg)) {
                        $argParts[] = $arg;
                    } elseif (is_bool($arg)) {
                        $argParts[] = $arg ? 'true' : 'false';
                    } elseif (is_null($arg)) {
                        $argParts[] = 'null';
                    } elseif (is_array($arg)) {
                        $argParts[] = '[' . count($arg) . ' items]';
                    } elseif (is_object($arg)) {
                        $argParts[] = get_class($arg);
                    } else {
                        $argParts[] = gettype($arg);
                    }
                }
                $args = '(<span class="args">' . implode(', ', $argParts) . '</span>)';
            } else {
                $args = '()';
            }

            $html .= '<div class="trace-item">'
                . '<div class="func">' . htmlspecialchars($class . $type . $function) . $args . '</div>'
                . '<div class="loc">' . htmlspecialchars($file) . ($line ? ' : ' . $line : '') . '</div>'
                . '</div>';
        }

        return $html;
    }

    private static function renderRequest(Request $request): string
    {
        $html = '<h3>Request</h3>';

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $html .= '<div class="kv"><span class="k">Method</span><span class="v">' . htmlspecialchars($method) . '</span></div>';
        $html .= '<div class="kv"><span class="k">URI</span><span class="v">' . htmlspecialchars($uri) . '</span></div>';

        if (!empty($_GET)) {
            $html .= '<h3>Query</h3>';
            foreach ($_GET as $k => $v) {
                $html .= '<div class="kv"><span class="k">' . htmlspecialchars($k) . '</span><span class="v">' . htmlspecialchars(is_array($v) ? json_encode($v) : $v) . '</span></div>';
            }
        }

        if (!empty($_POST)) {
            $html .= '<h3>Body</h3>';
            foreach ($_POST as $k => $v) {
                if (str_contains(strtolower($k), 'password')) {
                    $html .= '<div class="kv"><span class="k">' . htmlspecialchars($k) . '</span><span class="v">••••••••</span></div>';
                } else {
                    $html .= '<div class="kv"><span class="k">' . htmlspecialchars($k) . '</span><span class="v">' . htmlspecialchars(is_array($v) ? json_encode($v) : $v) . '</span></div>';
                }
            }
        }

        $html .= '<h3>Headers</h3>';
        $headers = [];
        foreach ($_SERVER as $k => $v) {
            if (str_starts_with($k, 'HTTP_')) {
                $name = str_replace('_', '-', strtolower(substr($k, 5)));
                $name = ucwords($name, '-');
                $headers[$name] = $v;
            }
        }
        ksort($headers);
        foreach ($headers as $k => $v) {
            $html .= '<div class="kv"><span class="k">' . htmlspecialchars($k) . '</span><span class="v">' . htmlspecialchars($v) . '</span></div>';
        }

        $html .= '<h3>Server</h3>';
        $serverKeys = ['SERVER_SOFTWARE', 'SERVER_NAME', 'SERVER_ADDR', 'SERVER_PORT', 'DOCUMENT_ROOT', 'REQUEST_SCHEME', 'REMOTE_ADDR', 'PHP_SELF'];
        foreach ($serverKeys as $k) {
            if (!empty($_SERVER[$k])) {
                $html .= '<div class="kv"><span class="k">' . htmlspecialchars($k) . '</span><span class="v">' . htmlspecialchars($_SERVER[$k]) . '</span></div>';
            }
        }

        if (!empty($_ENV)) {
            $html .= '<h3>Env</h3>';
            foreach ($_ENV as $k => $v) {
                if (str_contains(strtolower($k), 'pass') || str_contains(strtolower($k), 'secret') || str_contains(strtolower($k), 'key')) {
                    continue;
                }
                $html .= '<div class="kv"><span class="k">' . htmlspecialchars($k) . '</span><span class="v">' . htmlspecialchars($v) . '</span></div>';
            }
        }

        return $html;
    }
}
