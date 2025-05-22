<?php

namespace PhpFramework\Jwt;

use DateTime;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PhpFramework\Router;
use ReflectionClass;
use ReflectionProperty;
use stdClass;
use Throwable;

abstract class JwtToken
{
    /**
     * Issued at.
     */
    public ?int $iat;

    /**
     * Not before.
     */
    public ?int $nbf;

    /**
     * Expiration time.
     */
    public ?int $exp;

    private static string $Secret;

    private static string $Algorithm;

    public function __construct(
        /**
         * Subject.
         */
        public ?string $sub = null,
        /**
         * Name.
         */
        public ?string $name = null,
        /**
         * Email.
         */
        public ?string $email = null,
        /**
         * Issuer.
         */
        public ?string $iss = null,
        /**
         * Audience.
         */
        public ?string $aud = null,
        /**
         * Issued at.
         */
        ?DateTime $iat = null,
        /**
         * Not before.
         */
        ?DateTime $nbf = null,
        /**
         * Expiration time.
         */
        ?DateTime $exp = null
    ) {
        $this->iss = $iss ?? Router::BaseUrl();
        $this->aud = $aud ?? Router::BaseUrl();
        $this->iat = $iat?->getTimestamp();
        $this->nbf = $nbf?->getTimestamp();
        $this->exp = $exp?->getTimestamp();
    }

    public function __toString()
    {
        return static::Encode($this);
    }

    public static function Initialize(string $Secret, string $Algorithm = 'HS256'): void
    {
        static::$Secret = $Secret;
        static::$Algorithm = $Algorithm;
    }

    public static function Encode(object $Payload): string
    {
        return JWT::encode((array) $Payload, static::$Secret, static::$Algorithm);
    }

    public static function Decode(string $Token): static|Throwable
    {
        try {
            $Result = JWT::decode($Token, new Key(static::$Secret, static::$Algorithm));

            return static::ConvertTo($Result, static::class);
        } catch (Throwable $ex) {
            return $ex;
        }
    }

    private static function ConvertTo(stdClass $Object, string $Class): object
    {
        $Reflection = new ReflectionClass($Class);

        $Output = $Reflection->newInstanceWithoutConstructor();

        foreach ($Reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $Property) {
            $Type = $Property->getType();

            $Value = $Object->{$Property->getName()} ?? null;

            if ($Value === null && !$Type->allowsNull()) {
                throw new Exception($Property->getName() . ' not set');
            }

            if (!$Type->isBuiltin() && $Value !== null) {
                $Value = static::ConvertTo($Value, $Type->getName());
            }

            $Property->setValue($Output, $Value);
        }

        return $Output;
    }
}
