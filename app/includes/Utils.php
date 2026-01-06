<?php
/**
 * Utility Class
 * 
 * Collection of common utility functions used across the application.
 * Provides reusable methods for file handling, string manipulation, and more.
 * 
 * @package SkillShareLocal
 * @version 1.0.0
 */

require_once __DIR__ . '/../config/constants.php';

class Utils {
    
    /**
     * Sanitize output for HTML display
     * 
     * @param string $string String to sanitize
     * @return string Sanitized string
     */
    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Generate a random string
     * 
     * @param int $length Length of the string
     * @return string Random string
     */
    public static function generateRandomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
    
    /**
     * Format date for display
     * 
     * @param string $date Date string
     * @param string $format Output format (default: DISPLAY_DATE_FORMAT)
     * @return string Formatted date
     */
    public static function formatDate($date, $format = DISPLAY_DATE_FORMAT) {
        if (empty($date)) {
            return '';
        }
        
        try {
            $dateTime = new DateTime($date);
            return $dateTime->format($format);
        } catch (Exception $e) {
            error_log("Date formatting error: " . $e->getMessage());
            return $date;
        }
    }
    
    /**
     * Format datetime for display
     * 
     * @param string $datetime Datetime string
     * @param string $format Output format (default: DISPLAY_DATETIME_FORMAT)
     * @return string Formatted datetime
     */
    public static function formatDateTime($datetime, $format = DISPLAY_DATETIME_FORMAT) {
        return self::formatDate($datetime, $format);
    }
    
    /**
     * Format time for display
     * 
     * @param string $time Time string
     * @param string $format Output format (default: DISPLAY_TIME_FORMAT)
     * @return string Formatted time
     */
    public static function formatTime($time, $format = DISPLAY_TIME_FORMAT) {
        return self::formatDate($time, $format);
    }
    
    /**
     * Get time ago string (e.g., "2 hours ago")
     * 
     * @param string $datetime Datetime string
     * @return string Time ago string
     */
    public static function timeAgo($datetime) {
        if (empty($datetime)) {
            return 'Unknown';
        }
        
        try {
            $time = strtotime($datetime);
            $diff = time() - $time;
            
            if ($diff < 60) {
                return 'Just now';
            } elseif ($diff < 3600) {
                $mins = floor($diff / 60);
                return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
            } elseif ($diff < 86400) {
                $hours = floor($diff / 3600);
                return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
            } elseif ($diff < 604800) {
                $days = floor($diff / 86400);
                return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
            } elseif ($diff < 2592000) {
                $weeks = floor($diff / 604800);
                return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
            } elseif ($diff < 31536000) {
                $months = floor($diff / 2592000);
                return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
            } else {
                $years = floor($diff / 31536000);
                return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
            }
        } catch (Exception $e) {
            error_log("Time ago error: " . $e->getMessage());
            return 'Unknown';
        }
    }
    
    /**
     * Format currency amount
     * 
     * @param float $amount Amount to format
     * @param string $currency Currency code (default: DEFAULT_CURRENCY)
     * @return string Formatted currency string
     */
    public static function formatCurrency($amount, $currency = DEFAULT_CURRENCY) {
        $symbol = get_currency_symbol($currency);
        return $symbol . number_format($amount, 2);
    }
    
    /**
     * Truncate string to specified length
     * 
     * @param string $string String to truncate
     * @param int $length Maximum length
     * @param string $suffix Suffix to append (default: '...')
     * @return string Truncated string
     */
    public static function truncate($string, $length, $suffix = '...') {
        if (mb_strlen($string) <= $length) {
            return $string;
        }
        
        return mb_substr($string, 0, $length) . $suffix;
    }
    
    /**
     * Slugify a string (make URL-friendly)
     * 
     * @param string $string String to slugify
     * @return string Slugified string
     */
    public static function slugify($string) {
        // Convert to lowercase
        $string = strtolower($string);
        
        // Replace non-alphanumeric characters with hyphens
        $string = preg_replace('/[^a-z0-9]+/', '-', $string);
        
        // Remove leading/trailing hyphens
        $string = trim($string, '-');
        
        return $string;
    }
    
    /**
     * Upload file to specified directory
     * 
     * @param array $file $_FILES array element
     * @param string $directory Target directory
     * @param array $allowedTypes Allowed MIME types
     * @param int $maxSize Maximum file size in bytes
     * @return array ['success' => bool, 'filename' => string|null, 'error' => string|null]
     */
    public static function uploadFile($file, $directory, $allowedTypes = ALLOWED_IMAGE_TYPES, $maxSize = MAX_AVATAR_SIZE) {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'filename' => null,
                'error' => 'File upload failed. Error code: ' . $file['error']
            ];
        }
        
        // Validate file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes, true)) {
            return [
                'success' => false,
                'filename' => null,
                'error' => 'File type not allowed.'
            ];
        }
        
        // Validate file size
        if ($file['size'] > $maxSize) {
            $maxMB = round($maxSize / (1024 * 1024), 2);
            return [
                'success' => false,
                'filename' => null,
                'error' => "File size must not exceed {$maxMB}MB."
            ];
        }
        
        // Create directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('upload_', true) . '.' . $extension;
        $filepath = $directory . '/' . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'filename' => $filename,
                'error' => null
            ];
        }
        
        return [
            'success' => false,
            'filename' => null,
            'error' => 'Failed to move uploaded file.'
        ];
    }
    
    /**
     * Delete file from filesystem
     * 
     * @param string $filepath File path to delete
     * @return bool True if deleted or doesn't exist, false on error
     */
    public static function deleteFile($filepath) {
        if (file_exists($filepath)) {
            return @unlink($filepath);
        }
        return true; // File doesn't exist, consider it deleted
    }
    
    /**
     * Get file size in human-readable format
     * 
     * @param int $bytes File size in bytes
     * @param int $decimals Number of decimal places
     * @return string Formatted file size
     */
    public static function formatFileSize($bytes, $decimals = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $decimals) . ' ' . $units[$i];
    }
    
    /**
     * Redirect to specified URL
     * 
     * @param string $url URL to redirect to
     * @param int $statusCode HTTP status code (default: 302)
     * @return void
     */
    public static function redirect($url, $statusCode = 302) {
        header("Location: {$url}", true, $statusCode);
        exit();
    }
    
    /**
     * Get client IP address
     * 
     * @return string Client IP address
     */
    public static function getClientIp() {
        $ipKeys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($ipKeys as $key) {
            if (isset($_SERVER[$key]) && filter_var($_SERVER[$key], FILTER_VALIDATE_IP)) {
                return $_SERVER[$key];
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    }
    
    /**
     * Check if request is AJAX
     * 
     * @return bool True if AJAX request
     */
    public static function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Send JSON response
     * 
     * @param array $data Data to send
     * @param int $statusCode HTTP status code (default: 200)
     * @return void
     */
    public static function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    /**
     * Paginate array
     * 
     * @param array $items Items to paginate
     * @param int $page Current page number (1-based)
     * @param int $perPage Items per page
     * @return array ['data' => array, 'total' => int, 'page' => int, 'pages' => int]
     */
    public static function paginate($items, $page = 1, $perPage = ITEMS_PER_PAGE) {
        $total = count($items);
        $pages = ceil($total / $perPage);
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;
        
        $data = array_slice($items, $offset, $perPage);
        
        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'pages' => $pages,
            'per_page' => $perPage,
            'has_prev' => $page > 1,
            'has_next' => $page < $pages
        ];
    }
    
    /**
     * Generate pagination HTML
     * 
     * @param int $currentPage Current page number
     * @param int $totalPages Total number of pages
     * @param string $baseUrl Base URL for pagination links
     * @return string Pagination HTML
     */
    public static function paginationLinks($currentPage, $totalPages, $baseUrl) {
        if ($totalPages <= 1) {
            return '';
        }
        
        $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
        
        // Previous button
        $prevDisabled = $currentPage <= 1 ? 'disabled' : '';
        $prevPage = max(1, $currentPage - 1);
        $html .= "<li class=\"page-item {$prevDisabled}\">";
        $html .= "<a class=\"page-link\" href=\"{$baseUrl}?page={$prevPage}\" aria-label=\"Previous\">";
        $html .= '<span aria-hidden="true">&laquo;</span></a></li>';
        
        // Page numbers
        $range = 2; // Show 2 pages before and after current
        $start = max(1, $currentPage - $range);
        $end = min($totalPages, $currentPage + $range);
        
        if ($start > 1) {
            $html .= "<li class=\"page-item\"><a class=\"page-link\" href=\"{$baseUrl}?page=1\">1</a></li>";
            if ($start > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        for ($i = $start; $i <= $end; $i++) {
            $active = $i === $currentPage ? 'active' : '';
            $html .= "<li class=\"page-item {$active}\">";
            $html .= "<a class=\"page-link\" href=\"{$baseUrl}?page={$i}\">{$i}</a></li>";
        }
        
        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $html .= "<li class=\"page-item\"><a class=\"page-link\" href=\"{$baseUrl}?page={$totalPages}\">{$totalPages}</a></li>";
        }
        
        // Next button
        $nextDisabled = $currentPage >= $totalPages ? 'disabled' : '';
        $nextPage = min($totalPages, $currentPage + 1);
        $html .= "<li class=\"page-item {$nextDisabled}\">";
        $html .= "<a class=\"page-link\" href=\"{$baseUrl}?page={$nextPage}\" aria-label=\"Next\">";
        $html .= '<span aria-hidden="true">&raquo;</span></a></li>';
        
        $html .= '</ul></nav>';
        
        return $html;
    }
    
    /**
     * Calculate percentage
     * 
     * @param float $part Part value
     * @param float $total Total value
     * @param int $decimals Number of decimal places
     * @return float Percentage
     */
    public static function percentage($part, $total, $decimals = 2) {
        if ($total == 0) {
            return 0;
        }
        
        return round(($part / $total) * 100, $decimals);
    }
    
    /**
     * Array of GET parameters except specified keys
     * 
     * @param array $except Keys to exclude
     * @return array Filtered GET parameters
     */
    public static function getExcept($except = []) {
        return array_diff_key($_GET, array_flip($except));
    }
    
    /**
     * Build query string from array
     * 
     * @param array $params Parameters array
     * @return string Query string
     */
    public static function buildQueryString($params) {
        return http_build_query($params);
    }
}
