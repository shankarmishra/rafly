<?php
/**
 * The one image-upload pipeline.
 *
 * This lived inline in admin/media.php until the team page needed to accept a
 * headshot directly. Copying ~80 lines of security-critical code into a second
 * page is exactly how one of the two copies later drifts, so it moved here and
 * both callers share it.
 *
 * The order of the checks is the point, and none of them is redundant:
 *
 *   1. PHP's own upload error code, so a truncated or oversized POST is named
 *      rather than silently treated as "no file".
 *   2. is_uploaded_file(), which confirms the path really came through PHP's
 *      upload handler and is not an arbitrary path a crafted request supplied.
 *   3. finfo sniffs the real content type. The browser-supplied type is a
 *      claim by the client and is never consulted.
 *   4. getimagesize() must also agree it is a real image. finfo can be
 *      satisfied by a PHP script carrying forged magic bytes; an actual image
 *      decoder cannot.
 *   5. The stored filename is GENERATED, never derived from what was uploaded.
 *      Serving a user-supplied filename is how a .php lands in a served path.
 *
 * SVG is deliberately absent from MEDIA_ALLOWED — see the long rationale at the
 * top of admin/media.php. It is the only scripting-capable format here and the
 * only one that would skip check 4.
 *
 * This function deliberately RETURNS its outcome instead of redirecting, so a
 * caller that is in the middle of a half-filled form (admin/team.php) can
 * re-render with the error attached to the field rather than throwing away
 * everything the editor typed.
 */

const MEDIA_ALLOWED = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
];

const MEDIA_MAX_BYTES = 8 * 1024 * 1024;   // 8 MB

function media_upload_dir(): string
{
    return dirname(__DIR__, 2) . '/uploads';
}

/**
 * Validates and stores one uploaded image, returning the new media row id.
 *
 * @param  mixed  $file One entry from $_FILES.
 * @param  string $alt  Alt text; may be empty.
 * @return array{id:int}|array{error:string}
 */
function media_store_upload($file, string $alt = ''): array
{
    if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['error' => match ($file['error'] ?? UPLOAD_ERR_NO_FILE) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'That file is larger than the server allows.',
            UPLOAD_ERR_NO_FILE                        => 'Choose a file to upload.',
            UPLOAD_ERR_PARTIAL                        => 'The upload did not finish. Try again.',
            default                                   => 'The upload failed.',
        }];
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        return ['error' => 'That upload could not be verified.'];
    }

    if ($file['size'] > MEDIA_MAX_BYTES) {
        return ['error' => 'Files must be under ' . (MEDIA_MAX_BYTES / 1024 / 1024) . ' MB.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = (string)$finfo->file($file['tmp_name']);

    if (!isset(MEDIA_ALLOWED[$mime])) {
        return ['error' => 'Only JPEG, PNG, GIF and WebP images are accepted. That file looks like "' . $mime . '".'];
    }

    $ext = MEDIA_ALLOWED[$mime];

    $size = @getimagesize($file['tmp_name']);
    if ($size === false) {
        return ['error' => 'That file claims to be an image but could not be decoded.'];
    }
    $width  = (int)$size[0];
    $height = (int)$size[1];

    $dir = media_upload_dir();
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        error_log('media: could not create upload directory ' . $dir);
        return ['error' => 'The upload directory could not be created.'];
    }

    // Generated, not derived. The original name is kept for display only.
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;

    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $filename)) {
        error_log('media: move_uploaded_file failed for ' . $filename);
        return ['error' => 'The file could not be saved.'];
    }

    @chmod($dir . '/' . $filename, 0644);

    $user = current_user();
    $id = insert_returning_id('
        INSERT INTO media (filename, original_name, mime, bytes, width, height, alt, uploaded_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ', [
        $filename,
        str_cut((string)$file['name'], 200),
        $mime,
        (int)$file['size'],
        $width,
        $height,
        str_cut(trim($alt), 300),
        $user['id'] ?? null,
    ]);

    audit('media.upload', 'media', $id, null,
          ['filename' => $filename, 'mime' => $mime, 'bytes' => (int)$file['size']]);

    return ['id' => $id];
}
