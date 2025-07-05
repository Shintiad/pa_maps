<?php

namespace App\Helpers;

class TextFormatHelper
{
    public static function formatTextWithLists($text)
    {
        if (empty($text)) {
            return '';
        }

        // Normalize line endings
        $text = str_replace("\r\n", "\n", $text);

        // Check if there's any list item pattern
        if (preg_match('/(\n|^)- /', $text)) {
            // Convert text to HTML with list
            $formatted = '';
            $lines = explode("\n", $text);
            $inList = false;

            foreach ($lines as $line) {
                $trimmedLine = trim($line);
                if (empty($trimmedLine)) {
                    if ($inList) {
                        $formatted .= "</ul>\n";
                        $inList = false;
                    }
                    $formatted .= "<br>\n";
                } elseif (preg_match('/^- (.+)$/', $trimmedLine, $matches)) {
                    if (!$inList) {
                        $formatted .= "<ul class='formatted-list'>\n";
                        $inList = true;
                    }
                    $listItem = $matches[1];
                    $formatted .= "<li>" . $listItem . "</li>\n";
                } else {
                    if ($inList) {
                        $formatted .= "</ul>\n";
                        $inList = false;
                    }
                    $formatted .= htmlspecialchars($trimmedLine) . "<br>\n";
                }
            }

            if ($inList) {
                $formatted .= "</ul>\n";
            }

            return $formatted;
        } else {
            // If no list, just use nl2br with proper escaping
            return nl2br(htmlspecialchars($text));
        }
    }
}
