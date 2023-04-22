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
    use Lexique\CompileClass;
    use Lexique\CompileMacro;

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
        'Json',
        'Class',
        'Import'
    ];

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
        'includeIf',
        'includeWhen',
        'endblock',
        'while',
        'endwhile',
        'for',
        'endfor',
        'loop',
        'endloop',
        'stop',
        'jump',
        'json',
        'class',
        'import',
        'hasflash',
        'endhasflash',
        'empty',
        'endempty',
        'method',
        'service',
        'trans',
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
    protected $condition_pattern = '/(%s\s*\((.*)\))\s*/sm';

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
     * The reverse inclusion using for %import
     *
     * @var array
     */
    protected array $imports_render = [];

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
                $this->result .= strlen($value) == 0 || $value == ' ' ? trim($value) . "\n" : $value . "\n";
            }
        }

        // Apply the verbatim
        $this->applyVerbatimAccumulatorContent();
        $result = $this->applyImportTemplate();

        return $result;
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

            if (in_array($token, ['Comments', 'Import']) && strlen($out) == 0) {
                return "";
            }

            if (strlen($out) !== 0) {
                $value = $out;
            }
        }

        return $value;
    }

    /**
     * Apply the importation template
     *
     * @return string
     */
    private function applyImportTemplate(): string
    {
        $result = implode("\n", $this->imports_render) . "\n" . $this->result;
        $result = trim($result);
        $result = $result . "\n" . implode("\n", $this->extends_render);

        $this->result = '';

        $this->imports_render = [];
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
    public function pushDirective(string $name, callable $handler, bool $broken = false)
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
     * @throws DirectiveNotAllowException
     */
    public function _____executeCustomDirectory(string $name, ...$params): mixed
    {
        if (!isset($this->directives[$name])) {
            throw new DirectiveNotAllowException("The %$name is not found");
        }

        $directive = $this->directives[$name];

        return call_user_func_array($directive['handler'], $params);
    }
}
