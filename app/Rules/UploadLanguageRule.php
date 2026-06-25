<?php

namespace App\Rules;

use App\Rules\Base\AbstractUploadedFileRule;
use Illuminate\Http\UploadedFile;

class UploadLanguageRule extends AbstractUploadedFileRule
{

    /**
     * Determine if the uploaded language JSON file has required metadata.
     *
     * @param string $attribute
     * @param UploadedFile $value
     * @return bool
     */
    public function passes(string $attribute, UploadedFile $value): bool
    {
        $path = $value->getRealPath();

        if (!is_string($path)) {
            return false;
        }

        $uploadedFile = file_get_contents($path);

        if ($uploadedFile === false) {
            return false;
        }

        $file = json_decode($uploadedFile);

        if (!is_object($file)) {
            return false;
        }

        return isset($file->lang_code, $file->lang_name, $file->lang_dir);
    }

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return __('Invalid language file.');
    }
}
