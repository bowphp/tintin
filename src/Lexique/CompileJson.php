<?php

namespace Tintin\Lexique;

trait CompileJson
{
    /**
     * Compile the %json directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileJson(string $expression): string
    {
        $output = preg_replace_callback(
            '/^%json\s*\((.*)\)$/sm',
            function ($match) {
                array_shift($match);

                $parts = explode(',', $match[0]);

                if (! isset($parts[1])) {
                    // Escape HTML-significant characters by default so values
                    // rendered inside <script> cannot break out of the context
                    // (XSS). Explicit options passed by the caller are honored.
                    $flags = 'JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP';

                    return  "<?php echo json_encode($parts[0], $flags); ?>";
                }

                $options = trim($parts[1]);

                $depth = isset($parts[2]) ? trim($parts[2]) : 512;

                return  "<?php echo json_encode($parts[0], $options, $depth); ?>";
            },
            $expression
        );

        return $output == $expression ? '' : $output;
    }
}
