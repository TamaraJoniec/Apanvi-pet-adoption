<?php
// Upload configuration
define('UPLOAD_PATH', dirname(__DIR__) . '/uploads/pets/');
define('UPLOAD_URL', '/uploads/pets/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);
define('ALLOWED_MIME_TYPES', [
    'image/jpeg',
    'image/png',
    'image/webp'
]);

/**
 * Handles file upload with security checks
 * 
 * @param array $file The uploaded file array from $_FILES
 * @param string $newFilename Optional new filename (without extension)
 * @return array ['success' => bool, 'message' => string, 'filename' => string]
 */
function handleFileUpload($file, $newFilename = null) {
    $result = [
        'success' => false,
        'message' => '',
        'filename' => ''
    ];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['message'] = getUploadErrorMessage($file['error']);
        return $result;
    }

    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        $result['message'] = 'File is too large. Maximum size is ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB';
        return $result;
    }

    // Get file extension and check if it's allowed
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        $result['message'] = 'Invalid file type. Allowed types: ' . implode(', ', ALLOWED_EXTENSIONS);
        return $result;
    }

    // Verify MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, ALLOWED_MIME_TYPES)) {
        $result['message'] = 'Invalid file type detected';
        return $result;
    }

    // Generate unique filename if not provided
    if ($newFilename === null) {
        $newFilename = uniqid('pet_', true);
    }
    $filename = $newFilename . '.' . $extension;
    $destination = UPLOAD_PATH . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $result['message'] = 'Failed to move uploaded file';
        return $result;
    }

    // Set proper permissions
    chmod($destination, 0644);

    $result['success'] = true;
    $result['message'] = 'File uploaded successfully';
    $result['filename'] = $filename;

    return $result;
}

/**
 * Delete an uploaded file
 * 
 * @param string $filename The filename to delete
 * @return bool True if deleted successfully, false otherwise
 */
function deleteUploadedFile($filename) {
    $filepath = UPLOAD_PATH . $filename;
    
    // Verify the file is within the uploads directory
    if (!str_starts_with(realpath($filepath), realpath(UPLOAD_PATH))) {
        return false;
    }
    
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    
    return false;
}

/**
 * Get the URL for a pet's image
 * 
 * @param string|null $filename The image filename
 * @param bool $absolute Whether to return an absolute URL
 * @return string The URL to the image or default image if not found
 */
function getPetImageUrl($filename = null, $absolute = false) {
    if (empty($filename) || !file_exists(UPLOAD_PATH . $filename)) {
        $filename = 'default-pet.jpg';
    }
    
    $url = UPLOAD_URL . $filename;
    if ($absolute) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $url = $protocol . $_SERVER['HTTP_HOST'] . $url;
    }
    
    return $url;
}

/**
 * Get human-readable upload error message
 * 
 * @param int $errorCode PHP upload error code
 * @return string Human-readable error message
 */
function getUploadErrorMessage($errorCode) {
    switch ($errorCode) {
        case UPLOAD_ERR_INI_SIZE:
            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
        case UPLOAD_ERR_FORM_SIZE:
            return 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form';
        case UPLOAD_ERR_PARTIAL:
            return 'The uploaded file was only partially uploaded';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing a temporary folder';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk';
        case UPLOAD_ERR_EXTENSION:
            return 'A PHP extension stopped the file upload';
        default:
            return 'Unknown upload error';
    }
} 