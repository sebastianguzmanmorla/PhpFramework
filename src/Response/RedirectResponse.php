<?php

namespace PhpFramework\Response;

use PhpFramework\Url;

class RedirectResponse extends Url implements IResponse
{
    public function Response(): ?string
    {
        header("Location: {$this->Url}");

        return null;
    }
}
