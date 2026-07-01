import './bootstrap';
import * as PusherPushNotifications from "@pusher/push-notifications-web";

  const beamsClient = new PusherPushNotifications.Client({
    instanceId: import.meta.env.VITE_PUSHER_BEAMS_INSTANCE_ID,
  });

  beamsClient.start()
    .then(() => beamsClient.addDeviceInterest('hello'))
    .then(() => console.log('Successfully registered and subscribed!'))
    .catch(console.error);
