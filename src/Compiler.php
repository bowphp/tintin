<?php

namespace Tintin;

class Compiler
{
    use Lexique\CompileIf,
        Lexique\CompileLoop,
        Lexique\CompileEchos,
        Lexique\CompileRawPhp,
        Lexique\CompileComments,
        Lexique\CompileCustomDirective,
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
        'CustomDirective'
    ];

    /**
     * @var string
     */
    protected $result = '';

    /**
     * @var string
     */
    protected $conditionPatern = '/(%s\s*\((.+?)?\)$)+/sm';

    /**
     * @var array
     */
    protected $footer = [];

    /**
     * The custom directive collector
     *
     * @var array
     */
    private $directives = [];

    /**
     * Liste of default directive
     *
     * @var array
     */
    private $directivesProtected = [
        'if',
        'else',
        'elseif',
        'elif',
        'endif',
        'unless',
        'extends',
        'block',
        'inject',
        'include',
        'endblock',
        'while',
        'endwhile',
        'for',
        'endfor',
        'loop',
        'endloop',
        'stop',
        'jump',
    ];

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
            if (strlen($value) > 0) {
                $value = $this->compileToken($value);

                $this->result .= strlen($value) == 0 || $value == ' ' ? trim($value) : $value."\n";
            }
        }

        return $this->resetCompilatorAccumulator();
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
    private function resetCompilatorAccumulator()
    {
        $result = $this->result.implode("\n", $this->footer);

        $this->result = '';

        $this->footer = [];

        return $result;
    }

    /**
     * Push more directive in template system
     *
     * @param string $name
     * @param callable $handler
     * @return mixed
     */
    public function pushDirective($name, $handler)
    {
        if (in_array($name, $this->directivesProtected)) {
            throw new \Tintin\Exception\DirectiveNotAllowException('The ' . $name . ' directive is not allow.');
        }

        $this->directives[$name] = $handler;
    }

    /**
     * Execute custom directory
     *
     * @param callable $handler
     * @param array $param
     * @return mixed
     */
    public function _____executeCustomDirectory($name, ...$params)
    {
        return call_user_func_array($this->directives[$name], [$params]);
    }
}
