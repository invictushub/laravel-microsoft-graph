<?php

namespace Invictushub\MsGraph\AdminResources;

use Invictushub\MsGraph\Facades\MsGraphAdmin;
use Exception;

class CalendarEvents extends MsGraphAdmin
{
    private string $userId = '';

    private string $top = '';

    private string $skip = '';

    public function userid(string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function top(string $top): static
    {
        $this->top = $top;

        return $this;
    }

    public function skip(string $skip): static
    {
        $this->skip = $skip;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function get(string $calendarId, array $params = []): array
    {
        if ($this->userId === '') {
            throw new Exception('userId is required.');
        }

        $top = request('top', $this->top);
        $skip = request('skip', $this->skip);

        if ($params == []) {
            $params = http_build_query([
                '$orderby' => 'subject',
                '$top' => $top,
                '$skip' => $skip,
                '$count' => 'true',
            ]);
        } else {
            $params = http_build_query($params);
        }

        $events = MsGraphAdmin::get("users/$this->userId/calendars/$calendarId/events?$params");
        $data = MsGraphAdmin::getPagination($events, $top, $skip);

        return [
            'events' => $events,
            'total' => $data['total'],
            'top' => $data['top'],
            'skip' => $data['skip'],
        ];
    }

    /**
     * @throws Exception
     */
    public function find(string $calendarId, string $eventId): MsGraphAdmin
    {
        if ($this->userId === '') {
            throw new Exception('userId is required.');
        }

        return MsGraphAdmin::get("users/$this->userId/calendars/$calendarId/events/$eventId");
    }

    /**
     * @throws Exception
     */
    public function store(string $calendarId, array$data): MsGraphAdmin
    {
        if ($this->userId === '') {
            throw new Exception('userId is required.');
        }

        return MsGraphAdmin::post("users/$this->userId/calendars/$calendarId/events", $data);
    }
}
