(function () {
  function sendToVimeo(command) {
    var iframe = document.querySelector('iframe');
    if (!iframe || !iframe.contentWindow) {
      return;
    }

    iframe.contentWindow.postMessage({ method: command }, '*');
  }

  window.addEventListener('message', function (event) {
    var data = event && event.data ? event.data : null;
    if (!data || !data.command) {
      return;
    }

    if (data.command === 'play') {
      sendToVimeo('play');
    }
    else if (data.command === 'pause') {
      sendToVimeo('pause');
    }
    else if (data.command === 'stop') {
      // Vimeo has no explicit stop method; pause is the safest fallback.
      sendToVimeo('pause');
    }
  });
})();
