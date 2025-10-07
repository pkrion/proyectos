<?php

namespace FacturaScripts\Plugins\MoProjects\Service;

use DateInterval;
use DateTimeImmutable;
use Exception;
use FacturaScripts\Core\Tools;
use FacturaScripts\Core\Where;
use FacturaScripts\Plugins\MoProjects\Model\MoProject;
use FacturaScripts\Plugins\MoProjects\Model\MoProjectEvent;
class MoProjectCalendarService
{
    private static ?self $instance = null;
    private ?object $client = null;

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function configure(array $settings): void
    {
        if (!class_exists('Google\\Client')) {
            throw new Exception('Google API client library is required for calendar integration.');
        }

        $client = new \Google\Client();
        $client->setApplicationName($settings['applicationName'] ?? 'FacturaScripts Mo Projects');
        $client->setAuthConfig($settings['credentialsPath'] ?? '');
        $client->setScopes([\Google\Service\Calendar::CALENDAR]);
        $client->setAccessType('offline');

        if (!empty($settings['accessToken'])) {
            $client->setAccessToken($settings['accessToken']);
        }

        $this->client = $client;
    }

    public function isEnabled(): bool
    {
        return $this->client !== null;
    }

    public function syncProject(MoProject $project): void
    {
        if (!$this->isEnabled() || empty($project->calendar_id)) {
            return;
        }

        $service = new \Google\Service\Calendar($this->client);
        $events = MoProjectEvent::all([Where::eq('idproject', $project->id)]);

        foreach ($events as $event) {
            $googleEvent = $this->buildGoogleEvent($event);
            if (empty($event->calendar_event_id)) {
                $googleEvent = $service->events->insert($project->calendar_id, $googleEvent);
                $event->calendar_event_id = $googleEvent->getId();
            } else {
                $service->events->update($project->calendar_id, $event->calendar_event_id, $googleEvent);
            }

            $event->synced = true;
            $event->save();
        }
    }

    public function createEvent(MoProject $project, array $data): ?MoProjectEvent
    {
        $start = new DateTimeImmutable($data['start_at'] ?? 'now');
        $end = empty($data['end_at']) ? $start->add(new DateInterval('PT1H')) : new DateTimeImmutable($data['end_at']);

        $event = new MoProjectEvent([
            'idproject' => $project->id,
            'title' => $data['title'] ?? $project->name,
            'start_at' => $start->format('Y-m-d H:i:s'),
            'end_at' => $end->format('Y-m-d H:i:s'),
            'location' => $data['location'] ?? '',
            'notes' => $data['notes'] ?? '',
        ]);

        if (false === $event->save()) {
            return null;
        }

        if ($this->isEnabled() && !empty($project->calendar_id)) {
            try {
                $service = new \Google\Service\Calendar($this->client);
                $googleEvent = $service->events->insert($project->calendar_id, $this->buildGoogleEvent($event));
                $event->calendar_event_id = $googleEvent->getId();
                $event->synced = true;
                $event->save();
            } catch (Exception $exception) {
                Tools::log()->warning('mo-projects-calendar-sync-error', [$exception->getMessage()]);
            }
        }

        return $event;
    }

    protected function buildGoogleEvent(MoProjectEvent $event)
    {
        $googleEvent = new \Google\Service\Calendar\Event([
            'summary' => $event->title,
            'location' => $event->location,
            'description' => $event->notes,
        ]);

        $start = new \Google\Service\Calendar\EventDateTime();
        $start->setDateTime($event->start_at);
        $start->setTimeZone(date_default_timezone_get());

        $end = new \Google\Service\Calendar\EventDateTime();
        $end->setDateTime($event->end_at ?? $event->start_at);
        $end->setTimeZone(date_default_timezone_get());

        $googleEvent->setStart($start);
        $googleEvent->setEnd($end);

        return $googleEvent;
    }
}
