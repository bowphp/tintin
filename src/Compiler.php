<?php
namespace Tintin;

class Compiler
{
    use Lexique\CompileIf,
        Lexique\CompileLoop,
        Lexique\CompileEchos,
        Lexique\CompileRawPhp,
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
    protected $tokens = [
        'RawPhp',
        'RawEcho',
        'Echo',
        'If',
        'ElseIf',
        'Else',
        'Include',
        'Foreach',
        'EndForeach',
        'EndIf',
        'ContinueIf',
        'Continue',
        'BreakIf',
        'Break',
        'Block'
    ];

    /**
     * @var string
     */
    protected $result = '';

    /**
     * @var string
     */
    protected $conditionPatern = '/(%s *(.+?):)+/';

    /**
     * Permet de
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

        foreach ($data as $value) {
            foreach ($this->tokens as $token) {
                $out = $this->{'compile'.$token}($value);
                if (strlen($out) !== 0) {
                    $value = $out;
                }
            }
            $this->result .= $value;
        }
        return $this->result;
    }
}