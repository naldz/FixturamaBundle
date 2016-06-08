<?php

namespace Naldz\Bundle\FixturamaBundle\Fixturama;
use Naldz\Bundle\FixturamaBundle\Fixturama\Exception\InvalidDsnException;

class DsnParser
{
    public function parse($dsn)
    {
        $urlComponents = parse_url($dsn);

        // $dsnComponents = array(
        //     'database_type' => $urlComponents['scheme'],
        //     'database_user' => $urlComponents['user'],
        //     'database_password' => $urlComponents['password'],
        //     ''
        // );

        $dsnComponents = array();
        if (!isset($urlComponents['scheme'])) {
            throw new InvalidDsnException(sprintf('No database type detected while parsing DSN: %s', $dsn));
        }
        else {
            $dsnComponents = $urlComponents['scheme'];
        }

    }
}