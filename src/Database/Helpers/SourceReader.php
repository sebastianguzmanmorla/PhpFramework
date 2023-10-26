<?php

namespace PhpFramework\Database\Helpers;

use PhpToken;
use ReflectionFunction;

final class SourceReader
{
    private const OPEN_NEST_CHARS = ['(', '[', '{'];
    private const CLOSE_NEST_CHARS = [')', ']', '}'];

    public static function doesCharBeginNest($char)
    {
        return \in_array($char, self::OPEN_NEST_CHARS);
    }

    public static function doesCharEndNest($char)
    {
        return \in_array($char, self::CLOSE_NEST_CHARS);
    }

    public static function readClosure(ReflectionFunction $fn): array
    {
        $file = file($fn->getFileName());
        $tokens = PhpToken::tokenize(\implode('', $file), TOKEN_PARSE);

        $tokens = array_filter($tokens, fn ($token) => $token->line >= $fn->getStartLine() && $token->line <= $fn->getEndLine());

        $functionTokens = [];
        $functionStart = false;
        $functionCapture = false;
        $functionCaptureAny = false;
        $functionNest = [];
        $closeNest = null;

        foreach ($tokens as $i => $token) {
            if ($token->id == T_FN) {
                $functionStart = true;

                continue;
            }
            if ($functionStart && $token->id == T_DOUBLE_ARROW) {
                $functionCapture = true;

                continue;
            }
            if ($functionCapture) {
                if (!$functionCaptureAny && $token->id == T_WHITESPACE) {
                    continue;
                }
                $functionCaptureAny = true;

                if (self::doesCharBeginNest($token->text)) {
                    $functionNest[] = $token;
                }
                if (self::doesCharEndNest($token->text)) {
                    if (empty($functionNest)) {
                        $closeNest = $token;
                    } else {
                        array_pop($functionNest);
                    }
                }
                if ($closeNest == null || $closeNest->pos != $token->pos) {
                    $functionTokens[] = $token;
                }
            }
        }

        while ($functionTokens[count($functionTokens) - 1]->id == T_WHITESPACE) {
            array_pop($functionTokens);
        }

        return $functionTokens;
    }
}
