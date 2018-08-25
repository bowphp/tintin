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
     * Launch the compilation
     *
     * @param array|string $data
     * @return string
     */
    public function complie($data)
    {
        if (is_string($data)) {
            $data = preg_split('/\n/', $data);
        } else {
            $data = (array) $data;
        }

        if (isset($data[0]) && preg_match('/#extends(.+?)\n?/', $data[0])) {
            $first = $data[0];

            unset($data[0]);

            $data[] = $first;
        }

        foreach ($data as $value) {
            foreach ($this->tokens as $token) {
                $out = $this->{'compile'.$token}(trim($value, '\n'));

                if (strlen($out) !== 0) {
                    $value = $out;
                }
            }

            $this->result .= $value."\n";
        }

        return $this->result;
    }
}
