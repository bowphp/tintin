<?php

namespace Tintin;

class Compiler
{
    use Lexique\CompileIf,
        Lexique\CompileLoop,
        Lexique\CompileEchos,
        Lexique\CompileRawPhp,
        Lexique\CompileComments,
        Lexique\CompileExtends;

    /**
     * @var array
     */
    protected $echoTags = ['{{', '}}'];

    /**
     * @var array
     */
    protected $rawEchoTags = ['{{{', '}}}'];

    /**
     * @var array
     */
    protected $comments = ['{#', '#}'];

    /**
     * @var array
     */
    protected $tokens = [
        'Comments',
        'RawPhp',
        'EchoStack',
        'IfStack',
        'LoopStack',
        'ExtendsStack',
    ];

    /**
     * @var string
     */
    protected $result = '';

    /**
     * @var string
     */
    protected $conditionPatern = '/(^%s\s*\((.+?)?\)$)+/sm';

    /**
     * @var array
     */
    protected $footer = [];

    /**
     * Launch the compilation
     *
     * @param array|string $data
     * @return string
     */
    public function complie($data)
    {
        $data = preg_split('/\n|\r\n/', $data);

        foreach ($data as $value) {
            $this->result .= $this->compileToken($value)."\n";
        }

        return $this->resetCompilationAccumulator();
    }

    /**
     * Compile All define token
     *
     * @param string $value
     * @return string
     */
    private function compileToken($value)
    {
        foreach ($this->tokens as $token) {
            $out = $this->{'compile'.$token}($value);

            if (strlen($out) !== 0) {
                $value = $out;
            }
        }

        return $value;
    }

    /**
     * Reset Compilation accumulatior
     *
     * @return string
     */
    private function resetCompilationAccumulator()
    {
        $result = $this->result;

        $this->result = '';

        return $result.implode("\n", $this->footer);
    }
}
