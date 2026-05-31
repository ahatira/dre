(function () {
  function forwardCommand(command) {
    var iframe = document.querySelector('iframe');
    if (!iframe || !iframe.contentWindow) {
      return;
    }

    iframe.contentWindow.postMessage({
      source: 'bnp-media-parent',
      command: command
    }, '*');
  }

  window.addEventListener('message', function (event) {
    var data = event && event.data ? event.data : null;
    if (!data || data.source !== 'bnp-media-host') {
      return;
    }

    if (data.command === 'play' || data.command === 'pause' || data.command === 'stop') {
      forwardCommand(data.command);
    }
  });
})();
