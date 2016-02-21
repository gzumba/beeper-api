# beeper-api

This is a PHP client for [beeper.io](http://beeper.io) API.

## Usage

### Sending beeps

    use GuzzleHttp\Client;
    use Zumba\Beeper\Client\BeeperClient;

    $guzzle = new Client(['base_uri' => 'https://api.beeper.io/api/']);
    
    $beeper = new BeeperClient($guzzle, 'my app id', 'my api key');
    $beeper->setDefaultSenderId('my sender id');
    
    $beeper->sendText('phone number', 'Hello!');
    
### Handling responses

The Beeper mobile App allows one to answer the dialogs. Beeper.io sends these to a webhook endpoint.


    use Zumba\Beeper\Server\BeeperEventHandlerI;
    use Zumba\Beeper\Server\CallbackHandler;
    use Zumba\Beeper\Server\EventFactory;
    
    class MyEventHandler implements BeeperEventHandlerI
    {
      <implement the interface>
    }

    $event_factory = new EventFactory();
    $my_event_handler = new MyEventHandler();
    
    $callback_handler = new CallbackHandler($event_factory, $my_event_handler);
    
    $data = $_POST;
    
    $callback_handler->handle($data);