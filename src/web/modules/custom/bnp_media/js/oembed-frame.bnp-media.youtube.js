(function () {
  function sendToYoutube(command) {
    var iframe = document.querySelector('iframe');
    if (!iframe || !iframe.contentWindow) {
      return;
    }

    var payload = {
      event: 'command',
      func: command,
      args: []
    };

    iframe.contentWindow.postMessage(JSON.stringify(payload), '*');
  }

  window.addEventListener('message', function (event) {
    var data = event && event.data ? event.data : null;
    if (!data || !data.command) {
      return;
    }

    if (data.command === 'play') {
      sendToYoutube('playVideo');
    }
    else if (data.command === 'pause') {
      sendToYoutube('pauseVideo');
    }
    else if (data.command === 'stop') {
      sendToYoutube('stopVideo');
    }
  });
})();
