<?php

namespace PhpFramework\Jwt;

use DateTime;
use PhpFramework\Router;
use ReflectionClass;
use ReflectionProperty;

class Payload
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

    public static function Decode(string $Token): ?static
    {
        $Array = Token::Decode($Token);

        if ($Array !== false) {
            $Reflection = new ReflectionClass(static::class);

            return static::RecursiveArrayToObject($Array, $Reflection);
        }

        return null;
    }

    private static function RecursiveArrayToObject(array $Array, ReflectionClass $Reflection): object
    {
        $Output = $Reflection->newInstanceWithoutConstructor();

        foreach ($Reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $Property) {
            $Value = $Array[$Property->getName()] ?? null;

            if (!$Property->getType()->isBuiltin() && $Value !== null) {
                $Type = $Property->getType()->getName();
                $Value = static::RecursiveArrayToObject($Value, new ReflectionClass($Type));
            }

            $Property->setValue($Output, $Value);
        }

        return $Output;
    }
}
