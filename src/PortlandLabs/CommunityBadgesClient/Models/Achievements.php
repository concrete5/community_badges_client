<?php

/**
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace PortlandLabs\CommunityBadgesClient\Models;

use Concrete\Core\User\User;
use PortlandLabs\CommunityBadgesClient\ApiClient;
use PortlandLabs\CommunityBadgesClient\Exceptions\CommunicatorError;
use PortlandLabs\CommunityBadgesClient\Exceptions\InvalidConfiguration;

class Achievements
{
    protected $client;
    protected $user;

    public function __construct(
        User $user,
        ApiClient $client
    )
    {
        $this->client = $client;
        $this->user = $user;
    }

    public function getList()
    {
        try {
            $response = $this->client->doRequest("/api/v1/community_badges", [], "GET");
            return $response;
        } catch (CommunicatorError $e) {
            return $e;
        } catch (InvalidConfiguration $e) {
            return $e;
        }
    }

    public function assign(
        string $handle
    ): bool
    {
        $payload = [
            "user" => [
                "email" => $this->user->getUserInfoObject()->getUserEmail(),
            ],
            "achievement" => [
                "handle" => $handle
            ]
        ];

        try {
            $response = $this->client->doRequest("/api/v1/achievements/assign", $payload);

            if (isset($response["error"]) && (int)$response["error"] === 1) {
                return false;
            } else {
                return true;
            }
        } catch (CommunicatorError $e) {
            return false;
        } catch (InvalidConfiguration $e) {
            return false;
        }
    }
}