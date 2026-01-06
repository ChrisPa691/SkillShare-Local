<?php
/**
 * Input Validation Class
 * 
 * Centralized validation for all user inputs across the application.
 * Provides reusable validation methods with consistent error messages.
 * 
 * @package SkillShareLocal
 * @version 1.0.0
 */

require_once __DIR__ . '/../config/constants.php';

class Validator {
    
    /**
     * Validation errors
     * @var array
     */
    private $errors = [];
    
    /**
     * Validated data
     * @var array
     */
    private $data = [];
    
    /**
     * Constructor
     * 
     * @param array $data Data to validate
     */
    public function __construct($data = []) {
        $this->data = $data;
        $this->errors = [];
    }
    
    /**
     * Get all validation errors
     * 
     * @return array Array of error messages
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Get first error message
     * 
     * @return string|null First error message or null
     */
    public function getFirstError() {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
    
    /**
     * Check if validation passed
     * 
     * @return bool True if no errors, false otherwise
     */
    public function passes() {
        return empty($this->errors);
    }
    
    /**
     * Check if validation failed
     * 
     * @return bool True if has errors, false otherwise
     */
    public function fails() {
        return !$this->passes();
    }
    
    /**
     * Add an error message
     * 
     * @param string $field Field name
     * @param string $message Error message
     * @return void
     */
    private function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }
    
    /**
     * Validate required field
     * 
     * @param string $field Field name
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function required($field, $message = null) {
        if (!isset($this->data[$field]) || trim($this->data[$field]) === '') {
            $this->addError($field, $message ?? "The {$field} field is required.");
        }
        return $this;
    }
    
    /**
     * Validate email format
     * 
     * @param string $field Field name
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function email($field, $message = null) {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $message ?? "The {$field} must be a valid email address.");
        }
        return $this;
    }
    
    /**
     * Validate minimum length
     * 
     * @param string $field Field name
     * @param int $min Minimum length
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function minLength($field, $min, $message = null) {
        if (isset($this->data[$field]) && mb_strlen($this->data[$field]) < $min) {
            $this->addError($field, $message ?? "The {$field} must be at least {$min} characters.");
        }
        return $this;
    }
    
    /**
     * Validate maximum length
     * 
     * @param string $field Field name
     * @param int $max Maximum length
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function maxLength($field, $max, $message = null) {
        if (isset($this->data[$field]) && mb_strlen($this->data[$field]) > $max) {
            $this->addError($field, $message ?? "The {$field} must not exceed {$max} characters.");
        }
        return $this;
    }
    
    /**
     * Validate numeric value
     * 
     * @param string $field Field name
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function numeric($field, $message = null) {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->addError($field, $message ?? "The {$field} must be a number.");
        }
        return $this;
    }
    
    /**
     * Validate integer value
     * 
     * @param string $field Field name
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function integer($field, $message = null) {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_INT)) {
            $this->addError($field, $message ?? "The {$field} must be an integer.");
        }
        return $this;
    }
    
    /**
     * Validate minimum value
     * 
     * @param string $field Field name
     * @param int|float $min Minimum value
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function min($field, $min, $message = null) {
        if (isset($this->data[$field]) && $this->data[$field] < $min) {
            $this->addError($field, $message ?? "The {$field} must be at least {$min}.");
        }
        return $this;
    }
    
    /**
     * Validate maximum value
     * 
     * @param string $field Field name
     * @param int|float $max Maximum value
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function max($field, $max, $message = null) {
        if (isset($this->data[$field]) && $this->data[$field] > $max) {
            $this->addError($field, $message ?? "The {$field} must not exceed {$max}.");
        }
        return $this;
    }
    
    /**
     * Validate value is in array
     * 
     * @param string $field Field name
     * @param array $values Allowed values
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function in($field, $values, $message = null) {
        if (isset($this->data[$field]) && !in_array($this->data[$field], $values, true)) {
            $this->addError($field, $message ?? "The {$field} must be one of: " . implode(', ', $values) . ".");
        }
        return $this;
    }
    
    /**
     * Validate date format
     * 
     * @param string $field Field name
     * @param string $format Date format (default: Y-m-d)
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function date($field, $format = 'Y-m-d', $message = null) {
        if (isset($this->data[$field])) {
            $d = DateTime::createFromFormat($format, $this->data[$field]);
            if (!$d || $d->format($format) !== $this->data[$field]) {
                $this->addError($field, $message ?? "The {$field} must be a valid date in format {$format}.");
            }
        }
        return $this;
    }
    
    /**
     * Validate URL format
     * 
     * @param string $field Field name
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function url($field, $message = null) {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
            $this->addError($field, $message ?? "The {$field} must be a valid URL.");
        }
        return $this;
    }
    
    /**
     * Validate password strength
     * 
     * @param string $field Field name
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function password($field, $message = null) {
        if (isset($this->data[$field])) {
            $password = $this->data[$field];
            
            // Check minimum length
            if (strlen($password) < MIN_PASSWORD_LENGTH) {
                $this->addError($field, "Password must be at least " . MIN_PASSWORD_LENGTH . " characters.");
                return $this;
            }
            
            // Check for at least one uppercase letter
            if (!preg_match('/[A-Z]/', $password)) {
                $this->addError($field, "Password must contain at least one uppercase letter.");
            }
            
            // Check for at least one lowercase letter
            if (!preg_match('/[a-z]/', $password)) {
                $this->addError($field, "Password must contain at least one lowercase letter.");
            }
            
            // Check for at least one number
            if (!preg_match('/[0-9]/', $password)) {
                $this->addError($field, "Password must contain at least one number.");
            }
        }
        return $this;
    }
    
    /**
     * Validate file upload
     * 
     * @param string $field Field name
     * @param array $options Options: maxSize, allowedTypes
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function file($field, $options = [], $message = null) {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
            return $this; // No file uploaded, not an error unless required
        }
        
        $file = $_FILES[$field];
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->addError($field, $message ?? "File upload failed. Please try again.");
            return $this;
        }
        
        // Check file size
        if (isset($options['maxSize']) && $file['size'] > $options['maxSize']) {
            $maxMB = round($options['maxSize'] / (1024 * 1024), 2);
            $this->addError($field, "File size must not exceed {$maxMB}MB.");
        }
        
        // Check file type
        if (isset($options['allowedTypes'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $options['allowedTypes'], true)) {
                $this->addError($field, "File type not allowed. Allowed types: " . implode(', ', $options['allowedTypes']));
            }
        }
        
        return $this;
    }
    
    /**
     * Validate image file
     * 
     * @param string $field Field name
     * @param int|null $maxSize Maximum file size in bytes
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function image($field, $maxSize = null, $message = null) {
        $options = [
            'allowedTypes' => ALLOWED_IMAGE_TYPES,
            'maxSize' => $maxSize ?? MAX_AVATAR_SIZE
        ];
        
        return $this->file($field, $options, $message);
    }
    
    /**
     * Validate that field matches another field
     * 
     * @param string $field Field name
     * @param string $matchField Field to match
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function matches($field, $matchField, $message = null) {
        if (isset($this->data[$field]) && isset($this->data[$matchField])) {
            if ($this->data[$field] !== $this->data[$matchField]) {
                $this->addError($field, $message ?? "The {$field} must match {$matchField}.");
            }
        }
        return $this;
    }
    
    /**
     * Validate unique value in database
     * 
     * @param string $field Field name
     * @param string $table Database table
     * @param string $column Database column
     * @param int|null $excludeId ID to exclude from uniqueness check
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function unique($field, $table, $column, $excludeId = null, $message = null) {
        if (!isset($this->data[$field])) {
            return $this;
        }
        
        global $conn;
        
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
        $params = [$this->data[$field]];
        
        if ($excludeId !== null) {
            $sql .= " AND {$table}_id != ?";
            $params[] = $excludeId;
        }
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                $this->addError($field, $message ?? "The {$field} has already been taken.");
            }
        } catch (PDOException $e) {
            error_log("Validator unique check error: " . $e->getMessage());
            $this->addError($field, "Validation error occurred.");
        }
        
        return $this;
    }
    
    /**
     * Sanitize input data
     * 
     * @param array $data Data to sanitize
     * @return array Sanitized data
     */
    public static function sanitize($data) {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = self::sanitize($value);
            } else if (is_string($value)) {
                $sanitized[$key] = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Validate role value
     * 
     * @param string $field Field name
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function validRole($field, $message = null) {
        return $this->in($field, VALID_ROLES, $message ?? "Invalid user role.");
    }
    
    /**
     * Validate session status
     * 
     * @param string $field Field name
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function validSessionStatus($field, $message = null) {
        return $this->in($field, VALID_SESSION_STATUSES, $message ?? "Invalid session status.");
    }
    
    /**
     * Validate booking status
     * 
     * @param string $field Field name
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function validBookingStatus($field, $message = null) {
        return $this->in($field, VALID_BOOKING_STATUSES, $message ?? "Invalid booking status.");
    }
    
    /**
     * Validate location type
     * 
     * @param string $field Field name
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function validLocationType($field, $message = null) {
        return $this->in($field, VALID_LOCATION_TYPES, $message ?? "Invalid location type.");
    }
    
    /**
     * Validate fee type
     * 
     * @param string $field Field name
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function validFeeType($field, $message = null) {
        return $this->in($field, VALID_FEE_TYPES, $message ?? "Invalid fee type.");
    }
    
    /**
     * Validate rating value
     * 
     * @param string $field Field name
     * @param string|null $message Custom error message
     * @return Validator For method chaining
     */
    public function validRating($field, $message = null) {
        return $this->integer($field)
                    ->min($field, MIN_RATING)
                    ->max($field, MAX_RATING, $message ?? "Rating must be between " . MIN_RATING . " and " . MAX_RATING . ".");
    }
}
