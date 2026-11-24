<?php
namespace App\Core;

/**
 * Perender template PHP (lapisan Boundary).
 * Template berada di frontend/views/. Data di-extract menjadi variabel.
 */
final class View
{
    private static string $basePath = '';

    public static function setBasePath(string $path): void
    {
        self::$basePath = rtrim($path, '/');
    }

    /**
     * Render template di dalam layout.
     * @param string|null $layout nama layout di views/layouts (null = tanpa layout)
     */
    public static function render(string $template, array $data = [], ?string $layout = 'pelanggan'): string
    {
        $content = self::partial($template, $data);
        if ($layout === null) {
            return $content;
        }
        return self::partial('layouts/' . $layout, $data + ['content' => $content]);
    }

    public static function partial(string $template, array $data = []): string
    {
        $file = self::$basePath . '/' . $template . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("Template tidak ditemukan: {$template}");
        }
        extract($data, EXTR_SKIP);
        ob_start();
        try {
            include $file;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        return (string) ob_get_clean();
    }
}
