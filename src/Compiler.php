<?php

namespace Tintin;

use Tintin\Exception\DirectiveNotAllowException;

class Compiler
{
    use Lexique\CompileIf;
    use Lexique\CompileLoop;
    use Lexique\CompileEchos;
    use Lexique\CompileRawPhp;
    use Lexique\CompileComments;
    use Lexique\CompileCustomDirective;
    use Lexique\CompileExtends;
    use Lexique\CompileHelpers;
    use Lexique\CompileVerbatim;
    use Lexique\CompileJson;

    /**
     * The echo tags
     *
     * @var array
     */
    protected $echo_tags = ['{{', '}}'];

    /**
     * The raw echo tags
     *
     * @var array
     */
    protected $raw_echo_tags = ['{{{', '}}}'];

    /**
     * @var array
     */
    protected $comments = ['{##', '##}'];

    /**
     * The valid token list
     *
     * @var array
     */
    protected $tokens = [
        'Comments',
        'RawPhp',
        'EchoStack',
        'IfStack',
        'LoopStack',
        'ExtendsStack',
        'HelpersStack',
        'CustomDirective',
        'Json'
    ];

    /**
     * The compile result
     *
     * @var string
     */
    protected $result = '';

    /**
     * The expression pattern
     *
     * @var string
     */
    protected $condition_pattern = '/(%s\s*\((.+?)?\))+\s*/sm';

    /**
     * The option expression pattern
     *
     * @var string
     */
    protected $option_condition_pattern = '/(%s\s*(\((.+?)?\))?)+/sm';

    /**
     * The reverse inclusion using for %extends
     *
     * @var array
     */
    protected array $extends_render = [];

    /**
     * The %verbatim accumulator
     *
     * @var array
     */
    protected array $verbatim_accumulator = [];

    /**
     * Define the vertabim placeholder
     *
     * @var string
     */
    protected string $verbatim_placeholder = "@__tintin_verbatim__{index}__@";

    /**
     * The custom directive collector
     *
     * @var array
     */
    private array $directives = [];

    /**
     * List of default directive
     *
     * @var array
     */
    private array $directivesProtected = [
        'if',
        'else',
        'elseif',
        'elif',
        'endif',
        'verbatim',
        'endverbatim',
        'env',
        'endenv',
        'production',
        'endproduction',
        'unlang',
        'endunlang',
        'unless',
        'endunless',
        'auth',
        'endauth',
        'guest',
        'endguest',
        'isset',
        'endisset',
        'extends',
        'block',
        'inject',
        'include',
        'includeif',
        'includewhen',
        'endblock',
        'while',
        'endwhile',
        'for',
        'endfor',
        'loop',
        'endloop',
        'stop',
        'jump',
        'json'
    ];

    /**
     * Launch the compilation
     *
     * @param string $data
     * @return string
     */
    public function compile(string $data): string
    {
        $data = $this->compileVerbatim($data);
        $data = preg_split('/\n|\r\n/', $data);

        foreach ($data as $value) {
            if (strlen($value) > 0) {
                $value = $this->compileToken($value);

                $this->result .= strlen($value) == 0 || $value == ' ' ? trim($value) : $value . "\n";
            }
        }

        // Apply the verbatim
        $this->applyVerbatimAccumulatorContent();

        return $this->resetCompilationAccumulator();
    }

    /**
     * Compile All define token
     *
     * @param string $value
     * @return string
     */
    private function compileToken(string $value): string
    {
        foreach ($this->tokens as $token) {
            $out = $this->{'compile' . $token}($value);

            if ($token == 'Comments') {
                if (strlen($out) == 0) {
                    return "";
                }
            }

            if (strlen($out) !== 0) {
                $value = $out;
            }
        }

        return $value;
    }

    /**
     * Reset Compilation accumulator
     * @return string
     */
    private function resetCompilationAccumulator(): string
    {
        $result = $this->result . implode("\n", $this->extends_render);

        $this->result = '';

        $this->extends_render = [];

        return $result;
    }

    /**
     * Apply %verbatim accumulator content
     *
     * @return void
     */
    private function applyVerbatimAccumulatorContent(): void
    {
        foreach ($this->verbatim_accumulator as $index => $verbatim) {
            $placeholder = $this->generateVerbatimPlaceholder($index + 1);
            $this->result = str_replace($placeholder, $verbatim, $this->result);
        }

        $this->verbatim_accumulator = [];
    }

    /**
     * Generate the verbatim placeholder
     *
     * @param integer $index
     * @return string
     */
    private function generateVerbatimPlaceholder(int $index): string
    {
        return str_replace("{index}", $index, $this->verbatim_placeholder);
    }

    /**
     * Push more directive in template system
     *
     * @param string $name
     * @param callable $handler
     * @param boolean $broken
     * @return void
     * @throws DirectiveNotAllowException
     */
    public function pushDirective($name, $handler, $broken = false)
    {
        if (in_array($name, $this->directivesProtected)) {
            throw new DirectiveNotAllowException('The ' . $name . ' directive is not allow.');
        }

        $this->directives[$name] = compact('handler', 'broken');
    }

    /**
     * Execute custom directory
     *
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function _____executeCustomDirectory($name, ...$params): mixed
    {
        if (!isset($this->directives[$name])) {
            return null;
        }

        $directive = $this->directives[$name];

        return call_user_func_array($directive['handler'], [$params]);
    }
}
