<?php
declare(strict_types=1);

if (!function_exists('sanitizeInput')) {
    function sanitizeInput($data, bool $allowHtml = false) {
        if (is_array($data)) {
            return array_map('sanitizeInput', $data);
        }

        $filtered = trim($data);

        if ($allowHtml) {
            $filtered = strip_tags($filtered, '<p><a><br><strong><em><ul><ol><li>');
        } else {
            $filtered = htmlspecialchars($filtered, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        return $filtered;
    }
}