(function () {
  function sendToMediaHub(command) {
    var iframe = document.querySelector('iframe');
    if (!iframe || !iframe.contentWindow) {
      return;
    }

    iframe.contentWindow.postMessage({
      source: 'bnp-media',
      command: command
    }, '*');
  }

  window.addEventListener('message', function (event) {
    var data = event && event.data ? event.data : null;
    if (!data || !data.command) {
      return;
    }

    if (data.command === 'play' || data.command === 'pause' || data.command === 'stop') {
      sendToMediaHub(data.command);
    }
  });
})();
