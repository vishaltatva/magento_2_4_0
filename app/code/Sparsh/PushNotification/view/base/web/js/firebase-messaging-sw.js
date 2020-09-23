importScripts('https://www.gstatic.com/firebasejs/4.9.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/4.9.0/firebase-messaging.js');

var baseUrl = new URL(location).searchParams.get('baseUrl');
var apiKey = new URL(location).searchParams.get('apiKey');
var authDomain = new URL(location).searchParams.get('authDomain');
var databaseURL = new URL(location).searchParams.get('databaseURL');
var projectId = new URL(location).searchParams.get('projectId');
var storageBucket = new URL(location).searchParams.get('storageBucket');
var messagingSenderId = new URL(location).searchParams.get('messagingSenderId');

firebase.initializeApp({
	apiKey: apiKey,
    authDomain: authDomain,
    databaseURL: databaseURL,
    projectId: projectId,
    storageBucket: storageBucket,
    messagingSenderId: messagingSenderId
});

const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {  
  const title = payload.data.title;
  const options = {
      body: payload.data.body,
      icon: payload.data.icon,
      data: { 
        url:payload.data.click_action,
        id: payload.data.templateId,
        utm:payload.data.utm_parameters
      }      
  };
  return self.registration.showNotification(title,options);
});

self.addEventListener('notificationclick', (event) => {
    var url = baseUrl+"/push_notification/index/FireNotification?id="+event.notification.data.id;
    event.waitUntil(
        fetch(url)
        .then(function(response) {
        })
    );    
    if (event.notification.data.url) {
      if(event.notification.data.utm){
        clients.openWindow(event.notification.data.url+"?"+event.notification.data.utm);
      }else{
        clients.openWindow(event.notification.data.url);
      }
    }
    event.notification.close();
}); 