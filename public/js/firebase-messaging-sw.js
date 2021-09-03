/*

Give the service worker access to Firebase Messaging.

Note that you can only use Firebase Messaging here, other Firebase libraries are not available in the service worker.

*/

importScripts('https://www.gstatic.com/firebasejs/8.6.2/firebase-app.js');

importScripts('https://www.gstatic.com/firebasejs/8.6.2/firebase-messaging.js');



/*

Initialize the Firebase app in the service worker by passing in the messagingSenderId.

* New configuration for app@pulseservice.com

*/

firebase.initializeApp({

        apiKey: "AIzaSyBrhfC7ZrhEyH_xNGXcR6HQUUGUVBNlnWw",
        authDomain: "interview-6168a.firebaseapp.com",
        databaseURL: "https://interview-6168a.firebaseio.com",
        projectId: "interview-6168a",
        storageBucket: "interview-6168a.appspot.com",
        messagingSenderId: "1028679469723",
        appId: "1:1028679469723:web:026d079d23d7505744943e"
        // measurementId: "XXX",

    });

  

/*

Retrieve an instance of Firebase Messaging so that it can handle background messages.

*/

const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) {

    console.log(

        "[firebase-messaging-sw.js] Received background message ",

        payload,

    );

    /* Customize notification here */

    const notificationTitle = "Background Message Title";

    const notificationOptions = {

        body: "Background Message body.",

        icon: "/itwonders-web-logo.png",

    };

  

    return self.registration.showNotification(

        notificationTitle,

        notificationOptions,

    );

});