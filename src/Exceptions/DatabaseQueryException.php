<?php
/**
 * DatabaseQueryException.php
 *
 * @package   wp-migrations
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\Migrations\Exceptions;

class DatabaseQueryException extends \Exception
{

    private $queryErrors;

    public static function create($queryErrors, string $message = null): self
    {
        $error = new self();

        $error->message     = $message ? : 'Database query failure';
        $error->queryErrors = (array) $queryErrors;

        return $error;
    }

    public function getQueryErrors(): array
    {
        return $this->queryErrors;
    }

    public function getLogOutput(): string
    {
        $queryErrors = array_map(
            function ($error) {
                return ' - '.$error;
            },
            $this->queryErrors
        );

        return "
            Code: {$this->getCode()}\n
            Message: {$this->getMessage()}\n
            DB Errors: \n
            {$queryErrors}
        ";
    }

}
