<?php

namespace Request;

use Attribute;
use Database\Framework\Usuario;
use Model\Api\Payload;
use PhpFramework\Attributes\Singleton;

#[Attribute(Attribute::TARGET_METHOD)]
class TokenAuthentication extends \PhpFramework\Jwt\TokenAuthentication
{
    #[Singleton]
    private \Database\Framework $Database;

    public function Decode(string $Token): void
    {
        static::$Payload = Payload::Decode($Token);
    }

    public static function Payload(): ?Payload
    {
        return static::$Payload;
    }

    public function Valid(): bool
    {
        if (static::$Payload === null) {
            return false;
        }

        $id_usuario = static::Payload()->user->id_usuario;

        $usuario_rs = $this->Database->Usuario
            ->Where(fn (Usuario $x) => $x->id_usuario == $id_usuario && $x->usu_estado == 1)
            ->Select();

        return !$usuario_rs->EOF();
    }
}
