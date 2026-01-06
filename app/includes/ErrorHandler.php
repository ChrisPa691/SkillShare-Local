<?php
/**
 * Error Handler Class
 * 
 * Centralized error handling and logging for the application.
 * Provides consistent error responses and logging mechanisms.
 * 
 * @package SkillShareLocal
 * @version 1.0.0
 */

require_once __DIR__ . '/../config/constants.php';

class ErrorHandler {
    
    /**
     * Error log file path
     * @var string
     */
    private static $logFile = null;
    
    /**
     * Whether to display detailed errors (set false in production)
     * @var bool
     */
    private static $displayErrors = true;
    
    /**
     * Initialize error handler
     * 
     * @param string|null $logFile Custom log file path
     * @param bool $displayErrors Whether to display detailed errors
     * @return void
     */
    public static function init($logFile = null, $displayErrors = true) {
        self::$logFile = $logFile ?? __DIR__ . '/../../logs/errors.log';
        self::$displayErrors = $displayErrors;
        
        // Set custom error handler
        set_error_handler([self::class, 'handleError']);
        
        // Set custom exception handler
        set_exception_handler([self::class, 'handleException']);
        
        // Register shutdown function for fatal errors
        register_shutdown_function([self::class, 'handleShutdown']);
    }
    
    /**
     * Handle PHP errors
     * 
     * @param int $errno Error number
     * @param string $errstr Error message
     * @param string $errfile File where error occurred
     * @param int $errline Line number where error occurred
     * @return bool Always returns false to continue normal error handling
     */
    public static function handleError($errno, $errstr, $errfile, $errline) {
        // Don't handle error if error reporting is suppressed with @
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $errorType = self::getErrorType($errno);
        
        $message = sprintf(
            "[%s] %s: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            $errorType,
            $errstr,
            $errfile,
            $errline
        );
        
        self::log($message);
        
        // Display error if enabled
        if (self::$displayErrors) {
            echo "<div class='alert alert-danger'><strong>Error:</strong> {$errstr}</div>";
        }
        
        return false;
    }
    
    /**
     * Handle uncaught exceptions
     * 
     * @param Throwable $exception The exception to handle
     * @return void
     */
    public static function handleException($exception) {
        $message = sprintf(
            "[%s] Uncaught Exception: %s in %s on line %d\nStack trace:\n%s",
            date('Y-m-d H:i:s'),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
        
        self::log($message);
        
        // Display user-friendly error page
        if (self::$displayErrors) {
            self::displayExceptionPage($exception);
        } else {
            self::displayGenericErrorPage();
        }
    }
    
    /**
     * Handle fatal errors on shutdown
     * 
     * @return void
     */
    public static function handleShutdown() {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $message = sprintf(
                "[%s] Fatal Error: %s in %s on line %d",
                date('Y-m-d H:i:s'),
                $error['message'],
                $error['file'],
                $error['line']
            );
            
            self::log($message);
            
            if (self::$displayErrors) {
                echo "<div class='alert alert-danger'><strong>Fatal Error:</strong> {$error['message']}</div>";
            }
        }
    }
    
    /**
     * Log error message to file
     * 
     * @param string $message Error message
     * @return void
     */
    private static function log($message) {
        // Create log directory if it doesn't exist
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Write to log file
        error_log($message . "\n", 3, self::$logFile);
        
        // Also log to PHP error log
        error_log($message);
    }
    
    /**
     * Get error type name from error number
     * 
     * @param int $errno Error number
     * @return string Error type name
     */
    private static function getErrorType($errno) {
        $errorTypes = [
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Notice',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated'
        ];
        
        return $errorTypes[$errno] ?? 'Unknown Error';
    }
    
    /**
     * Display detailed exception page (development)
     * 
     * @param Throwable $exception Exception to display
     * @return void
     */
    private static function displayExceptionPage($exception) {
        http_response_code(500);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Application Error</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
                .error-container { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                h1 { color: #d32f2f; }
                .error-message { background: #ffebee; padding: 15px; border-left: 4px solid #d32f2f; margin: 20px 0; }
                .stack-trace { background: #263238; color: #aed581; padding: 15px; overflow-x: auto; font-family: monospace; font-size: 12px; }
                .error-details { margin: 20px 0; }
                .error-details dt { font-weight: bold; margin-top: 10px; }
                .error-details dd { margin-left: 20px; }
            </style>
        </head>
        <body>
            <div class="error-container">
                <h1>⚠ Application Error</h1>
                <div class="error-message">
                    <strong>Error:</strong> <?= htmlspecialchars($exception->getMessage()) ?>
                </div>
                <dl class="error-details">
                    <dt>Type:</dt>
                    <dd><?= get_class($exception) ?></dd>
                    <dt>File:</dt>
                    <dd><?= htmlspecialchars($exception->getFile()) ?></dd>
                    <dt>Line:</dt>
                    <dd><?= $exception->getLine() ?></dd>
                </dl>
                <h3>Stack Trace:</h3>
                <pre class="stack-trace"><?= htmlspecialchars($exception->getTraceAsString()) ?></pre>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
    
    /**
     * Display generic error page (production)
     * 
     * @return void
     */
    private static function displayGenericErrorPage() {
        http_response_code(500);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error</title>
            <style>
                body { font-family: Arial, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #f5f5f5; }
                .error-box { text-align: center; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 20px rgba(0,0,0,0.1); }
                h1 { color: #d32f2f; font-size: 48px; margin: 0; }
                p { color: #666; font-size: 18px; }
                a { color: #1976d2; text-decoration: none; }
            </style>
        </head>
        <body>
            <div class="error-box">
                <h1>Oops!</h1>
                <p>Something went wrong. Please try again later.</p>
                <p><a href="/SkillShare-Local/public/index.php">← Return to Home</a></p>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
    
    /**
     * Log database error
     * 
     * @param PDOException $exception PDO exception
     * @param string $context Additional context information
     * @return void
     */
    public static function logDatabaseError($exception, $context = '') {
        $message = sprintf(
            "[%s] Database Error%s: %s\nSQL State: %s",
            date('Y-m-d H:i:s'),
            $context ? " ({$context})" : '',
            $exception->getMessage(),
            $exception->getCode()
        );
        
        self::log($message);
    }
    
    /**
     * Log custom message
     * 
     * @param string $message Message to log
     * @param string $level Log level (INFO, WARNING, ERROR)
     * @return void
     */
    public static function logMessage($message, $level = 'INFO') {
        $formattedMessage = sprintf(
            "[%s] [%s] %s",
            date('Y-m-d H:i:s'),
            $level,
            $message
        );
        
        self::log($formattedMessage);
    }
    
    /**
     * Handle AJAX errors
     * 
     * @param string $message Error message
     * @param int $code HTTP status code
     * @return void (exits)
     */
    public static function ajaxError($message, $code = 400) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $message
        ]);
        exit;
    }
    
    /**
     * Handle validation errors
     * 
     * @param array $errors Validation errors
     * @return void (exits)
     */
    public static function validationError($errors) {
        http_response_code(422);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
        exit;
    }
    
    /**
     * Log user action
     * 
     * @param int $userId User ID
     * @param string $action Action performed
     * @param array $details Additional details
     * @return void
     */
    public static function logUserAction($userId, $action, $details = []) {
        $message = sprintf(
            "[%s] User Action - User ID: %d, Action: %s, Details: %s",
            date('Y-m-d H:i:s'),
            $userId,
            $action,
            json_encode($details)
        );
        
        self::log($message);
    }
}
