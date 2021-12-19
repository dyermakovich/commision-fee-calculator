<?php

declare(strict_types=1);

namespace DY\CFC\User;

use DY\CFC\Service\IntegerParser;
use DY\CFC\Service\Parser;
use DY\CFC\Service\ParserInterface;
use DY\CFC\User\Exception\WrongUserIDException;
use DY\CFC\User\Exception\WrongUserTypeException;
use Exception;
use DY\CFC\User\Exception\UserFoundException;
use JetBrains\PhpStorm\Pure;

class UserService implements UserServiceInterface
{
    public function __construct(private ParserInterface $parser)
    {
    }

    public static function create(): UserServiceInterface
    {
        return new UserService(Parser::create());
    }

    /**
     * @var UserInterface[]
     */
    protected array $users = [];

    public function findByID(string $id): ?UserInterface
    {
        $parsedId = $this->parser->parseInteger($id, 0);
        return $this->users[$parsedId] ?? null;
    }

    /**
     * @throws UserFoundException
     * @throws WrongUserIDException
     * @throws WrongUserTypeException
     */
    public function addNew(string $id, string $type): UserInterface
    {
        $user = $this->findByID($id);

        if (isset($user)) {
            throw new UserFoundException();
        }

        $parsedId = $this->parser->parseInteger($id, 0);
        $user = new User($parsedId, $type);
        return $this->users[$user->getID()] = $user;
    }
}
