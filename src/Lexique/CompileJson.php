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
            '/^%json\s*\((.*)\)$/',
            function ($match) {
                array_shift($match);

                $parts = explode(',', $match[0]);

                if (! isset($parts[1])) {
                    return  "<?php echo json_encode($parts[0]); ?>";
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
