<?php

/*
 * This file is part of the Behat Testwork.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Testwork\PathOptions\Printer;

use Symfony\Component\Console\Formatter\OutputFormatter;

final class ConfigurablePathPrinter
{
    private string $basePath;

    public function __construct(
        string $basePath,
        private bool $printAbsolutePaths,
        private ?string $editorUrl = null,
    ) {
        $realBasePath = realpath($basePath);

        if ($realBasePath) {
            $basePath = $realBasePath;
        }

        $this->basePath = $basePath;
    }

    public function setPrintAbsolutePaths(bool $printAbsolutePaths): void
    {
        $this->printAbsolutePaths = $printAbsolutePaths;
    }

    public function setEditorUrl(?string $editorUrl): void
    {
        $this->editorUrl = $editorUrl;
    }

    /**
     * Conditionally transforms paths to relative and adds editor links if configured.
     */
    public function processPathsInText(string $text): string
    {
        // If no editor URL is set, use the original behavior
        if ($this->editorUrl === null) {
            if ($this->printAbsolutePaths === true) {
                return $text;
            }

            return str_replace($this->basePath . DIRECTORY_SEPARATOR, '', $text);
        }

        // Search for paths in the text
        $basePathPattern = preg_quote($this->basePath . DIRECTORY_SEPARATOR, '/');
        $pattern = '/(' . $basePathPattern . '[^:\s]+)((:|\s+line\s+)(\d+))?/';

        return preg_replace_callback($pattern, function (array $matches): string {
            $filePath = $matches[1];
            $line = $matches[4] ?? null;

            // Calculate absolute and relative paths
            $absPath = $filePath;
            $relPath = str_replace($this->basePath . DIRECTORY_SEPARATOR, '', $filePath);

            // Format the path according to printAbsolutePaths setting
            $displayPath = $this->printAbsolutePaths ? $absPath : $relPath;

            // If no line number is present, use empty string
            if ($line === null) {
                $line = '';
            }

            // Replace placeholders in the editor URL
            $editorUrl = str_replace(
                ['{absPath}', '{relPath}', '{line}'],
                [$absPath, $relPath, $line],
                $this->editorUrl
            );

            // Create a link with the path
            // $matches[2] represents the line number with its prefix (`:LINE` or ` line LINE`)
            return '<href=' . OutputFormatter::escape($editorUrl) . '>' . $displayPath . ($matches[2] ?? '') . '</>';
        }, $text);
    }
}
