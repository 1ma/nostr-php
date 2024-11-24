<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use swentel\nostr\Event\Event;
use swentel\nostr\Key\Key;
use swentel\nostr\Message\EventMessage;
use swentel\nostr\Relay\Relay;
use swentel\nostr\Request\Request;
use swentel\nostr\Sign\Sign;

try {
    $note = new Event();
    $note->setKind(1);
    $note->addTag(['t', 'introduction']);
    $note->addTag(['r', 'wss://relay.nostr.band']);
    $content = 'Hello Nostr world!';
    $note->setContent($content);
    // Sign event.
    $private_key = new Key();
    $private_key = $private_key->generatePrivateKey();
    $signer = new Sign();
    $signer->signEvent($note, $private_key);
    // Optional, verify event.
    $isValid = $note->verify();
    // Transmit the event to a relay.
    $relay = new Relay('wss://relay.nostr.band');
    $eventMessage = new EventMessage($note);
    $relay->setMessage($eventMessage);
    $request = new Request($relay, $eventMessage);
    $response = $request->send();
    // Handle response.
    foreach ($response as $relayUrl => $relayResponses) {
        foreach ($relayResponses as $relayResponse) {
            if ($relayResponse->isSuccess) {
                print 'The event has been transmitted to the relay ' . $relayUrl . PHP_EOL;
                $eventId = $relayResponse->eventId;
                print 'The received event id from the relay: ' . $relayResponse->eventId;
                // Now we could request the event with this id.
            }
        }
    }
} catch (Exception $e) {
    print 'Exception error: ' . $e->getMessage() . PHP_EOL;
}