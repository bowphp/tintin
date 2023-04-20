<?php

namespace Tintin\Lexique;

trait CompileVerbatim
{
    /**
     * Compile %verbatim directive
     *
     * @param string $expression
     * @return string
     */
    public function compileVerbatim(string $expression): string
    {
        $regex = "/(%verbatim(.*?)%endverbatim)/s";

        preg_match_all($regex, $expression, $matches);
        array_shift($matches);

        if (count($matches) == 0) {
            return $expression;
        }

        // Spread the macthes values
        [$raw_expression, $accumulators] = $matches;

        foreach ($accumulators as $key => $accumulator) {
            $index = count($this->verbatim_accumulator);
            $verbatim = $this->generateVerbatimPlaceholder($index + 1);
            $expression = str_replace($raw_expression[$key], $verbatim, $expression);
            $this->verbatim_accumulator[] = $accumulator;
        }

        return $expression;
    }
}
