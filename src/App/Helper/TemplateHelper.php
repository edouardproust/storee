<?php 

namespace App\App\Helper;

class TemplateHelper
{

    /**
     * Get an extract of a text (trim a string and format it)
     * @param string $text The text to trim
     * @param int $maxChars How many characters long the extract should be?
     * @param string|null $delimiter The delimiter that ends the extract if the text is trimmed. Default: '...'
     * @return string The extract
     */
    public static function extract(string $text, int $maxChars, ?string $delimiter = '...'): string
    {
        if (strlen($text) > $maxChars) {
            preg_match('/(.{' . $maxChars . '}.*?)\b/', $text, $matches);
            return rtrim($matches[1]) . $delimiter;
        }
        else {
            return $text;
        }
    }

}