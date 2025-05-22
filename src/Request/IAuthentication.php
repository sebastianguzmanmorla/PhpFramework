<?php

namespace PhpFramework\Request;

interface IAuthentication
{
    public function IsAuthenticated(): bool;

    public function IsAuthorized(): bool;
}
